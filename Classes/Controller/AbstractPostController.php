<?php
namespace JWeiland\Pforum\Controller;

/*
 * This file is part of the package jweiland/pforum.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use JWeiland\Pforum\Controller\AbstractController;
use JWeiland\Pforum\Domain\Model\FrontendUser;
use JWeiland\Pforum\Domain\Model\Post;
use JWeiland\Pforum\Domain\Model\Topic;
use TYPO3\CMS\Core\Mail\MailMessage;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;

/**
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class AbstractPostController extends AbstractController
{
    /**
     * @var PageRenderer
     */
    protected $pageRenderer;

    /**
     * inject pageRenderer
     *
     * @param PageRenderer $pageRenderer
     * @return void
     */
    public function injectPageRenderer(PageRenderer $pageRenderer)
    {
        $this->pageRenderer = $pageRenderer;
    }

    /**
     * This is a workaround to help controller actions to find (hidden) posts.
     *
     * @param $argumentName
     * @return void
     */
    protected function registerPostFromRequest($argumentName)
    {
        $argument = $this->request->getArgument($argumentName);
        if (is_array($argument)) {
            // get post from form ($_POST)
            $post = $this->postRepository->findHiddenEntryByUid($argument['__identity']);
        } else {
            // get post from UID
            $post = $this->postRepository->findHiddenEntryByUid($argument);
        }
        $this->session->registerObject($post, $post->getUid());
    }

    /**
     * add current fe_user to post.
     *
     * @param Topic $topic
     * @param Post  $newPost
     * @return void
     */
    protected function addFeUserToPost(Topic $topic, Post $newPost)
    {
        if (is_array($GLOBALS['TSFE']->fe_user->user) && $GLOBALS['TSFE']->fe_user->user['uid']) {
            /** @var FrontendUser $user */
            $user = $this->frontendUserRepository->findByUid((int) $GLOBALS['TSFE']->fe_user->user['uid']);
            $newPost->setFrontendUser($user);
        } else {
            /* normally this should never be called, because the link to create a new entry was not displayed if user was not authenticated */
            $this->addFlashMessage('You must be logged in before creating a post');
            $this->redirect('show', 'Forum', null, ['forum' => $topic->getForum()]);
        }
    }

    /**
     * send mail to user to confirm, edit or delete his entry.
     *
     * @param Post $post
     * @return void
     */
    protected function mailToUser(Post $post)
    {
        $mail = $this->objectManager->get(MailMessage::class);
        $mail->setFrom($this->extConf->getEmailFromAddress(), $this->extConf->getEmailFromName());
        $mail->setTo($post->getUser()->getEmail(), $post->getUser()->getName());
        $mail->setSubject(LocalizationUtility::translate('email.post.subject', 'pforum'));
        $mail->setBody($this->getContent($post), 'text/html');

        $mail->send();
    }

    /**
     * get content for mailing.
     *
     * @param Post $post
     * @return string
     */
    protected function getContent(Post $post)
    {
        /** @var StandaloneView $view */
        $view = $this->objectManager->get(StandaloneView::class);
        $view->setTemplatePathAndFilename(
            ExtensionManagementUtility::extPath('pforum') .
            'Resources/Private/Templates/Mail/ConfigurePost.html'
        );
        $view->setControllerContext($this->getControllerContext());
        $view->assign('settings', $this->settings);
        $view->assign('post', $post);

        return $view->render();
    }

    /**
     * add flash message for creation.
     *
     * @return void
     */
    protected function addFlashMessageForCreation()
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
                FlashMessage::OK)
            ;
        }
    }
}
