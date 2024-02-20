<?php

/**
 * This file is part of the package jweiland/pforum.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

namespace JWeiland\Pforum\Controller;

use JWeiland\Pforum\Domain\Model\Post;
use JWeiland\Pforum\Domain\Model\Topic;
use JWeiland\Pforum\Domain\Repository\PostRepository;
use JWeiland\Pforum\Domain\Repository\TopicRepository;
use TYPO3\CMS\Backend\Template\Components\ButtonBar;
use TYPO3\CMS\Backend\View\BackendTemplateView;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Messaging\AbstractMessage;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Mvc\View\ViewInterface;

/**
 * Main controller to list and show postings/questions
 */
class AdministrationController extends ActionController
{
    /**
     * @var BackendTemplateView
     */
    protected $view;

    /**
     * @var BackendTemplateView
     */
    protected $defaultViewObjectName = BackendTemplateView::class;

    /**
     * @var TopicRepository
     */
    protected $topicRepository;

    /**
     * @var PostRepository
     */
    protected $postRepository;

    public function __construct(TopicRepository $topicRepository, PostRepository $postRepository)
    {
        $this->topicRepository = $topicRepository;
        $this->postRepository = $postRepository;
    }

    /**
     * Set up the doc header properly here
     */
    protected function initializeView(ViewInterface $view): void
    {
        if ($view instanceof BackendTemplateView) {
            parent::initializeView($view);
            //$view->getModuleTemplate()->getDocHeaderComponent()->setMetaInformation([]);

            $this->createDocheaderActionButtons();
            $this->createShortcutButton();
        }
    }

    protected function createDocheaderActionButtons(): void
    {
        if (!in_array($this->actionMethodName, ['indexAction', 'listHiddenTopicsAction', 'listHiddenPostsAction'], true)) {
            return;
        }

        $buttonBar = $this->view->getModuleTemplate()->getDocHeaderComponent()->getButtonBar();
        $uriBuilder = $this->controllerContext->getUriBuilder();

        $button = $buttonBar->makeLinkButton()
            ->setHref($uriBuilder->reset()->uriFor('index', [], 'Administration'))
            ->setTitle('Back')
            ->setIcon($this->view->getModuleTemplate()->getIconFactory()->getIcon('actions-view-go-back', Icon::SIZE_SMALL));
        $buttonBar->addButton($button, ButtonBar::BUTTON_POSITION_LEFT);
    }

    protected function createShortcutButton(): void
    {
        $buttonBar = $this->view->getModuleTemplate()->getDocHeaderComponent()->getButtonBar();

        // Shortcut
        $shortcutButton = $buttonBar->makeShortcutButton()
            ->setModuleName('web_PforumAdministration')
            ->setGetVariables(['route', 'module', 'id'])
            ->setDisplayName('Shortcut');
        $buttonBar->addButton($shortcutButton, ButtonBar::BUTTON_POSITION_RIGHT);
    }

    public function indexAction(): void
    {
    }

    public function listHiddenTopicsAction(): void
    {
        $this->view->assign('topics', $this->topicRepository->findAllHidden()->toArray());
    }

    public function listHiddenPostsAction(): void
    {
        $this->view->assign('posts', $this->postRepository->findAllHidden()->toArray());
    }

    /**
     * @param Topic $record
     */
    public function activateTopicAction(Topic $record): void
    {
        $record->setHidden(false);
        $this->topicRepository->update($record);
        $this->addFlashMessage(
            'Topic "' . $record->getTitle() . '" was activated.',
            'Topic activated',
            AbstractMessage::INFO
        );
        $this->redirect('listHiddenTopics');
    }

    /**
     * @param Post $record
     */
    public function activatePostAction(Post $record): void
    {
        $record->setHidden(false);
        $this->postRepository->update($record);
        $this->addFlashMessage(
            'Post "' . $record->getTitle() . '" was activated.',
            'Post activated',
            AbstractMessage::INFO
        );
        $this->redirect('listHiddenPosts');
    }

    protected function getBackendUser(): BackendUserAuthentication
    {
        return $GLOBALS['BE_USER'];
    }
}
