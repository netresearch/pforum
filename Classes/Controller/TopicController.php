<?php

namespace JWeiland\Pforum\Controller;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 Stefan Froemken <sfroemken@jweiland.net>, jweiland.net
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class TopicController extends \JWeiland\Pforum\Controller\AbstractTopicController
{
    /**
     * action show.
     *
     * @param \JWeiland\Pforum\Domain\Model\Topic $topic
     */
    public function showAction(\JWeiland\Pforum\Domain\Model\Topic $topic)
    {
        /* @var \TYPO3\CMS\Extbase\Persistence\Generic\QueryResult $topics */
        $posts = $this->postRepository->findByTopic($topic);
        if (!empty($this->settings['uidOfAdminGroup']) && is_array($GLOBALS['TSFE']->fe_user->groupData['uid']) && in_array($this->settings['uidOfAdminGroup'], $GLOBALS['TSFE']->fe_user->groupData['uid'])) {
            $posts->getQuery()
                ->getQuerySettings()
                ->setIgnoreEnableFields(true)
                ->setEnableFieldsToBeIgnored(array('disabled'));
        }
        $this->view->assign('topic', $topic);
        $this->view->assign('posts', $posts);
    }

    /**
     * action new.
     *
     * @param \JWeiland\Pforum\Domain\Model\Forum $forum
     */
    public function newAction(\JWeiland\Pforum\Domain\Model\Forum $forum)
    {
        $this->deleteUploadedFilesOnValidationErrors('newTopic');
        $this->view->assign('forum', $forum);
        $this->view->assign('newTopic', $this->objectManager->get('JWeiland\\Pforum\\Domain\\Model\\Topic'));
    }

    /**
     * set special typeConverter for images
     *
     * @return void
     */
    public function initializeCreateAction()
    {
        if ($this->settings['useImages']) {
            /** @var \JWeiland\Pforum\Property\TypeConverter\UploadMultipleFilesConverter $multipleFilesTypeConverter */
            $multipleFilesTypeConverter = $this->objectManager->get(
                'JWeiland\\Pforum\\Property\\TypeConverter\\UploadMultipleFilesConverter'
            );
            $this->arguments->getArgument('newTopic')
                ->getPropertyMappingConfiguration()
                ->forProperty('images')
                ->setTypeConverter($multipleFilesTypeConverter);
        }
    }

    /**
     * action create.
     *
     * @param \JWeiland\Pforum\Domain\Model\Forum $forum
     * @param \JWeiland\Pforum\Domain\Model\Topic $newTopic
     */
    public function createAction(\JWeiland\Pforum\Domain\Model\Forum $forum, \JWeiland\Pforum\Domain\Model\Topic $newTopic)
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
            $this->redirect('edit', null, null, array('topic' => $newTopic, 'isPreview' => true, 'isNew' => true));
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
        $this->redirect('show', 'Forum', null, array('forum' => $forum));
    }

    /**
     * initialize action edit
     * hidden record throws an exception. Thats why I check it here before calling editAction.
     */
    public function initializeEditAction()
    {
        $this->registerTopicFromRequest('topic');
    }

    /**
     * action edit.
     *
     * @param \JWeiland\Pforum\Domain\Model\Topic $topic
     * @param bool                                $isPreview If is preview there will be an additional output above edit form
     * @param bool                                $isNew     We need the information if updateAction was called from createAction. If so we have to passthrough this information
     * @dontvalidate $topic
     */
    public function editAction(\JWeiland\Pforum\Domain\Model\Topic $topic = null, $isPreview = false, $isNew = false)
    {
        $this->view->assign('topic', $topic);
        $this->view->assign('isPreview', $isPreview);
        $this->view->assign('isNew', $isNew);
    }

    /**
     * getObjectByIdentifier can only find non-hidden values
     * With this method we help extbase backend to find our hidden object.
     */
    public function initializeUpdateAction()
    {
        $this->registerTopicFromRequest('topic');
        $argument = $this->request->getArgument('topic');
        /** @var \JWeiland\Pforum\Domain\Model\Topic $topic */
        $topic = $this->topicRepository->findByIdentifier($argument['__identity']);
        if ($this->settings['useImages']) {
            /** @var \JWeiland\Pforum\Property\TypeConverter\UploadMultipleFilesConverter $multipleFilesTypeConverter */
            $multipleFilesTypeConverter = $this->objectManager->get(
                'JWeiland\\Pforum\\Property\\TypeConverter\\UploadMultipleFilesConverter'
            );
            $this->arguments->getArgument('topic')
                ->getPropertyMappingConfiguration()
                ->forProperty('images')
                ->setTypeConverter($multipleFilesTypeConverter)
                ->setTypeConverterOptions('JWeiland\\Pforum\\Property\\TypeConverter\\UploadMultipleFilesConverter',
                    array(
                        'IMAGES' => $topic->getImages()
                    )
                );
        }
    }

    /**
     * action update.
     *
     * @param \JWeiland\Pforum\Domain\Model\Topic $topic
     * @param bool                                $isNew We need the information if updateAction was called from createAction. If so we have to add different messages
     */
    public function updateAction(\JWeiland\Pforum\Domain\Model\Topic $topic, $isNew = false)
    {
        $this->topicRepository->update($topic);

        // if a preview was requested direct to preview action
        if ($this->controllerContext->getRequest()->hasArgument('preview')) {
            $topic->setHidden(true);
            $this->redirect('edit', null, null, array('topic' => $topic, 'isPreview' => true, 'isNew' => $isNew));
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
                $this->flashMessageContainer->add(LocalizationUtility::translate('topicUpdated', 'pforum'), '', FlashMessage::OK);
            }
            $this->redirect('show', 'Forum', '', array('forum' => $topic->getForum()));
        }
    }

    /**
     * initialize action delete
     * hidden record throws an exception. Thats why I check it here before calling deleteAction.
     */
    public function initializeDeleteAction()
    {
        $this->registerTopicFromRequest('topic');
    }

    /**
     * action delete.
     *
     * @param \JWeiland\Pforum\Domain\Model\Topic $topic
     */
    public function deleteAction(\JWeiland\Pforum\Domain\Model\Topic $topic)
    {
        $this->topicRepository->remove($topic);
        $this->flashMessageContainer->add(LocalizationUtility::translate('topicDeleted', 'pforum'), '', FlashMessage::OK);
        $this->redirect('list', 'Forum');
    }

    /**
     * initialize action activate
     * hidden record throws an exception. Thats why I check it here before calling activateAction.
     */
    public function initializeActivateAction()
    {
        $this->registerTopicFromRequest('topic');
    }

    /**
     * action activate by uid
     * We need this extra action, because hidden entries can't be found in FE mode.
     *
     * @param \JWeiland\Pforum\Domain\Model\Topic $topic
     */
    public function activateAction(\JWeiland\Pforum\Domain\Model\Topic $topic)
    {
        $topic->setHidden(false);
        $this->topicRepository->update($topic);
        $this->flashMessageContainer->add(LocalizationUtility::translate('topicActivated', 'pforum'), '', FlashMessage::OK);
        $this->redirect('list', 'Forum');
    }
}
