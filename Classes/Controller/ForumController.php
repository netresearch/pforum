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
use JWeiland\Pforum\Domain\Model\Post;
use JWeiland\Pforum\Domain\Model\Topic;
use JWeiland\Pforum\Domain\Repository\ForumRepository;
use JWeiland\Pforum\Domain\Repository\PostRepository;
use JWeiland\Pforum\Domain\Repository\TopicRepository;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\PathUtility;

/**
 * Class ForumController
 *
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class ForumController extends AbstractController
{
    /**
     * forumRepository.
     *
     * @var \JWeiland\Pforum\Domain\Repository\ForumRepository
     */
    protected $forumRepository;

    /**
     * topicRepository.
     *
     * @var \JWeiland\Pforum\Domain\Repository\TopicRepository
     */
    protected $topicRepository;

    /**
     * postRepository.
     *
     * @var \JWeiland\Pforum\Domain\Repository\PostRepository
     */
    protected $postRepository;

    /**
     * pageRenderer
     *
     * @var \TYPO3\CMS\Core\Page\PageRenderer
     */
    protected $pageRenderer;

    /**
     * inject forumRepository
     *
     * @param ForumRepository $forumRepository
     * @return void
     */
    public function injectForumRepository(ForumRepository $forumRepository)
    {
        $this->forumRepository = $forumRepository;
    }

    /**
     * inject topicRepository
     *
     * @param TopicRepository $topicRepository
     * @return void
     */
    public function injectTopicRepository(TopicRepository $topicRepository)
    {
        $this->topicRepository = $topicRepository;
    }

    /**
     * inject postRepository
     *
     * @param PostRepository $postRepository
     * @return void
     */
    public function injectPostRepository(PostRepository $postRepository)
    {
        $this->postRepository = $postRepository;
    }

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
     * @param Forum $forum
     */
    public function showAction(Forum $forum)
    {
        /** @var \TYPO3\CMS\Extbase\Persistence\Generic\QueryResult $topics */
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

    /**
     * initialize activate action.
     */
    public function initializeListHiddenAction()
    {
        $this->pageRenderer->loadRequireJsModule('TYPO3/CMS/Pforum/Forum');
        $path = PathUtility::getAbsoluteWebPath(ExtensionManagementUtility::extPath('pforum')) .
            'Resources/Public/Css/';
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
     * @param Topic $topic
     * @param Post $post
     */
    public function activateAction(Topic $topic = null, Post $post = null)
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
