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
use TYPO3\CMS\Core\Mail\MailMessage;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Extbase\Domain\Model\FrontendUser;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;

/**
 * Abstract class to keep PostController more clean
 */
class AbstractPostController extends AbstractController
{
    /**
     * @var PageRenderer
     */
    protected $pageRenderer;

    public function injectPageRenderer(PageRenderer $pageRenderer): void
    {
        $this->pageRenderer = $pageRenderer;
    }

    /**
     * This is a workaround to help controller actions to find (hidden) posts.
     *
     * @param string $argumentName
     */
    protected function registerPostFromRequest(string $argumentName): void
    {
        $argument = $this->request->getArgument($argumentName);
        if (is_array($argument)) {
            // get post from form ($_POST)
            $post = $this->postRepository->findHiddenEntryByUid((int)$argument['__identity']);
        } else {
            // get post from UID
            $post = $this->postRepository->findHiddenEntryByUid((int)$argument);
        }
        $this->session->registerObject($post, $post->getUid());
    }

    protected function addFeUserToPost(Topic $topic, Post $newPost): void
    {
        if (is_array($GLOBALS['TSFE']->fe_user->user) && $GLOBALS['TSFE']->fe_user->user['uid']) {
            /** @var FrontendUser $user */
            $user = $this->frontendUserRepository->findByUid(
                (int)$GLOBALS['TSFE']->fe_user->user['uid']
            );
            $newPost->setFrontendUser($user);
        } else {
            /* normally this should never be called, because the link to create a new entry was not displayed if user was not authenticated */
            $this->addFlashMessage('You must be logged in before creating a post');
            $this->redirect('show', 'Forum', null, ['forum' => $topic->getForum()]);
        }
    }

    protected function mailToUser(Post $post): void
    {
        $mail = $this->objectManager->get(MailMessage::class);
        $mail->setFrom($this->extConf->getEmailFromAddress(), $this->extConf->getEmailFromName());
        $mail->setTo($post->getUser()->getEmail(), $post->getUser()->getName());
        $mail->setSubject(LocalizationUtility::translate('email.post.subject', 'pforum'));
        if (version_compare(TYPO3_branch, '10.0', '>=')) {
            $mail->html($this->getContentForMail($post));
        } else {
            $mail->setBody($this->getContentForMail($post), 'text/html');
        }

        $mail->send();
    }

    protected function getContentForMail(Post $post): string
    {
        $view = $this->objectManager->get(StandaloneView::class);
        $view->setTemplatePathAndFilename(
            'EXT:pforum/Resources/Private/Templates/Mail/ConfigurePost.html'
        );
        $view->setControllerContext($this->getControllerContext());
        $view->assign('settings', $this->settings);
        $view->assign('post', $post);

        return $view->render();
    }

    protected function addFlashMessageForCreation(): void
    {
        if ($this->settings['post']['hideAtCreation']) {
            if ($this->settings['post']['activateByAdmin']) {
                $this->addFlashMessage(
                    LocalizationUtility::translate('hiddenPostCreatedAndActivateByAdmin', 'pforum'),
                    '',
                    FlashMessage::OK
                );
            } else {
                $this->addFlashMessage(
                    LocalizationUtility::translate('hiddenPostCreatedAndActivateByUser', 'pforum'),
                    '',
                    FlashMessage::OK
                );
            }
        } else {
            // if topic is not hidden at creation there is no need to activate it by admin
            $this->addFlashMessage(
                LocalizationUtility::translate('postCreated', 'pforum'),
                '',
                FlashMessage::OK
            );
        }
    }
}
