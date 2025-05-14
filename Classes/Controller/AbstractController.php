<?php

/**
 * This file is part of the package netresearch/pforum.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

namespace JWeiland\Pforum\Controller;

use JWeiland\Pforum\Configuration\ExtConf;
use JWeiland\Pforum\Domain\Model\Topic;
use JWeiland\Pforum\Domain\Repository\AnonymousUserRepository;
use JWeiland\Pforum\Domain\Repository\ForumRepository;
use JWeiland\Pforum\Domain\Repository\FrontendUserRepository;
use JWeiland\Pforum\Domain\Repository\PostRepository;
use JWeiland\Pforum\Domain\Repository\TopicRepository;
use JWeiland\Pforum\Event\PostProcessFluidVariablesEvent;
use JWeiland\Pforum\Event\PreProcessControllerActionEvent;
use JWeiland\Pforum\Service\FrontendUserAccessService;
use RuntimeException;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;
use TYPO3\CMS\Extbase\Persistence\Generic\Session;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

/**
 * Abstract class with useful methods for all other extending classes.
 */
class AbstractController extends ActionController
{
    /**
     * @var PersistenceManager
     */
    protected PersistenceManager $persistenceManager;

    /**
     * @var ExtConf
     */
    protected ExtConf $extConf;

    /**
     * @var Session
     */
    protected Session $session;

    /**
     * @var ForumRepository
     */
    protected ForumRepository $forumRepository;

    /**
     * @var TopicRepository
     */
    protected TopicRepository $topicRepository;

    /**
     * @var PostRepository
     */
    protected PostRepository $postRepository;

    /**
     * @var AnonymousUserRepository
     */
    protected AnonymousUserRepository $anonymousUserRepository;

    /**
     * @var FrontendUserRepository
     */
    protected FrontendUserRepository $frontendUserRepository;

    /**
     * @var FrontendUserAccessService
     */
    protected FrontendUserAccessService $frontendUserAccessService;

    /**
     * Constructor.
     *
     * @param PersistenceManager            $persistenceManager
     * @param FrontendUserAccessService     $frontendUserAccessService
     * @param ExtConf                       $extConf
     * @param Session                       $session
     * @param ForumRepository               $forumRepository
     * @param TopicRepository               $topicRepository
     * @param PostRepository                $postRepository
     * @param AnonymousUserRepository       $anonymousUserRepository
     * @param FrontendUserRepository        $frontendUserRepository
     */
    public function __construct(
        PersistenceManager $persistenceManager,
        FrontendUserAccessService $frontendUserAccessService,
        ExtConf $extConf,
        Session $session,
        ForumRepository $forumRepository,
        TopicRepository $topicRepository,
        PostRepository $postRepository,
        AnonymousUserRepository $anonymousUserRepository,
        FrontendUserRepository $frontendUserRepository,
    ) {
        $this->persistenceManager = $persistenceManager;
        $this->frontendUserAccessService = $frontendUserAccessService;
        $this->extConf = $extConf;
        $this->session = $session;
        $this->forumRepository = $forumRepository;
        $this->topicRepository = $topicRepository;
        $this->postRepository = $postRepository;
        $this->anonymousUserRepository = $anonymousUserRepository;
        $this->frontendUserRepository = $frontendUserRepository;
    }

    /**
     * @return bool
     */
    protected function accessCheck(): bool
    {
        return $this->frontendUserAccessService
            ->accessCheck(
                (int) $this->settings['auth'],
                (int) $this->settings['uidOfUserGroup']
            );
    }

    protected function initializeAction(): void
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
            $this->settings['topic']['hideAtCreation']
            && empty($this->settings['topic']['activateByAdmin'])
            && empty($this->settings['emailIsMandatory'])
        ) {
            throw new RuntimeException(
                "You can't hide topics at creation, deactivate admin activation and mark email as NOT mandatory." .
                'This would produce hidden records which will never be visible',
                1378371532
            );
        }

        if (
            $this->settings['post']['hideAtCreation']
            && empty($this->settings['post']['activateByAdmin'])
            && empty($this->settings['emailIsMandatory'])
        ) {
            throw new RuntimeException(
                "You can't hide posts at creation, deactivate admin activation and mark email " .
                'as NOT mandatory. This would produce hidden records which will never be visible',
                1378371541
            );
        }
    }

    /**
     * files will be uploaded in typeConverter automatically
     * But, if an error occurs we have to remove them.
     */
    protected function deleteUploadedFilesOnValidationErrors(string $argument): void
    {
        if ($this->request->hasArgument($argument)) {
            /** @var Topic $topic */
            $topic  = $this->request->getArgument($argument);
            $images = $topic->getImages();
            foreach ($images as $image) {
                $image->getOriginalResource()->getOriginalFile()->delete();
            }
        }
    }

    protected function postProcessAndAssignFluidVariables(array $variables = []): void
    {
        /** @var PostProcessFluidVariablesEvent $event */
        $event = $this->eventDispatcher->dispatch(
            new PostProcessFluidVariablesEvent(
                $this->request,
                $this->settings,
                $variables
            )
        );

        $this->view->assignMultiple($event->getFluidVariables());
    }

    protected function preProcessControllerAction(): void
    {
        $this->eventDispatcher->dispatch(
            new PreProcessControllerActionEvent(
                $this->request,
                $this->arguments,
                $this->settings
            )
        );
    }
}
