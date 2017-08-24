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
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class AbstractTopicController extends \JWeiland\Pforum\Controller\AbstractController
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
     * @param \JWeiland\Pforum\Domain\Model\Forum $forum
     * @param \JWeiland\Pforum\Domain\Model\Topic $newTopic
     */
    protected function addFeUserToTopic(\JWeiland\Pforum\Domain\Model\Forum $forum, \JWeiland\Pforum\Domain\Model\Topic $newTopic)
    {
        if (is_array($GLOBALS['TSFE']->fe_user->user) && $GLOBALS['TSFE']->fe_user->user['uid']) {
            /** @var \JWeiland\Pforum\Domain\Model\FrontendUser $user */
            $user = $this->frontendUserRepository->findByUid((int) $GLOBALS['TSFE']->fe_user->user['uid']);
            $newTopic->setFrontendUser($user);
        } else {
            /* normally this should never be called, because the link to create a new entry was not displayed if user was not authenticated */
            $this->flashMessageContainer->add('You must be logged in before creating a topic');
            $this->redirect('show', 'Forum', null, array('forum' => $forum));
        }
    }

    /**
     * send mail to user to confirm, edit or delete his entry.
     *
     * @param \JWeiland\Pforum\Domain\Model\Topic $topic
     */
    protected function mailToUser(\JWeiland\Pforum\Domain\Model\Topic $topic)
    {
        $mail = $this->objectManager->get('TYPO3\\CMS\\Core\\Mail\\MailMessage');
        $mail->setFrom($this->extConf->getEmailFromAddress(), $this->extConf->getEmailFromName());
        $mail->setTo($topic->getUser()->getEmail(), $topic->getUser()->getName());
        $mail->setSubject(LocalizationUtility::translate('email.topic.subject', 'pforum'));
        $mail->setBody($this->getContent($topic), 'text/html');

        $mail->send();
    }

    /**
     * get content for mailing.
     *
     * @param \JWeiland\Pforum\Domain\Model\Topic $topic
     *
     * @return string
     */
    protected function getContent(\JWeiland\Pforum\Domain\Model\Topic $topic)
    {
        /** @var \TYPO3\CMS\Fluid\View\StandaloneView $view */
        $view = $this->objectManager->get('TYPO3\\CMS\\Fluid\\View\\StandaloneView');
        $view->setTemplatePathAndFilename(ExtensionManagementUtility::extPath('pforum').'Resources/Private/Templates/Mail/ConfigureTopic.html');
        $view->setControllerContext($this->getControllerContext());
        $view->assign('settings', $this->settings);
        $view->assign('topic', $topic);

        return $view->render();
    }

    /**
     * add flash message for creation.
     */
    protected function addFlashMessageForCreation()
    {
        if ($this->settings['topic']['hideAtCreation']) {
            if ($this->settings['topic']['activateByAdmin']) {
                $this->flashMessageContainer->add(LocalizationUtility::translate('hiddenTopicCreatedAndActivateByAdmin', 'pforum'), '', FlashMessage::OK);
            } else {
                $this->flashMessageContainer->add(LocalizationUtility::translate('hiddenTopicCreatedAndActivateByUser', 'pforum'), '', FlashMessage::OK);
            }
        } else {
            // if topic is not hidden at creation there is no need to activate it by admin
            $this->flashMessageContainer->add(LocalizationUtility::translate('topicCreated', 'pforum'), '', FlashMessage::OK);
        }
    }
}
