<?php

/**
 * This file is part of the package netresearch/pforum.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

namespace JWeiland\Pforum\EventListener;

use JWeiland\Pforum\Domain\Model\Post;
use JWeiland\Pforum\Domain\Repository\PostRepository;
use JWeiland\Pforum\Event\PreProcessControllerActionEvent;
use JWeiland\Pforum\Property\TypeConverter\UploadMultipleFilesConverter;
use TYPO3\CMS\Extbase\Mvc\Controller\MvcPropertyMappingConfiguration;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\CMS\Extbase\Property\PropertyMappingConfiguration;

class AssignMediaTypeConverterForPostEventListener extends AbstractControllerEventListener
{
    /**
     * @var PostRepository
     */
    protected $postRepository;

    /**
     * @var UploadMultipleFilesConverter
     */
    protected $uploadMultipleFilesConverter;

    protected $allowedControllerActions = [
        'Post' => [
            'create',
            'update',
        ],
    ];

    public function __construct(
        PostRepository $postRepository,
        UploadMultipleFilesConverter $uploadMultipleFilesConverter,
    ) {
        $this->postRepository               = $postRepository;
        $this->uploadMultipleFilesConverter = $uploadMultipleFilesConverter;
    }

    public function __invoke(PreProcessControllerActionEvent $controllerActionEvent): void
    {
        if ($this->isValidRequest($controllerActionEvent)) {
            if ($controllerActionEvent->getActionName() === 'create') {
                $this->assignTypeConverterForCreateAction($controllerActionEvent);
            } else {
                $this->assignTypeConverterForUpdateAction($controllerActionEvent);
            }
        }
    }

    protected function assignTypeConverterForCreateAction(PreProcessControllerActionEvent $controllerActionEvent): void
    {
        $this->setTypeConverterForProperty('images', null, $controllerActionEvent);
    }

    protected function assignTypeConverterForUpdateAction(PreProcessControllerActionEvent $controllerActionEvent): void
    {
        // Needed to get the previously stored images
        $persistedPost = $this->postRepository->findHiddenObject(
            (int) $controllerActionEvent->getRequest()->getArgument('post')['__identity']
        );

        if ($persistedPost instanceof Post) {
            $this->setTypeConverterForProperty(
                'images',
                $persistedPost->getOriginalImages(),
                $controllerActionEvent
            );
        }
    }

    protected function setTypeConverterForProperty(
        string $property,
        ?ObjectStorage $persistedFiles,
        PreProcessControllerActionEvent $controllerActionEvent,
    ): void {
        $propertyMappingConfiguration = $this->getPropertyMappingConfigurationForEvent($controllerActionEvent)
            ->forProperty($property)
            ->setTypeConverter($this->uploadMultipleFilesConverter);

        // Do not use setTypeConverterOptions() as this will remove all existing options
        $this->addOptionToUploadFilesConverter(
            $propertyMappingConfiguration,
            'settings',
            $controllerActionEvent->getSettings()
        );

        if ($persistedFiles !== null) {
            $this->addOptionToUploadFilesConverter(
                $propertyMappingConfiguration,
                'IMAGES',
                $persistedFiles
            );
        }
    }

    protected function getPropertyMappingConfigurationForEvent(
        PreProcessControllerActionEvent $controllerActionEvent,
    ): MvcPropertyMappingConfiguration {
        return $controllerActionEvent->getArguments()
            ->getArgument('post')
            ->getPropertyMappingConfiguration();
    }

    protected function addOptionToUploadFilesConverter(
        PropertyMappingConfiguration $propertyMappingConfiguration,
        string $optionKey,
        $optionValue,
    ): void {
        $propertyMappingConfiguration->setTypeConverterOption(
            UploadMultipleFilesConverter::class,
            $optionKey,
            $optionValue
        );
    }
}
