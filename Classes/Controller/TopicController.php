<?php

/**
 * This file is part of the package jweiland/pforum.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

namespace JWeiland\Pforum\Controller;

use JWeiland\Pforum\Domain\Model\Forum;
use JWeiland\Pforum\Domain\Model\Topic;
use JWeiland\Pforum\Event\AfterTopicCreateEvent;
use JWeiland\Pforum\Helper\FrontendGroupHelper;
use Symfony\Component\Mime\Address;
use TYPO3\CMS\Core\Mail\FluidEmail;
use TYPO3\CMS\Core\Mail\Mailer;
use TYPO3\CMS\Core\Messaging\AbstractMessage;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Annotation as Extbase;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * Controller to list and show topics of forum
 */
class TopicController extends AbstractController
{
    /**
     * @var FrontendGroupHelper
     */
    protected $frontendGroupHelper;

    public function injectFrontendGroupHelper(FrontendGroupHelper $frontendGroupHelper): void
    {
        $this->frontendGroupHelper = $frontendGroupHelper;
    }

    public function showAction(Topic $topic): void
    {
        $posts = $this->postRepository->findByTopic($topic);
        if ($this->frontendGroupHelper->uidExistsInGroupData((int)($this->settings['uidOfAdminGroup'] ?? 0))) {
            $posts->getQuery()
                ->getQuerySettings()
                ->setIgnoreEnableFields(true)
                ->setEnableFieldsToBeIgnored(['disabled']);
        }

        $this->postProcessAndAssignFluidVariables([
            'topic' => $topic,
            'posts' => $posts,
        ]);
    }

    public function newAction(Forum $forum): void
    {
        if (!$this->accessCheck()) {
            $this->redirect('list', 'Forum', 'Pforum');
        }

        $this->deleteUploadedFilesOnValidationErrors('topic');

        $this->postProcessAndAssignFluidVariables([
            'forum' => $forum,
            'topic' => GeneralUtility::makeInstance(Topic::class),
        ]);
    }

    /**
     * Set typeConverter for images
     */
    public function initializeCreateAction(): void
    {
        $this->preProcessControllerAction();
    }

    public function createAction(Forum $forum, Topic $topic): void
    {
        // if auth = frontend user
        if ((int)$this->settings['auth'] === 2) {
            $this->addFeUserToTopic($forum, $topic);
        }

        $forum->addTopic($topic);

        $this->forumRepository->update($forum);
        $this->persistenceManager->persistAll();

        // Dispatch event to allow handling after a new topic was created
        $this->eventDispatcher->dispatch(
            new AfterTopicCreateEvent(
                $this->request,
                $forum,
                $topic,
                $this->settings
            )
        );

        // if a preview was requested direct to preview action
        if ($this->controllerContext->getRequest()->hasArgument('preview')) {
            $topic->setHidden(true); // topic should not be visible while previewing
            $this->forumRepository->update($forum);
            $this->persistenceManager->persistAll(); // we need an uid before redirecting
            $this->redirect(
                'edit',
                'Topic',
                'Pforum',
                ['topic' => $topic, 'isPreview' => true, 'isNew' => true]
            );
        }

        if ($this->settings['topic']['hideAtCreation']) {
            $topic->setHidden(true);
            $this->forumRepository->update($forum);
            $this->persistenceManager->persistAll();
        }

        // if auth = anonymous user
        // send a mail to the user to activate, edit or delete his entry
        if (((int)$this->settings['auth'] === 1) && $this->settings['emailIsMandatory']) {
            $this->persistenceManager->persistAll(); // we need an uid before mailing
            $this->mailToUser($topic);
        }

        $this->addFlashMessageForCreation();
        $this->redirect('show', 'Forum', 'Pforum', ['forum' => $forum]);
    }

    /**
     * Hidden record throws an exception.
     * That's why I check it here before calling editAction.
     */
    public function initializeEditAction(): void
    {
        $this->preProcessControllerAction();

        $this->registerTopicFromRequest('topic');
    }

    /**
     * @param bool $isPreview If is preview there will be an additional output above edit form
     * @param bool $isNew We need the information if updateAction was called from createAction.
     *                    If so we have to passthrough this information
     * @Extbase\IgnoreValidation("topic")
     */
    public function editAction(
        Topic $topic = null,
        bool $isPreview = false,
        bool $isNew = false
    ): void {
        $this->postProcessAndAssignFluidVariables([
            'topic' => $topic,
            'isPreview' => $isPreview,
            'isNew' => $isNew,
        ]);
    }

    /**
     * getObjectByIdentifier can only find non-hidden values
     * With this method we help extbase backend to find our hidden object.
     */
    public function initializeUpdateAction(): void
    {
        $this->preProcessControllerAction();

        $this->registerTopicFromRequest('topic');
    }

    /**
     * @param bool $isNew We need the information if updateAction was called from createAction.
     *                    If so we have to add different messages
     */
    public function updateAction(Topic $topic, bool $isNew = false): void
    {
        $this->topicRepository->update($topic);

        // if a preview was requested direct to preview action
        if ($this->controllerContext->getRequest()->hasArgument('preview')) {
            $topic->setHidden(true);
            $this->redirect(
                'edit',
                'Topic',
                'Pforum',
                ['topic' => $topic, 'isPreview' => true, 'isNew' => $isNew]
            );
        } else {
            if ($isNew) {
                // if is new and preview was pressed we have to check for visibility again
                if ($this->settings['topic']['hideAtCreation']) {
                    $topic->setHidden(true);
                } else {
                    $topic->setHidden(false);
                }

                $this->topicRepository->update($topic);
                $this->persistenceManager->persistAll();

                // if auth = anonymous user
                // send a mail to the user to activate, edit or delete his entry
                if (((int)$this->settings['auth'] === 1) && $this->settings['emailIsMandatory']) {
                    $this->mailToUser($topic);
                }

                $this->addFlashMessageForCreation();
            } else {
                // edited topics which are not new are visible
                $topic->setHidden(false);
                $this->topicRepository->update($topic);
                $this->persistenceManager->persistAll();

                $this->addFlashMessage(LocalizationUtility::translate('topicUpdated', 'pforum'));
            }

            $this->redirect('show', 'Forum', 'Pforum', ['forum' => $topic->getForum()]);
        }
    }

    /**
     * Hidden record throws an exception.
     * That's why I check it here before calling deleteAction.
     */
    public function initializeDeleteAction(): void
    {
        $this->preProcessControllerAction();

        $this->registerTopicFromRequest('topic');
    }

    public function deleteAction(Topic $topic): void
    {
        $this->topicRepository->remove($topic);
        $this->addFlashMessage(LocalizationUtility::translate('topicDeleted', 'pforum'));
        $this->redirect('list', 'Forum', 'Pforum');
    }

    /**
     * Hidden record throws an exception.
     * That's why I check it here before calling activateAction.
     */
    public function initializeActivateAction(): void
    {
        $this->preProcessControllerAction();

        $this->registerTopicFromRequest('topic');
    }

    /**
     * We need this extra action, because hidden entries can't be found in FE mode.
     */
    public function activateAction(Topic $topic): void
    {
        $topic->setHidden(false);
        $this->topicRepository->update($topic);
        $this->addFlashMessage(LocalizationUtility::translate('topicActivated', 'pforum'));
        $this->redirect('list', 'Forum', 'Pforum');
    }

    /**
     * This is a workaround to help controller actions to find (hidden) topics.
     *
     * @param string $argumentName
     */
    protected function registerTopicFromRequest(string $argumentName): void
    {
        $argument = $this->request->getArgument($argumentName);
        if (is_array($argument)) {
            // get topic from form ($_POST)
            $topic = $this->topicRepository->findHiddenObject((int)$argument['__identity']);
        } else {
            // get topic from UID
            $topic = $this->topicRepository->findHiddenObject((int)$argument);
        }

        if ($topic instanceof Topic) {
            $this->session->registerObject($topic, $topic->getUid());
        }
    }

    protected function addFeUserToTopic(Forum $forum, Topic $topic): void
    {
        if (is_array($GLOBALS['TSFE']->fe_user->user) && $GLOBALS['TSFE']->fe_user->user['uid']) {
            $user = $this->frontendUserRepository->findByUid(
                (int)$GLOBALS['TSFE']->fe_user->user['uid']
            );
            $topic->setFrontendUser($user);
        } else {
            // normally this should never be called, because the link to create a new entry was not displayed if user was not authenticated
            $this->addFlashMessage(
                'You must be logged in before creating a topic',
                '',
                AbstractMessage::WARNING
            );
            $this->redirect('show', 'Forum', 'Pforum', ['forum' => $forum]);
        }
    }

    protected function mailToUser(Topic $topic): void
    {
        $email = GeneralUtility::makeInstance(FluidEmail::class);
        $email
            ->to(new Address($topic->getUser()->getEmail(), $topic->getUser()->getName()))
            ->from(new Address($this->extConf->getEmailFromAddress(), $this->extConf->getEmailFromName()))
            ->subject(LocalizationUtility::translate('email.topic.subject', 'pforum'))
            ->format('html')
            ->setTemplate('ConfigureTopic')
            ->assignMultiple([
                'settings' => $this->settings,
                'topic' => $topic,
            ]);
        GeneralUtility::makeInstance(Mailer::class)->send($email);
    }

    protected function addFlashMessageForCreation(): void
    {
        if ($this->settings['topic']['hideAtCreation']) {
            if ($this->settings['topic']['activateByAdmin']) {
                $this->addFlashMessage(
                    LocalizationUtility::translate('hiddenTopicCreatedAndActivateByAdmin', 'pforum')
                );
            } else {
                $this->addFlashMessage(
                    LocalizationUtility::translate('hiddenTopicCreatedAndActivateByUser', 'pforum')
                );
            }
        } else {
            // if topic is not hidden at creation there is no need to activate it by admin
            $this->addFlashMessage(
                LocalizationUtility::translate('topicCreated', 'pforum')
            );
        }
    }
}
