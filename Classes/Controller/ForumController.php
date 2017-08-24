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
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

/**
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class ForumController extends \JWeiland\Pforum\Controller\AbstractController
{
    /**
     * forumRepository.
     *
     * @var \JWeiland\Pforum\Domain\Repository\ForumRepository
     * @inject
     */
    protected $forumRepository;

    /**
     * topicRepository.
     *
     * @var \JWeiland\Pforum\Domain\Repository\TopicRepository
     * @inject
     */
    protected $topicRepository;

    /**
     * postRepository.
     *
     * @var \JWeiland\Pforum\Domain\Repository\PostRepository
     * @inject
     */
    protected $postRepository;

    /**
     * @var \TYPO3\CMS\Core\Page\PageRenderer
     * @inject
     */
    protected $pageRenderer;

    /**
     * action list.
     */
    public function listAction()
    {
        $forums = $this->forumRepository->findAll();
        $this->view->assign('forums', $forums);
    }

    /**
     * action show.
     *
     * @param \JWeiland\Pforum\Domain\Model\Forum $forum
     */
    public function showAction(\JWeiland\Pforum\Domain\Model\Forum $forum)
    {
        /** @var \TYPO3\CMS\Extbase\Persistence\Generic\QueryResult $topics */
        $topics = $this->topicRepository->findByForum($forum);
        if (!empty($this->settings['uidOfAdminGroup']) && is_array($GLOBALS['TSFE']->fe_user->groupData['uid']) && in_array($this->settings['uidOfAdminGroup'], $GLOBALS['TSFE']->fe_user->groupData['uid'])) {
            $topics->getQuery()
                ->getQuerySettings()
                ->setIgnoreEnableFields(true)
                ->setEnableFieldsToBeIgnored(array('disabled'));
        }
        $this->view->assign('forum', $forum);
        $this->view->assign('topics', $topics);
    }

    /**
     * initialize activate action.
     */
    public function initializeListHiddenAction()
    {
        $path = ExtensionManagementUtility::extRelPath('pforum').'Resources/Public/JavaScript/';
        $this->pageRenderer->addJsFile($path.'jquery-1.9.1.min.js');
        $this->pageRenderer->addJsFile($path.'jquery.dataTables.min.js');

        $path = ExtensionManagementUtility::extRelPath('pforum').'Resources/Public/Css/';
        $this->pageRenderer->addCssFile($path.'demo_page.css');
        $this->pageRenderer->addCssFile($path.'demo_table_jui.css');
        $this->pageRenderer->addCssFile($path.'smoothness/jquery-ui-1.10.3.custom.min.css');
    }

    /**
     * action list hidden.
     */
    public function listHiddenAction()
    {
        $this->view->assign('topics', $this->topicRepository->findAllHidden());
        $this->view->assign('posts', $this->postRepository->findAllHidden());
    }

    /**
     * action activate
     * This is for BE use.
     *
     * @param \JWeiland\Pforum\Domain\Model\Topic $topic
     * @param \JWeiland\Pforum\Domain\Model\Post  $post
     */
    public function activateAction(\JWeiland\Pforum\Domain\Model\Topic $topic = null, \JWeiland\Pforum\Domain\Model\Post $post = null)
    {
        if (!empty($topic)) {
            $topic->setHidden(false);
            $this->topicRepository->update($topic);
        }
        if (!empty($post)) {
            $post->setHidden(false);
            $this->postRepository->update($post);
        }
        $this->redirect('listHidden');
    }
}
