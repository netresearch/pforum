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
use JWeiland\Pforum\Domain\Model\Post;
use JWeiland\Pforum\Domain\Model\Topic;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\PathUtility;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;

/**
 * Main controller to list and show postings/questions
 */
class ForumController extends AbstractController
{
    /**
     * @var PageRenderer
     */
    protected $pageRenderer;

    public function injectPageRenderer(PageRenderer $pageRenderer): void
    {
        $this->pageRenderer = $pageRenderer;
    }

    public function listAction(): void
    {
        $forums = $this->forumRepository->findAll();
        $this->view->assign('forums', $forums);
    }

    /**
     * @param Forum $forum
     */
    public function showAction(Forum $forum): void
    {
        /** @var QueryResultInterface $topics */
        $topics = $this->topicRepository->findByForum($forum);
        if (
            !empty($this->settings['uidOfAdminGroup']) &&
            is_array($GLOBALS['TSFE']->fe_user->groupData['uid']) &&
            in_array($this->settings['uidOfAdminGroup'], $GLOBALS['TSFE']->fe_user->groupData['uid'])
        ) {
            $topics->getQuery()
                ->getQuerySettings()
                ->setIgnoreEnableFields(true)
                ->setEnableFieldsToBeIgnored(['disabled']);
        }
        $this->view->assign('forum', $forum);
        $this->view->assign('topics', $topics);
    }

    public function initializeListHiddenAction(): void
    {
        $this->pageRenderer->loadRequireJsModule('TYPO3/CMS/Pforum/Forum');
        $path = PathUtility::getAbsoluteWebPath(ExtensionManagementUtility::extPath('pforum')) .
            'Resources/Public/Css/';
        $this->pageRenderer->addCssFile($path . 'demo_page.css');
        $this->pageRenderer->addCssFile($path . 'demo_table_jui.css');
        $this->pageRenderer->addCssFile($path . 'smoothness/jquery-ui-1.10.3.custom.min.css');
    }

    public function listHiddenAction(): void
    {
        $this->view->assign('topics', $this->topicRepository->findAllHidden());
        $this->view->assign('posts', $this->postRepository->findAllHidden());
    }

    /**
     * This is for BE use only.
     *
     * @param Topic|null $topic
     * @param Post|null $post
     */
    public function activateAction(?Topic $topic, ?Post $post = null): void
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
