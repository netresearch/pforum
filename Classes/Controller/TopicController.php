<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/pforum.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Pforum\Controller;

use JWeiland\Pforum\Domain\Model\Forum;
use JWeiland\Pforum\Domain\Model\Topic;
use JWeiland\Pforum\Helper\FrontendGroupHelper;
use JWeiland\Pforum\Property\TypeConverter\UploadMultipleFilesConverter;
use TYPO3\CMS\Core\Mail\MailMessage;
use TYPO3\CMS\Core\Messaging\AbstractMessage;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Annotation as Extbase;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;

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

        $this->view->assign('topic', $topic);
        $this->view->assign('posts', $posts);
    }

    public function newAction(Forum $forum): void
    {
        $this->deleteUploadedFilesOnValidationErrors('newTopic');
        $this->view->assign('forum', $forum);
        $this->view->assign('newTopic', GeneralUtility::makeInstance(Topic::class));
    }

    /**
     * Set typeConverter for images
     */
    public function initializeCreateAction(): void
    {
        if ($this->settings['useImages']) {
            $multipleFilesTypeConverter = GeneralUtility::makeInstance(UploadMultipleFilesConverter::class);
            $this->arguments->getArgument('newTopic')
                ->getPropertyMappingConfiguration()
                ->forProperty('images')
                ->setTypeConverter($multipleFilesTypeConverter);
        }
    }

    public function createAction(Forum $forum, Topic $newTopic): void
    {
        // if auth = frontend user
        if ((int)$this->settings['auth'] === 2) {
            $this->addFeUserToTopic($forum, $newTopic);
        }

        $forum->addTopic($newTopic);
        $this->forumRepository->update($forum);

        // if a preview was requested direct to preview action
        if ($this->controllerContext->getRequest()->hasArgument('preview')) {
            $newTopic->setHidden(true); // topic should not be visible while previewing
            $this->persistenceManager->persistAll(); // we need an uid before redirecting
            $this->redirect(
                'edit',
                'Topic',
                'Pforum',
                ['topic' => $newTopic, 'isPreview' => true, 'isNew' => true]
            );
        }

        if ($this->settings['topic']['hideAtCreation']) {
            $newTopic->setHidden(true);
        }

        // if auth = anonymous user
        // send a mail to the user to activate, edit or delete his entry
        if (((int)$this->settings['auth'] === 1) && $this->settings['emailIsMandatory']) {
            $this->persistenceManager->persistAll(); // we need an uid before mailing
            $this->mailToUser($newTopic);
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
        $this->view->assign('topic', $topic);
        $this->view->assign('isPreview', $isPreview);
        $this->view->assign('isNew', $isNew);
    }

    /**
     * getObjectByIdentifier can only find non-hidden values
     * With this method we help extbase backend to find our hidden object.
     */
    public function initializeUpdateAction(): void
    {
        $this->registerTopicFromRequest('topic');
        $argument = $this->request->getArgument('topic');
        /** @var Topic $topic */
        $topic = $this->topicRepository->findByIdentifier($argument['__identity']);
        if ($this->settings['useImages']) {
            $multipleFilesTypeConverter = GeneralUtility::makeInstance(UploadMultipleFilesConverter::class);
            $this->arguments->getArgument('topic')
                ->getPropertyMappingConfiguration()
                ->forProperty('images')
                ->setTypeConverter($multipleFilesTypeConverter)
                ->setTypeConverterOptions(
                    UploadMultipleFilesConverter::class,
                    [
                        'IMAGES' => $topic->getImages()
                    ]
                );
        }
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

                // if auth = anonymous user
                // send a mail to the user to activate, edit or delete his entry
                if (((int)$this->settings['auth'] === 1) && $this->settings['emailIsMandatory']) {
                    $this->mailToUser($topic);
                }

                $this->addFlashMessageForCreation();
            } else {
                // edited topics which are not new are visible
                $topic->setHidden(false);
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
            $topic = $this->topicRepository->findHiddenEntryByUid((int)$argument['__identity']);
        } else {
            // get topic from UID
            $topic = $this->topicRepository->findHiddenEntryByUid((int)$argument);
        }
        $this->session->registerObject($topic, $topic->getUid());
    }

    protected function addFeUserToTopic(Forum $forum, Topic $newTopic): void
    {
        if (is_array($GLOBALS['TSFE']->fe_user->user) && $GLOBALS['TSFE']->fe_user->user['uid']) {
            $user = $this->frontendUserRepository->findByUid(
                (int)$GLOBALS['TSFE']->fe_user->user['uid']
            );
            $newTopic->setFrontendUser($user);
        } else {
            /* normally this should never be called, because the link to create a new entry was not displayed if user was not authenticated */
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
        $mail = $this->objectManager->get(MailMessage::class);
        $mail->setFrom($this->extConf->getEmailFromAddress(), $this->extConf->getEmailFromName());
        $mail->setTo($topic->getUser()->getEmail(), $topic->getUser()->getName());
        $mail->setSubject(
            LocalizationUtility::translate(
                'email.topic.subject',
                'pforum'
            )
        );
        $mail->html($this->getContentForMailing($topic));

        $mail->send();
    }

    protected function getContentForMailing(Topic $topic): string
    {
        $view = GeneralUtility::makeInstance(StandaloneView::class);
        $view->setTemplatePathAndFilename(
            sprintf(
                '%s%s',
                ExtensionManagementUtility::extPath('pforum'),
                'Resources/Private/Templates/Mail/ConfigureTopic.html'
            )
        );
        $view->setControllerContext($this->getControllerContext());
        $view->assign('settings', $this->settings);
        $view->assign('topic', $topic);

        return $view->render();
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
