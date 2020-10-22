<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/pforum.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Pforum\Controller;

use JWeiland\Pforum\Configuration\ExtConf;
use JWeiland\Pforum\Domain\Model\Topic;
use JWeiland\Pforum\Domain\Repository\AnonymousUserRepository;
use JWeiland\Pforum\Domain\Repository\ForumRepository;
use JWeiland\Pforum\Domain\Repository\FrontendUserRepository;
use JWeiland\Pforum\Domain\Repository\PostRepository;
use JWeiland\Pforum\Domain\Repository\TopicRepository;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;
use TYPO3\CMS\Extbase\Persistence\Generic\Session;

/**
 * Abstract class with useful methods for all other extending classes
 */
class AbstractController extends ActionController
{
    /**
     * @var ExtConf
     */
    protected $extConf;

    /**
     * @var Session
     */
    protected $session;

    /**
     * @var ForumRepository
     */
    protected $forumRepository;

    /**
     * @var TopicRepository
     */
    protected $topicRepository;

    /**
     * @var PostRepository
     */
    protected $postRepository;

    /**
     * @var AnonymousUserRepository
     */
    protected $anonymousUserRepository;

    /**
     * @var FrontendUserRepository
     */
    protected $frontendUserRepository;

    /**
     * @var PersistenceManager
     */
    protected $persistenceManager;

    public function injectExtConf(ExtConf $extConf): void
    {
        $this->extConf = $extConf;
    }

    public function injectSession(Session $session): void
    {
        $this->session = $session;
    }

    public function injectForumRepository(ForumRepository $forumRepository): void
    {
        $this->forumRepository = $forumRepository;
    }

    public function injectTopicRepository(TopicRepository $topicRepository): void
    {
        $this->topicRepository = $topicRepository;
    }

    public function injectPostRepository(PostRepository $postRepository): void
    {
        $this->postRepository = $postRepository;
    }

    public function injectAnonymousUserRepository(AnonymousUserRepository $anonymousUserRepository): void
    {
        $this->anonymousUserRepository = $anonymousUserRepository;
    }

    public function injectFrontendUserRepository(FrontendUserRepository $frontendUserRepository): void
    {
        $this->frontendUserRepository = $frontendUserRepository;
    }

    public function injectPersistenceManager(PersistenceManager $persistenceManager): void
    {
        $this->persistenceManager = $persistenceManager;
    }

    public function injectConfigurationManager(ConfigurationManagerInterface $configurationManager): void
    {
        $this->configurationManager = $configurationManager;
        $tsSettings = $this->configurationManager->getConfiguration(
           ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS, 'pforum', 'doNotLoadFlexFormSettings'
        );
        $mergedSettings = $this->configurationManager->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS
        );
        if (is_array($mergedSettings)) {
            foreach ($mergedSettings as $key => $value) {
                if (!is_array($value) && empty($value)) {
                    $mergedSettings[$key] = $tsSettings[$key];
                }
            }
        }
        $this->settings = $mergedSettings;
    }

    public function initializeAction(): void
    {
        // if this value was not set, then it will be filled with 0
        // but that is not good, because UriBuilder accepts 0 as pid, so it's better to set it to NULL
        if (empty($this->settings['pidOfDetailPage'])) {
            $this->settings['pidOfDetailPage'] = null;
        }
        $this->checkForMisconfiguration();
    }

    /**
     * If there is a misconfiguration in TS this will throw an Exception.
     */
    protected function checkForMisconfiguration(): void
    {
        if (
            $this->settings['topic']['hideAtCreation'] &&
            empty($this->settings['topic']['activateByAdmin']) &&
            empty($this->settings['emailIsMandatory'])
        ) {
            throw new \Exception(
                'You can\'t hide topics at creation, deactivate admin activation and mark email as NOT mandatory.' .
                'This would produce hidden records which will never be visible',
                1378371532
            );
        }
        if (
            $this->settings['post']['hideAtCreation'] &&
            empty($this->settings['post']['activateByAdmin']) &&
            empty($this->settings['emailIsMandatory'])
        ) {
            throw new \Exception(
                'You can\'t hide posts at creation, deactivate admin activation and mark email ' .
                'as NOT mandatory. This would produce hidden records which will never be visible',
                1378371541
            );
        }
    }

    /**
     * files will be uploaded in typeConverter automatically
     * But, if an error occurs we have to remove them.
     *
     * @param string $argument
     */
    protected function deleteUploadedFilesOnValidationErrors(string $argument): void
    {
        if ($this->getControllerContext()->getRequest()->hasArgument($argument)) {
            /** @var Topic $topic */
            $topic = $this->getControllerContext()->getRequest()->getArgument($argument);
            $images = $topic->getImages();
            if (count($images)) {
                /** @var \TYPO3\CMS\Extbase\Domain\Model\FileReference $image */
                foreach ($images as $image) {
                    $image->getOriginalResource()->getOriginalFile()->delete();
                }
            }
        }
    }
}
