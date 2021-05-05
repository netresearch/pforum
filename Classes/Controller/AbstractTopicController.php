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
use JWeiland\Pforum\Domain\Model\FrontendUser;
use JWeiland\Pforum\Domain\Model\Topic;
use TYPO3\CMS\Core\Mail\MailMessage;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;

/**
 * Abstract class to keep TopicController more clean
 */
class AbstractTopicController extends AbstractController
{
    /**
     * This is a workaround to help controller actions to find (hidden) topics.
     *
     * @param string $argumentName
     */
    protected function registerTopicFromRequest(string $argumentName): void
    {
        $argument = $this->request->getArgument($argumentName);
        if (is_array($argument)) {
            // get topic from form ($_POST)
            $topic = $this->topicRepository->findHiddenEntryByUid((int)$argument['__identity']);
        } else {
            // get topic from UID
            $topic = $this->topicRepository->findHiddenEntryByUid((int)$argument);
        }
        $this->session->registerObject($topic, $topic->getUid());
    }

    protected function addFeUserToTopic(Forum $forum, Topic $newTopic): void
    {
        if (is_array($GLOBALS['TSFE']->fe_user->user) && $GLOBALS['TSFE']->fe_user->user['uid']) {
            /** @var FrontendUser $user */
            $user = $this->frontendUserRepository->findByUid(
                (int)$GLOBALS['TSFE']->fe_user->user['uid']
            );
            $newTopic->setFrontendUser($user);
        } else {
            /* normally this should never be called, because the link to create a new entry was not displayed if user was not authenticated */
            $this->addFlashMessage('You must be logged in before creating a topic', '', FlashMessage::WARNING);
            $this->redirect('show', 'Forum', null, ['forum' => $forum]);
        }
    }

    protected function mailToUser(Topic $topic): void
    {
        $mail = $this->objectManager->get(MailMessage::class);
        $mail->setFrom($this->extConf->getEmailFromAddress(), $this->extConf->getEmailFromName());
        $mail->setTo($topic->getUser()->getEmail(), $topic->getUser()->getName());
        $mail->setSubject(
            LocalizationUtility::translate(
                'email.topic.subject',
                'pforum'
            )
        );
        if (version_compare(TYPO3_branch, '10.0', '>=')) {
            $mail->html($this->getContentForMailing($topic));
        } else {
            $mail->setBody($this->getContentForMailing($topic), 'text/html');
        }

        $mail->send();
    }

    protected function getContentForMailing(Topic $topic): string
    {
        $view = $this->objectManager->get(StandaloneView::class);
        $view->setTemplatePathAndFilename(
            sprintf(
                '%s%s',
                ExtensionManagementUtility::extPath('pforum'),
                'Resources/Private/Templates/Mail/ConfigureTopic.html'
            )
        );
        $view->setControllerContext($this->getControllerContext());
        $view->assign('settings', $this->settings);
        $view->assign('topic', $topic);

        return $view->render();
    }

    protected function addFlashMessageForCreation(): void
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
