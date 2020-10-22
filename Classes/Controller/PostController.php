<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/pforum.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Pforum\Controller;

use JWeiland\Pforum\Domain\Model\Post;
use JWeiland\Pforum\Domain\Model\Topic;
use JWeiland\Pforum\Domain\Model\User;
use TYPO3\CMS\Core\Mail\MailMessage;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Extbase\Annotation as Extbase;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * Controller to manage (list and show) postings
 */
class PostController extends AbstractPostController
{
    /**
     * @param Topic $topic
     * @param Post|null $newPost
     * @Extbase\IgnoreValidation("newPost")
     */
    public function newAction(Topic $topic, Post $newPost = null): void
    {
        $this->view->assign('topic', $topic);
        $this->view->assign('newPost', $newPost);
    }

    /**
     * Check if email of user object is mandatory or not.
     */
    public function initializeCreateAction(): void
    {
        if ($this->settings['useImages']) {
            // we have our own implementation how to implement images
            $this->arguments->getArgument('newPost')->getPropertyMappingConfiguration()->setTargetTypeForSubProperty(
                'images', 'array'
            );
        }
    }

    /**
     * @param Topic $topic
     * @param Post $newPost
     */
    public function createAction(Topic $topic, Post $newPost): void
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
            $this->redirect('edit', null, null, ['post' => $newPost, 'isPreview' => true, 'isNew' => true]);
        }

        if ($this->settings['post']['hideAtCreation']) {
            $newPost->setHidden(true);
        }

        // if auth = anonymous user
        if ($this->settings['auth'] == 1) {
            /* send a mail to the user to activate, edit or delete his entry */
            if ($this->settings['emailIsMandatory']) {
                $this->persistenceManager->persistAll(); // we need an uid for mailing
                $this->mailToUser($newPost);
            }
        }
        if (
            $newPost->getHidden() === false &&
            $topic->getUser() instanceof User &&
            $topic->getUser()->getEmail() != ''
        ) {
            // send an email to creator of topic to inform him about new comments/posts
            $this->mailToTopicCreator($topic, $newPost);
        }
        $this->addFlashMessageForCreation();
        $this->redirect('show', 'Topic', null, ['topic' => $topic]);
    }

    /**
     * Hidden record throws an exception.
     * That's why I check it here before calling editAction.
     */
    public function initializeEditAction(): void
    {
        $this->registerPostFromRequest('post');
    }

    /**
     * @param Post|null $post
     * @param bool $isPreview
     * @param bool $isNew We need the information if updateAction was called from createAction.
     *                    If so we have to passthrough this information
     * @Extbase\IgnoreValidation("post")
     */
    public function editAction(
        Post $post = null,
        bool $isPreview = false,
        bool $isNew = false
    ): void {
        $this->view->assign('post', $post);
        $this->view->assign('isPreview', $isPreview);
        $this->view->assign('isNew', $isNew);
    }

    /**
     * getObjectByIdentifier can only find non-hidden values.
     * With this method we help extbase backend to find our hidden object.
     */
    public function initializeUpdateAction(): void
    {
        $this->registerPostFromRequest('post');
        if ($this->settings['useImages']) {
            // we have our own implementation how to implement images
            $this->arguments->getArgument('post')->getPropertyMappingConfiguration()->setTargetTypeForSubProperty(
                'images', 'array'
            );
        }
    }

    /**
     * @param Post $post
     * @param bool $isNew We need the information if updateAction was
     *                    called from createAction. If so we have to add different messages
     */
    public function updateAction(Post $post, bool $isNew = false): void
    {
        $this->postRepository->update($post);

        // if a preview was requested direct to preview action
        if ($this->controllerContext->getRequest()->hasArgument('preview')) {
            $post->setHidden(true);
            $this->redirect('edit', null, null, ['post' => $post, 'isPreview' => true, 'isNew' => $isNew]);
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
                $this->addFlashMessage(LocalizationUtility::translate('postUpdated', 'pforum'), '', FlashMessage::OK);
            }
            $this->redirect('show', 'Forum', '', ['forum' => $post->getTopic()->getForum()]);
        }
    }

    /**
     * Hidden record throws an exception.
     * That's why I check it here before calling deleteAction.
     */
    public function initializeDeleteAction(): void
    {
        $this->registerPostFromRequest('post');
    }

    /**
     * @param Post $post
     */
    public function deleteAction(Post $post): void
    {
        $this->postRepository->remove($post);
        $this->addFlashMessage(LocalizationUtility::translate('postDeleted', 'pforum'), '', FlashMessage::OK);
        $this->redirect('list', 'Forum');
    }

    protected function mailToTopicCreator(Topic $topic, Post $post): void
    {
        $mail = $this->objectManager->get(MailMessage::class);
        $mail->setFrom($this->extConf->getEmailFromAddress(), $this->extConf->getEmailFromName());
        $mail->setTo($topic->getUser()->getEmail(), $topic->getUser()->getName());
        $mail->setSubject(LocalizationUtility::translate(
            'email.post.subject.newPost', 'pforum', [$topic->getTitle()])
        );

        $content = LocalizationUtility::translate(
            'email.post.text.newPost',
            'pforum',
            [
                $topic->getUser()->getName(),
                $topic->getTitle(),
                $post->getDescription(),
            ]
        );

        if (version_compare(TYPO3_branch, '10.0', '>=')) {
            $mail->html($content);
        } else {
            $mail->setBody($content, 'text/html');
        }

        $mail->send();
    }

    /**
     * Hidden record throws an exception.
     * That's why I check it here before calling activateAction.
     */
    public function initializeActivateAction(): void
    {
        $this->registerPostFromRequest('post');
    }

    /**
     * We need this extra action, because hidden entries can't be found in FE mode.
     *
     * @param Post $post
     */
    public function activateAction(Post $post): void
    {
        $post->setHidden(false);
        $this->postRepository->update($post);

        // send an email to creator of topic to inform him about new comments/posts
        $this->mailToTopicCreator($post->getTopic(), $post);

        $this->addFlashMessage(LocalizationUtility::translate('postActivated', 'pforum'), '', FlashMessage::OK);
        $this->redirect('list', 'Forum');
    }
}
