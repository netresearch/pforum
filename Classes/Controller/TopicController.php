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
use JWeiland\Pforum\Property\TypeConverter\UploadMultipleFilesConverter;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Extbase\Annotation as Extbase;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * Controller to list and show topics of forum
 */
class TopicController extends AbstractTopicController
{
    /**
     * @param Topic $topic
     */
    public function showAction(Topic $topic): void
    {
        /* @var QueryResultInterface $topics */
        $posts = $this->postRepository->findByTopic($topic);
        if (
            !empty($this->settings['uidOfAdminGroup']) &&
            is_array($GLOBALS['TSFE']->fe_user->groupData['uid']) &&
            in_array($this->settings['uidOfAdminGroup'], $GLOBALS['TSFE']->fe_user->groupData['uid'])
        ) {
            $posts->getQuery()
                ->getQuerySettings()
                ->setIgnoreEnableFields(true)
                ->setEnableFieldsToBeIgnored(['disabled']);
        }
        $this->view->assign('topic', $topic);
        $this->view->assign('posts', $posts);
    }

    /**
     * @param Forum $forum
     */
    public function newAction(Forum $forum): void
    {
        $this->deleteUploadedFilesOnValidationErrors('newTopic');
        $this->view->assign('forum', $forum);
        $this->view->assign('newTopic', $this->objectManager->get(Topic::class));
    }

    /**
     * Set typeConverter for images
     */
    public function initializeCreateAction(): void
    {
        if ($this->settings['useImages']) {
            $multipleFilesTypeConverter = $this->objectManager->get(UploadMultipleFilesConverter::class);
            $this->arguments->getArgument('newTopic')
                ->getPropertyMappingConfiguration()
                ->forProperty('images')
                ->setTypeConverter($multipleFilesTypeConverter);
        }
    }

    /**
     * @param Forum $forum
     * @param Topic $newTopic
     */
    public function createAction(Forum $forum, Topic $newTopic): void
    {
        // if auth = frontend user
        if ($this->settings['auth'] == 2) {
            $this->addFeUserToTopic($forum, $newTopic);
        }

        $forum->addTopic($newTopic);
        $this->forumRepository->update($forum);

        // if a preview was requested direct to preview action
        if ($this->controllerContext->getRequest()->hasArgument('preview')) {
            $newTopic->setHidden(true); // topic should not be visible while previewing
            $this->persistenceManager->persistAll(); // we need an uid before redirecting
            $this->redirect('edit', null, null, ['topic' => $newTopic, 'isPreview' => true, 'isNew' => true]);
        }

        if ($this->settings['topic']['hideAtCreation']) {
            $newTopic->setHidden(true);
        }

        // if auth = anonymous user
        if ($this->settings['auth'] == 1) {
            /* send a mail to the user to activate, edit or delete his entry */
            if ($this->settings['emailIsMandatory']) {
                $this->persistenceManager->persistAll(); // we need an uid before mailing
                $this->mailToUser($newTopic);
            }
        }
        $this->addFlashMessageForCreation();
        $this->redirect('show', 'Forum', null, ['forum' => $forum]);
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
     * @param Topic $topic
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
            $multipleFilesTypeConverter = $this->objectManager->get(UploadMultipleFilesConverter::class);
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
     * @param Topic $topic
     * @param bool $isNew We need the information if updateAction was called from createAction.
     *                    If so we have to add different messages
     */
    public function updateAction(Topic $topic, bool $isNew = false): void
    {
        $this->topicRepository->update($topic);

        // if a preview was requested direct to preview action
        if ($this->controllerContext->getRequest()->hasArgument('preview')) {
            $topic->setHidden(true);
            $this->redirect('edit', null, null, ['topic' => $topic, 'isPreview' => true, 'isNew' => $isNew]);
        } else {
            if ($isNew) {
                // if is new and preview was pressed we have to check for visibility again
                if ($this->settings['topic']['hideAtCreation']) {
                    $topic->setHidden(true);
                } else {
                    $topic->setHidden(false);
                }

                /* if auth = anonymous user */
                if ($this->settings['auth'] == 1) {
                    /* send a mail to the user to activate, edit or delete his entry */
                    if ($this->settings['emailIsMandatory']) {
                        $this->mailToUser($topic);
                    }
                }

                $this->addFlashMessageForCreation();
            } else {
                // edited topics which are not new are visible
                $topic->setHidden(false);
                $this->addFlashMessage(LocalizationUtility::translate('topicUpdated', 'pforum'), '', FlashMessage::OK);
            }
            $this->redirect('show', 'Forum', '', ['forum' => $topic->getForum()]);
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

    /**
     * @param Topic $topic
     */
    public function deleteAction(Topic $topic): void
    {
        $this->topicRepository->remove($topic);
        $this->addFlashMessage(LocalizationUtility::translate('topicDeleted', 'pforum'), '', FlashMessage::OK);
        $this->redirect('list', 'Forum');
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
     *
     * @param Topic $topic
     */
    public function activateAction(Topic $topic): void
    {
        $topic->setHidden(false);
        $this->topicRepository->update($topic);
        $this->addFlashMessage(LocalizationUtility::translate('topicActivated', 'pforum'), '', FlashMessage::OK);
        $this->redirect('list', 'Forum');
    }
}
