<?php
namespace JWeiland\Pforum\Controller;

/*
 * This file is part of the package jweiland/pforum.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use JWeiland\Pforum\Domain\Model\Forum;
use JWeiland\Pforum\Domain\Model\Topic;
use JWeiland\Pforum\Property\TypeConverter\UploadMultipleFilesConverter;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * Class TopicController
 *
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class TopicController extends AbstractTopicController
{
    /**
     * action show.
     *
     * @param Topic $topic
     * @return void
     */
    public function showAction(Topic $topic)
    {
        /* @var \TYPO3\CMS\Extbase\Persistence\Generic\QueryResult $topics */
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
     * action new.
     *
     * @param Forum $forum
     * @return void
     */
    public function newAction(Forum $forum)
    {
        $this->deleteUploadedFilesOnValidationErrors('newTopic');
        $this->view->assign('forum', $forum);
        $this->view->assign('newTopic', $this->objectManager->get(Topic::class));
    }

    /**
     * set special typeConverter for images
     *
     * @return void
     */
    public function initializeCreateAction()
    {
        if ($this->settings['useImages']) {
            /** @var UploadMultipleFilesConverter $multipleFilesTypeConverter */
            $multipleFilesTypeConverter = $this->objectManager->get(UploadMultipleFilesConverter::class);
            $this->arguments->getArgument('newTopic')
                ->getPropertyMappingConfiguration()
                ->forProperty('images')
                ->setTypeConverter($multipleFilesTypeConverter);
        }
    }

    /**
     * action create.
     *
     * @param Forum $forum
     * @param Topic $newTopic
     * @return void
     */
    public function createAction(Forum $forum, Topic $newTopic)
    {
        /* if auth = frontend user */
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

        /* if auth = anonymous user */
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
     * initialize action edit
     * hidden record throws an exception. Thats why I check it here before calling editAction.
     *
     * @return void
     */
    public function initializeEditAction()
    {
        $this->registerTopicFromRequest('topic');
    }

    /**
     * action edit.
     *
     * @param Topic $topic
     * @param bool $isPreview If is preview there will be an additional output above edit form
     * @param bool $isNew We need the information if updateAction was called from createAction.
     *                    If so we have to passthrough this information
     * @dontvalidate $topic
     * @return void
     */
    public function editAction(Topic $topic = null, $isPreview = false, $isNew = false)
    {
        $this->view->assign('topic', $topic);
        $this->view->assign('isPreview', $isPreview);
        $this->view->assign('isNew', $isNew);
    }

    /**
     * getObjectByIdentifier can only find non-hidden values
     * With this method we help extbase backend to find our hidden object.
     *
     * @return void
     */
    public function initializeUpdateAction()
    {
        $this->registerTopicFromRequest('topic');
        $argument = $this->request->getArgument('topic');
        /** @var Topic $topic */
        $topic = $this->topicRepository->findByIdentifier($argument['__identity']);
        if ($this->settings['useImages']) {
            /** @var UploadMultipleFilesConverter $multipleFilesTypeConverter */
            $multipleFilesTypeConverter = $this->objectManager->get(UploadMultipleFilesConverter::class);
            $this->arguments->getArgument('topic')
                ->getPropertyMappingConfiguration()
                ->forProperty('images')
                ->setTypeConverter($multipleFilesTypeConverter)
                ->setTypeConverterOptions(UploadMultipleFilesConverter::class,
                    [
                        'IMAGES' => $topic->getImages()
                    ]
                );
        }
    }

    /**
     * action update.
     *
     * @param Topic $topic
     * @param bool $isNew We need the information if updateAction was called from createAction.
     *                    If so we have to add different messages
     * @return void
     */
    public function updateAction(Topic $topic, $isNew = false)
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
     * initialize action delete
     * hidden record throws an exception. Thats why I check it here before calling deleteAction.
     *
     * @return void
     */
    public function initializeDeleteAction()
    {
        $this->registerTopicFromRequest('topic');
    }

    /**
     * action delete.
     *
     * @param Topic $topic
     * @return void
     */
    public function deleteAction(Topic $topic)
    {
        $this->topicRepository->remove($topic);
        $this->addFlashMessage(LocalizationUtility::translate('topicDeleted', 'pforum'), '', FlashMessage::OK);
        $this->redirect('list', 'Forum');
    }

    /**
     * initialize action activate
     * hidden record throws an exception. Thats why I check it here before calling activateAction.
     *
     * @return void
     */
    public function initializeActivateAction()
    {
        $this->registerTopicFromRequest('topic');
    }

    /**
     * action activate by uid
     * We need this extra action, because hidden entries can't be found in FE mode.
     *
     * @param Topic $topic
     * @return void
     */
    public function activateAction(Topic $topic)
    {
        $topic->setHidden(false);
        $this->topicRepository->update($topic);
        $this->addFlashMessage(LocalizationUtility::translate('topicActivated', 'pforum'), '', FlashMessage::OK);
        $this->redirect('list', 'Forum');
    }
}
