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
class PostController extends \JWeiland\Pforum\Controller\AbstractPostController
{
    /**
     * action new.
     *
     * @param \JWeiland\Pforum\Domain\Model\Topic $topic
     * @param \JWeiland\Pforum\Domain\Model\Post  $newPost
     * @dontvalidate $newPost
     */
    public function newAction(\JWeiland\Pforum\Domain\Model\Topic $topic, \JWeiland\Pforum\Domain\Model\Post $newPost = null)
    {
        $this->view->assign('topic', $topic);
        $this->view->assign('newPost', $newPost);
    }

    /**
     * check if email of user object is mandatory or not.
     */
    public function initializeCreateAction()
    {
        if ($this->settings['useImages']) {
            // we have our own implementation how to implement images
            $this->arguments->getArgument('newPost')->getPropertyMappingConfiguration()->setTargetTypeForSubProperty('images', 'array');
        }
    }

    /**
     * action create.
     *
     * @param \JWeiland\Pforum\Domain\Model\Topic $topic
     * @param \JWeiland\Pforum\Domain\Model\Post  $newPost
     */
    public function createAction(\JWeiland\Pforum\Domain\Model\Topic $topic, \JWeiland\Pforum\Domain\Model\Post $newPost)
    {
        /* if auth = frontend user */
        if ($this->settings['auth'] == 2) {
            $this->addFeUserToPost($topic, $newPost);
        }

        $topic->addPost($newPost);
        $this->topicRepository->update($topic);

        // if a preview was requested direct to preview action
        if ($this->controllerContext->getRequest()->hasArgument('preview')) {
            $newPost->setHidden(true); // post should not be visible while previewing
            $this->persistenceManager->persistAll(); // we need an uid before redirecting
            $this->redirect('edit', null, null, array('post' => $newPost, 'isPreview' => true, 'isNew' => true));
        }

        if ($this->settings['post']['hideAtCreation']) {
            $newPost->setHidden(true);
        }

        /* if auth = anonymous user */
        if ($this->settings['auth'] == 1) {
            /* send a mail to the user to activate, edit or delete his entry */
            if ($this->settings['emailIsMandatory']) {
                $this->persistenceManager->persistAll(); // we need an uid for mailing
                $this->mailToUser($newPost);
            }
        }
        if ($newPost->getHidden() === false && $topic->getUser() instanceof \JWeiland\Pforum\Domain\Model\User && $topic->getUser()->getEmail() != '') {
            // send an email to creator of topic to inform him about new comments/posts
            $this->mailToTopicCreator($topic, $newPost);
        }
        $this->addFlashMessageForCreation();
        $this->redirect('show', 'Topic', null, array('topic' => $topic));
    }

    /**
     * initialize action edit
     * hidden record throws an exception. Thats why I check it here before calling editAction.
     */
    public function initializeEditAction()
    {
        $this->registerPostFromRequest('post');
    }

    /**
     * action edit.
     *
     * @param \JWeiland\Pforum\Domain\Model\Post $post
     * @param bool                               $isPreview
     * @param bool                               $isNew     We need the information if updateAction was called from createAction. If so we have to passthrough this information
     * @dontvalidate $post
     */
    public function editAction(\JWeiland\Pforum\Domain\Model\Post $post = null, $isPreview = false, $isNew = false)
    {
        $this->view->assign('post', $post);
        $this->view->assign('isPreview', $isPreview);
        $this->view->assign('isNew', $isNew);
    }

    /**
     * getObjectByIdentifier can only find non-hidden values
     * With this method we help extbase backend to find our hidden object.
     */
    public function initializeUpdateAction()
    {
        $this->registerPostFromRequest('post');
        if ($this->settings['useImages']) {
            // we have our own implementation how to implement images
            $this->arguments->getArgument('post')->getPropertyMappingConfiguration()->setTargetTypeForSubProperty('images', 'array');
        }
    }

    /**
     * action update.
     *
     * @param \JWeiland\Pforum\Domain\Model\Post $post
     * @param bool                               $isNew We need the information if updateAction was called from createAction. If so we have to add different messages
     */
    public function updateAction(\JWeiland\Pforum\Domain\Model\Post $post, $isNew = false)
    {
        $this->postRepository->update($post);

        // if a preview was requested direct to preview action
        if ($this->controllerContext->getRequest()->hasArgument('preview')) {
            $post->setHidden(true);
            $this->redirect('edit', null, null, array('post' => $post, 'isPreview' => true, 'isNew' => $isNew));
        } else {
            if ($isNew) {
                // if is new and preview was pressed we have to check for visibility again
                if ($this->settings['post']['hideAtCreation']) {
                    $post->setHidden(true);
                } else {
                    $post->setHidden(false);
                }

                /* if auth = anonymous user */
                if ($this->settings['auth'] == 1) {
                    /* send a mail to the user to activate, edit or delete his entry */
                    if ($this->settings['emailIsMandatory']) {
                        $this->mailToUser($post);
                    }
                }
                $this->addFlashMessageForCreation();
            } else {
                // edited posts which are not new are visible
                $post->setHidden(false);
                $this->flashMessageContainer->add(LocalizationUtility::translate('postUpdated', 'pforum'), '', FlashMessage::OK);
            }
            $this->redirect('show', 'Forum', '', array('forum' => $post->getTopic()->getForum()));
        }
    }

    /**
     * initialize action delete
     * hidden record throws an exception. Thats why I check it here before calling deleteAction.
     */
    public function initializeDeleteAction()
    {
        $this->registerPostFromRequest('post');
    }

    /**
     * action delete.
     *
     * @param \JWeiland\Pforum\Domain\Model\Post $post
     */
    public function deleteAction(\JWeiland\Pforum\Domain\Model\Post $post)
    {
        $this->topicRepository->remove($post);
        $this->flashMessageContainer->add(LocalizationUtility::translate('postDeleted', 'pforum'), '', FlashMessage::OK);
        $this->redirect('list', 'Forum');
    }

    /**
     * send mail to user to confirm, edit or delete his entry.
     *
     * @param \JWeiland\Pforum\Domain\Model\Topic $topic
     * @param \JWeiland\Pforum\Domain\Model\Post  $post
     */
    protected function mailToTopicCreator(\JWeiland\Pforum\Domain\Model\Topic $topic, \JWeiland\Pforum\Domain\Model\Post $post)
    {
        $mail = $this->objectManager->get('TYPO3\\CMS\\Core\\Mail\\MailMessage');
        $mail->setFrom($this->extConf->getEmailFromAddress(), $this->extConf->getEmailFromName());
        $mail->setTo($topic->getUser()->getEmail(), $topic->getUser()->getName());
        $mail->setSubject(LocalizationUtility::translate('email.post.subject.newPost', 'pforum', array($topic->getTitle())));
        $mail->setBody(LocalizationUtility::translate(
            'email.post.text.newPost',
            'pforum',
            array(
                $topic->getUser()->getName(),
                $topic->getTitle(),
                $post->getDescription(),
            )
        ), 'text/html');

        $mail->send();
    }

    /**
     * initialize action activate
     * hidden record throws an exception. Thats why I check it here before calling activateAction.
     */
    public function initializeActivateAction()
    {
        $this->registerPostFromRequest('post');
    }

    /**
     * action activate by uid
     * We need this extra action, because hidden entries can't be found in FE mode.
     *
     * @param \JWeiland\Pforum\Domain\Model\Post $post
     */
    public function activateAction(\JWeiland\Pforum\Domain\Model\Post $post)
    {
        $post->setHidden(false);
        $this->postRepository->update($post);

        // send an email to creator of topic to inform him about new comments/posts
        $this->mailToTopicCreator($post->getTopic(), $post);

        $this->flashMessageContainer->add(LocalizationUtility::translate('postActivated', 'pforum'), '', FlashMessage::OK);
        $this->redirect('list', 'Forum');
    }
}
