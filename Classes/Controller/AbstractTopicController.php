<?php
namespace JWeiland\Pforum\Controller;

/*
 * This file is part of the package jweiland/pforum.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use JWeiland\Pforum\Controller\AbstractController;
use JWeiland\Pforum\Domain\Model\Forum;
use JWeiland\Pforum\Domain\Model\FrontendUser;
use JWeiland\Pforum\Domain\Model\Topic;
use TYPO3\CMS\Core\Mail\MailMessage;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;

/**
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class AbstractTopicController extends AbstractController
{
    /**
     * This is a workaround to help controller actions to find (hidden) topics.
     *
     * @param $argumentName
     */
    protected function registerTopicFromRequest($argumentName)
    {
        $argument = $this->request->getArgument($argumentName);
        if (is_array($argument)) {
            // get topic from form ($_POST)
            $topic = $this->topicRepository->findHiddenEntryByUid($argument['__identity']);
        } else {
            // get topic from UID
            $topic = $this->topicRepository->findHiddenEntryByUid($argument);
        }
        $this->session->registerObject($topic, $topic->getUid());
    }

    /**
     * add current fe_user to topic.
     *
     * @param Forum $forum
     * @param Topic $newTopic
     */
    protected function addFeUserToTopic(Forum $forum, Topic $newTopic)
    {
        if (is_array($GLOBALS['TSFE']->fe_user->user) && $GLOBALS['TSFE']->fe_user->user['uid']) {
            /** @var FrontendUser $user */
            $user = $this->frontendUserRepository->findByUid((int) $GLOBALS['TSFE']->fe_user->user['uid']);
            $newTopic->setFrontendUser($user);
        } else {
            /* normally this should never be called, because the link to create a new entry was not displayed if user was not authenticated */
            $this->addFlashMessage('You must be logged in before creating a topic', '', FlashMessage::WARNING);
            $this->redirect('show', 'Forum', null, ['forum' => $forum]);
        }
    }

    /**
     * send mail to user to confirm, edit or delete his entry.
     *
     * @param Topic $topic
     */
    protected function mailToUser(Topic $topic)
    {
        $mail = $this->objectManager->get(MailMessage::class);
        $mail->setFrom($this->extConf->getEmailFromAddress(), $this->extConf->getEmailFromName());
        $mail->setTo($topic->getUser()->getEmail(), $topic->getUser()->getName());
        $mail->setSubject(LocalizationUtility::translate('email.topic.subject', 'pforum'));
        $mail->setBody($this->getContent($topic), 'text/html');

        $mail->send();
    }

    /**
     * get content for mailing.
     *
     * @param Topic $topic
     *
     * @return string
     */
    protected function getContent(Topic $topic)
    {
        /** @var StandaloneView $view */
        $view = $this->objectManager->get(StandaloneView::class);
        $view->setTemplatePathAndFilename(
            ExtensionManagementUtility::extPath('pforum') . 'Resources/Private/Templates/Mail/ConfigureTopic.html'
        );
        $view->setControllerContext($this->getControllerContext());
        $view->assign('settings', $this->settings);
        $view->assign('topic', $topic);

        return $view->render();
    }

    /**
     * add flash message for creation.
     *
     * @return void
     */
    protected function addFlashMessageForCreation()
    {
        if ($this->settings['topic']['hideAtCreation']) {
            if ($this->settings['topic']['activateByAdmin']) {
                $this->addFlashMessage(
                    LocalizationUtility::translate('hiddenTopicCreatedAndActivateByAdmin', 'pforum'),
                    '',
                    FlashMessage::OK
                );
            } else {
                $this->addFlashMessage(
                    LocalizationUtility::translate('hiddenTopicCreatedAndActivateByUser', 'pforum'),
                    '',
                    FlashMessage::OK
                );
            }
        } else {
            // if topic is not hidden at creation there is no need to activate it by admin
            $this->addFlashMessage(LocalizationUtility::translate('topicCreated', 'pforum'), '', FlashMessage::OK);
        }
    }
}
