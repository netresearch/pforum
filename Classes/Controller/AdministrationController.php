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
use JWeiland\Pforum\Property\TypeConverter\HiddenPostConverter;
use JWeiland\Pforum\Property\TypeConverter\HiddenTopicConverter;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Backend\Template\Components\ButtonBar;
use TYPO3\CMS\Backend\Template\ModuleTemplate;
use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Imaging\IconSize;
use TYPO3\CMS\Core\Type\ContextualFeedbackSeverity;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException;
use TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException;

use function in_array;

/**
 * Main controller to list and show postings/questions.
 */
class AdministrationController extends ActionController
{
    /**
     * @var ModuleTemplateFactory
     */
    private ModuleTemplateFactory $moduleTemplateFactory;

    /**
     * @var IconFactory
     */
    private IconFactory $iconFactory;

    /**
     * @var ModuleTemplate
     */
    protected ModuleTemplate $moduleTemplate;

    /**
     * @var TopicRepository
     */
    protected TopicRepository $topicRepository;

    /**
     * @var PostRepository
     */
    protected PostRepository $postRepository;

    /**
     * @param ModuleTemplateFactory $moduleTemplateFactory
     * @param IconFactory           $iconFactory
     * @param TopicRepository       $topicRepository
     * @param PostRepository        $postRepository
     */
    public function __construct(
        ModuleTemplateFactory $moduleTemplateFactory,
        IconFactory $iconFactory,
        TopicRepository $topicRepository,
        PostRepository $postRepository,
    ) {
        $this->moduleTemplateFactory = $moduleTemplateFactory;
        $this->iconFactory           = $iconFactory;
        $this->topicRepository       = $topicRepository;
        $this->postRepository        = $postRepository;
    }

    /**
     * Initialize action.
     *
     * @return void
     */
    protected function initializeAction(): void
    {
        parent::initializeAction();

        $this->moduleTemplate = $this->getModuleTemplate();

        $this->createDocheaderActionButtons();
        $this->createShortcutButton();
    }

    /**
     * Returns the module template instance.
     *
     * @return ModuleTemplate
     */
    private function getModuleTemplate(): ModuleTemplate
    {
        $moduleTemplate = $this->moduleTemplateFactory->create($this->request);
        $moduleTemplate->setBodyTag('<body class="typo3-module-pforum">');
        $moduleTemplate->setModuleId('typo3-module-pforum');

        return $moduleTemplate;
    }

    /**
     * @return void
     */
    protected function createDocheaderActionButtons(): void
    {
        if (!in_array(
            $this->actionMethodName,
            [
                'indexAction',
                'listHiddenTopicsAction',
                'listHiddenPostsAction',
            ],
            true
        )) {
            return;
        }

        $buttonBar = $this->moduleTemplate
            ->getDocHeaderComponent()
            ->getButtonBar();

        $button = $buttonBar
            ->makeLinkButton()
            ->setHref(
                $this->uriBuilder
                    ->reset()
                    ->setArguments([
                        'route' => 'pforum_module_administration',
                    ])
                    ->buildBackendUri()
            )
            ->setTitle('Back')
            ->setIcon(
                $this->iconFactory->getIcon(
                    'actions-view-go-back',
                    IconSize::SMALL
                )
            );

        $buttonBar->addButton($button);
    }

    protected function createShortcutButton(): void
    {
        $buttonBar = $this->moduleTemplate
            ->getDocHeaderComponent()
            ->getButtonBar();

        $pageId = (int) ($this->request->getQueryParams()['id'] ?? 0);

        // Shortcut
        $shortcutButton = $buttonBar
            ->makeShortcutButton()
            ->setRouteIdentifier('pforum_module_administration')
            ->setArguments([
                'id' => $pageId,
            ])
            ->setDisplayName('Shortcut');

        $buttonBar->addButton(
            $shortcutButton,
            ButtonBar::BUTTON_POSITION_RIGHT
        );
    }

    public function indexAction(): ResponseInterface
    {
        $this->moduleTemplate->assign(
            'content',
            $this->view->render('Administration/Index')
        );

        return $this->moduleTemplate->renderResponse('Backend/PforumAdministration');
    }

    public function listHiddenTopicsAction(): ResponseInterface
    {
        $this->view->assign('topics', $this->topicRepository->findAllHidden()->toArray());

        $this->moduleTemplate->assign(
            'content',
            $this->view->render('Administration/ListHiddenTopics')
        );

        return $this->moduleTemplate->renderResponse('Backend/PforumAdministration');
    }

    public function listHiddenPostsAction(): ResponseInterface
    {
        $this->view->assign('posts', $this->postRepository->findAllHidden()->toArray());

        $this->moduleTemplate->assign(
            'content',
            $this->view->render('Administration/ListHiddenPosts')
        );

        return $this->moduleTemplate->renderResponse('Backend/PforumAdministration');
    }

    /**
     * @return void
     */
    public function initializeActivateTopicAction(): void
    {
        $this->arguments
            ->getArgument('record')
            ->getPropertyMappingConfiguration()
            ->setTypeConverter(
                GeneralUtility::makeInstance(HiddenTopicConverter::class)
            );
    }

    /**
     * @param Topic $record
     *
     * @return ResponseInterface
     *
     * @throws IllegalObjectTypeException
     * @throws UnknownObjectException
     */
    public function activateTopicAction(Topic $record): ResponseInterface
    {
        $record->setHidden(false);

        $this->topicRepository->update($record);

        $this->addFlashMessage(
            'Topic "' . $record->getTitle() . '" was activated.',
            'Topic activated',
            ContextualFeedbackSeverity::INFO
        );

        return $this->redirect('listHiddenTopics');
    }

    /**
     * @return void
     */
    public function initializeActivatePostAction(): void
    {
        $this->arguments
            ->getArgument('record')
            ->getPropertyMappingConfiguration()
            ->setTypeConverter(
                GeneralUtility::makeInstance(HiddenPostConverter::class)
            );
    }

    /**
     * @param Post $record
     *
     * @return ResponseInterface
     *
     * @throws IllegalObjectTypeException
     * @throws UnknownObjectException
     */
    public function activatePostAction(Post $record): ResponseInterface
    {
        $record->setHidden(false);

        $this->postRepository->update($record);

        $this->addFlashMessage(
            'Post "' . $record->getTitle() . '" was activated.',
            'Post activated',
            ContextualFeedbackSeverity::INFO
        );

        return $this->redirect('listHiddenPosts');
    }
}
