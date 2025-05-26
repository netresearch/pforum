<?php

/**
 * This file is part of the package jweiland/pforum.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

namespace JWeiland\Pforum\Property\TypeConverter;

use Exception;
use JWeiland\Pforum\Event\PostCheckFileReferenceEvent;
use RuntimeException;
use TYPO3\CMS\Core\EventDispatcher\EventDispatcher;
use TYPO3\CMS\Core\Http\UploadedFile;
use TYPO3\CMS\Core\Resource\Enum\DuplicationBehavior;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Resource\FileReference as CoreFileReference;
use TYPO3\CMS\Core\Resource\Folder;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\StringUtility;
use TYPO3\CMS\Extbase\Domain\Model\FileReference;
use TYPO3\CMS\Extbase\Error\Error;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\CMS\Extbase\Property\PropertyMappingConfigurationInterface;
use TYPO3\CMS\Extbase\Property\TypeConverter\AbstractTypeConverter;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * A for PropertyMapper to convert multiple file uploads into an array.
 */
class UploadMultipleFilesConverter extends AbstractTypeConverter
{
    /**
     * @var Folder
     */
    private Folder $uploadFolder;

    /**
     * @var PropertyMappingConfigurationInterface
     */
    private PropertyMappingConfigurationInterface $converterConfiguration;

    /**
     * @var EventDispatcher
     */
    private EventDispatcher $eventDispatcher;

    /**
     * @var ResourceFactory
     */
    private ResourceFactory $resourceFactory;

    /**
     * Do not inject this property, as EXT:checkfaluploads may not be loaded.
     *
     * @var \JWeiland\Checkfaluploads\Service\FalUploadService
     */
    private \JWeiland\Checkfaluploads\Service\FalUploadService $falUploadService;

    /**
     *  Constructor.
     *
     * @param EventDispatcher $eventDispatcher
     * @param ResourceFactory $resourceFactory
     */
    public function __construct(
        EventDispatcher $eventDispatcher,
        ResourceFactory $resourceFactory,
    ) {
        $this->eventDispatcher = $eventDispatcher;
        $this->resourceFactory = $resourceFactory;
    }

    /**
     * @param mixed                                      $source
     * @param string                                     $targetType
     * @param array                                      $convertedChildProperties
     * @param PropertyMappingConfigurationInterface|null $configuration
     *
     * @return Error|ObjectStorage<FileReference>
     *
     * @throws Exception
     */
    public function convertFrom(
        $source,
        string $targetType,
        array $convertedChildProperties = [],
        ?PropertyMappingConfigurationInterface $configuration = null,
    ): Error|ObjectStorage {
        $this->initialize($configuration);

        $originalSource = $source;
        $references     = new ObjectStorage();

        foreach ($originalSource as $key => $uploadedFile) {
            if ($uploadedFile instanceof UploadedFile) {
                $uploadedFile = $this->convertUploadedFileToUploadInfoArray($uploadedFile);
            }

            $alreadyPersistedImage = $this->getAlreadyPersistedFileReferenceByPosition(
                $this->getAlreadyPersistedImages(),
                $key
            );

            // If no file was uploaded, use the already persisted one
            if (!$this->isValidUploadFile($uploadedFile)) {
                // TODO How is this triggered?
                //                if (isset($uploadedFile['delete']) && $uploadedFile['delete'] === '1') {
                //                    $this->deleteFile($alreadyPersistedImage);
                //                    unset($source[$key]);
                //                } elseif ($alreadyPersistedImage instanceof FileReference) {
                //                    $source[$key] = $alreadyPersistedImage;
                //                } else {
                //                    unset($source[$key]);
                //                }
                //
                continue;
            }

            // Check if the uploaded file returns an error
            if ($uploadedFile['error'] !== UPLOAD_ERR_OK) {
                return GeneralUtility::makeInstance(
                    Error::class,
                    LocalizationUtility::translate(
                        'error.upload',
                        'pforum'
                    ) . $uploadedFile['error'],
                    1396957314
                );
            }

            // Check if file extension is allowed
            $fileParts = GeneralUtility::split_fileref($uploadedFile['name']);

            if (!GeneralUtility::inList($GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext'], $fileParts['fileext'])) {
                return new Error(
                    LocalizationUtility::translate(
                        'error.fileExtension',
                        'pforum',
                        [
                            $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext'],
                        ]
                    ),
                    1402981282
                );
            }

            if (
                ExtensionManagementUtility::isLoaded('checkfaluploads')
                && $error = $this->getFalUploadService()->checkFile($uploadedFile)
            ) {
                return $error;
            }

            $this->eventDispatcher->dispatch(
                new PostCheckFileReferenceEvent(
                    $source,
                    $key,
                    $alreadyPersistedImage,
                    $uploadedFile
                )
            );

            try {
                $resource = $this->importUploadedResource($uploadedFile);
            } catch (Exception $exception) {
                return GeneralUtility::makeInstance(
                    Error::class,
                    $exception->getMessage(),
                    $exception->getCode()
                );
            }

            $references->attach($resource);
        }

        return $references;
    }

    /**
     * @param PropertyMappingConfigurationInterface|null $configuration
     *
     * @return void
     *
     * @throws RuntimeException
     */
    private function initialize(?PropertyMappingConfigurationInterface $configuration): void
    {
        if (!($configuration instanceof PropertyMappingConfigurationInterface)) {
            throw new RuntimeException(
                'Missing PropertyMapper configuration in UploadMultipleFilesConverter',
                1666698966
            );
        }

        $this->converterConfiguration = $configuration;

        // Set up the upload folder
        $uploadFolderId = $this->getTypoScriptPluginSettings()['new']['uploadFolder'] ?? '';

        if ($uploadFolderId === '') {
            throw new RuntimeException(
                'You have forgotten to set an Upload Folder in TypoScript for pforum',
                1666698952
            );
        }

        $this->uploadFolder = $this->provideUploadFolder($uploadFolderId);
    }

    /**
     * @return array<string, string|string[]>
     */
    private function getTypoScriptPluginSettings(): array
    {
        /** @var array<string, string|string[]>|null $settings */
        $settings = $this->converterConfiguration
            ->getConfigurationValue(
                self::class,
                'settings'
            );

        return $settings ?? [];
    }

    /**
     * @param UploadedFile $uploadedFile
     *
     * @return array<string, int|string|null>
     */
    private function convertUploadedFileToUploadInfoArray(UploadedFile $uploadedFile): array
    {
        return [
            'name'     => $uploadedFile->getClientFilename(),
            'tmp_name' => $uploadedFile->getTemporaryFileName(),
            'size'     => $uploadedFile->getSize(),
            'error'    => $uploadedFile->getError(),
            'type'     => $uploadedFile->getClientMediaType(),
        ];
    }

    /**
     * Check if we have a valid uploaded file
     * Error = 4: No file uploaded.
     *
     * @param array<string, int|string|null> $uploadedFile
     *
     * @return bool
     */
    private function isValidUploadFile(array $uploadedFile): bool
    {
        if ($uploadedFile['error'] === UPLOAD_ERR_NO_FILE) {
            return false;
        }

        return isset(
            $uploadedFile['name'],
            $uploadedFile['tmp_name'],
            $uploadedFile['size'],
            $uploadedFile['error'],
            $uploadedFile['type']
        );
    }

    /**
     * Ensures that the upload folder exists, creates it if it does not.
     */
    private function provideUploadFolder(string $uploadFolderIdentifier): Folder
    {
        try {
            return $this->resourceFactory->getFolderObjectFromCombinedIdentifier($uploadFolderIdentifier);
        } catch (Exception) {
            [$storageId, $storagePath] = explode(':', $uploadFolderIdentifier, 2);
            $storage                   = $this->resourceFactory->getStorageObject((int) $storageId);

            $uploadFolder = $this->provideTargetFolder($storage->getRootLevelFolder(), $storagePath);

            $this->provideFolderInitialization($uploadFolder);

            return $uploadFolder;
        }
    }

    /**
     * Ensures that a particular target folder exists, creates it if it does not.
     */
    private function provideTargetFolder(Folder $parentFolder, string $folderName): Folder
    {
        return $parentFolder->hasFolder($folderName)
            ? $parentFolder->getSubfolder($folderName)
            : $parentFolder->createFolder($folderName);
    }

    /**
     * Creates an empty index.html file to avoid directory indexing, in case it does not exist yet.
     */
    private function provideFolderInitialization(Folder $parentFolder): void
    {
        if (!$parentFolder->hasFile('index.html')) {
            $parentFolder->createFile('index.html');
        }
    }

    /**
     * Import a resource and respect configuration given for properties.
     *
     * @param array<string, mixed> $uploadInfo
     *
     * @return FileReference
     */
    private function importUploadedResource(array $uploadInfo): FileReference
    {
        /** @var File $uploadedFile */
        $uploadedFile = $this->uploadFolder
            ->addUploadedFile(
                $uploadInfo,
                DuplicationBehavior::RENAME
            );

        return $this->createFileReferenceFromFalFileObject($uploadedFile);
    }

    /**
     * Upload the file and get a file reference object.
     *
     * @param File $file
     *
     * @return FileReference
     */
    private function createFileReferenceFromFalFileObject(File $file): FileReference
    {
        $fileReference = $this->resourceFactory->createFileReferenceObject(
            [
                'uid_local'   => $file->getUid(),
                'uid_foreign' => StringUtility::getUniqueId('NEW_'),
                'uid'         => StringUtility::getUniqueId('NEW_'),
                'crop'        => null,
            ]
        );

        return $this->createFileReferenceFromFalFileReferenceObject($fileReference);
    }

    /**
     * In case no $resourcePointer is given a new file reference domain object
     * will be returned. Otherwise, the file reference is reconstituted from
     * storage and will be updated(!) with the provided $falFileReference.
     *
     * @param CoreFileReference $falFileReference
     *
     * @return FileReference
     */
    private function createFileReferenceFromFalFileReferenceObject(
        CoreFileReference $falFileReference,
    ): FileReference {
        $fileReference = GeneralUtility::makeInstance(FileReference::class);
        $fileReference->setOriginalResource($falFileReference);

        return $fileReference;
    }

    /**
     * @return ObjectStorage<FileReference>
     */
    private function getAlreadyPersistedImages(): ObjectStorage
    {
        $alreadyPersistedImages = $this->converterConfiguration->getConfigurationValue(
            self::class,
            'IMAGES'
        );

        return $alreadyPersistedImages instanceof ObjectStorage ? $alreadyPersistedImages : new ObjectStorage();
    }

    /**
     * @param ObjectStorage<FileReference> $alreadyPersistedFileReferences
     * @param int                          $position
     *
     * @return FileReference|null
     */
    private function getAlreadyPersistedFileReferenceByPosition(
        ObjectStorage $alreadyPersistedFileReferences,
        int $position,
    ): ?FileReference {
        return $alreadyPersistedFileReferences->toArray()[$position] ?? null;
    }

    /**
     * If a file is in our own upload folder, we can delete it from the filesystem and sys_file table.
     *
     * @param FileReference|null $fileReference
     *
     * @return void
     */
    private function deleteFile(?FileReference $fileReference): void
    {
        if ($fileReference instanceof FileReference) {
            $fileReference = $fileReference->getOriginalResource();

            if ($fileReference->getStorage()->isWithinFolder($this->uploadFolder, $fileReference)) {
                try {
                    $fileReference->getOriginalFile()->delete();
                } catch (Exception) {
                    // Do nothing. File already deleted or not found
                }
            }
        }
    }

    /**
     * @return \JWeiland\Checkfaluploads\Service\FalUploadService
     */
    private function getFalUploadService(): \JWeiland\Checkfaluploads\Service\FalUploadService
    {
        if ($this->falUploadService === null) {
            $this->falUploadService = GeneralUtility::makeInstance(
                \JWeiland\Checkfaluploads\Service\FalUploadService::class
            );
        }

        return $this->falUploadService;
    }
}
