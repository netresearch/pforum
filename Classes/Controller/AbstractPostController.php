<?php
namespace JWeiland\Pforum\Controller;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class AbstractPostController extends \JWeiland\Pforum\Controller\AbstractController
{
    /**
     * @var \TYPO3\CMS\Core\Page\PageRenderer
     * @inject
     */
    protected $pageRenderer;

    /**
     * This is a workaround to help controller actions to find (hidden) posts.
     *
     * @param $argumentName
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
     * @param \JWeiland\Pforum\Domain\Model\Topic $topic
     * @param \JWeiland\Pforum\Domain\Model\Post  $newPost
     */
    protected function addFeUserToPost(\JWeiland\Pforum\Domain\Model\Topic $topic, \JWeiland\Pforum\Domain\Model\Post $newPost)
    {
        if (is_array($GLOBALS['TSFE']->fe_user->user) && $GLOBALS['TSFE']->fe_user->user['uid']) {
            /** @var \JWeiland\Pforum\Domain\Model\FrontendUser $user */
            $user = $this->frontendUserRepository->findByUid((int) $GLOBALS['TSFE']->fe_user->user['uid']);
            $newPost->setFrontendUser($user);
        } else {
            /* normally this should never be called, because the link to create a new entry was not displayed if user was not authenticated */
            $this->flashMessageContainer->add('You must be logged in before creating a post');
            $this->redirect('show', 'Forum', null, array('forum' => $topic->getForum()));
        }
    }

    /**
     * send mail to user to confirm, edit or delete his entry.
     *
     * @param \JWeiland\Pforum\Domain\Model\Post $post
     */
    protected function mailToUser(\JWeiland\Pforum\Domain\Model\Post $post)
    {
        $mail = $this->objectManager->get('TYPO3\\CMS\\Core\\Mail\\MailMessage');
        $mail->setFrom($this->extConf->getEmailFromAddress(), $this->extConf->getEmailFromName());
        $mail->setTo($post->getUser()->getEmail(), $post->getUser()->getName());
        $mail->setSubject(LocalizationUtility::translate('email.post.subject', 'pforum'));
        $mail->setBody($this->getContent($post), 'text/html');

        $mail->send();
    }

    /**
     * get content for mailing.
     *
     * @param \JWeiland\Pforum\Domain\Model\Post $post
     *
     * @return string
     */
    protected function getContent(\JWeiland\Pforum\Domain\Model\Post $post)
    {
        /** @var \TYPO3\CMS\Fluid\View\StandaloneView $view */
        $view = $this->objectManager->get('TYPO3\\CMS\\Fluid\\View\\StandaloneView');
        $view->setTemplatePathAndFilename(ExtensionManagementUtility::extPath('pforum').'Resources/Private/Templates/Mail/ConfigurePost.html');
        $view->setControllerContext($this->getControllerContext());
        $view->assign('settings', $this->settings);
        $view->assign('post', $post);

        return $view->render();
    }

    /**
     * add flash message for creation.
     */
    protected function addFlashMessageForCreation()
    {
        if ($this->settings['post']['hideAtCreation']) {
            if ($this->settings['post']['activateByAdmin']) {
                $this->flashMessageContainer->add(LocalizationUtility::translate('hiddenPostCreatedAndActivateByAdmin', 'pforum'), '', FlashMessage::OK);
            } else {
                $this->flashMessageContainer->add(LocalizationUtility::translate('hiddenPostCreatedAndActivateByUser', 'pforum'), '', FlashMessage::OK);
            }
        } else {
            // if topic is not hidden at creation there is no need to activate it by admin
            $this->flashMessageContainer->add(LocalizationUtility::translate('postCreated', 'pforum'), '', FlashMessage::OK);
        }
    }
}
