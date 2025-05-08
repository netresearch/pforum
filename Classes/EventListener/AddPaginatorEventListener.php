<?php

/**
 * This file is part of the package netresearch/pforum.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

namespace JWeiland\Pforum\EventListener;

use JWeiland\Pforum\Event\PostProcessFluidVariablesEvent;
use TYPO3\CMS\Core\Pagination\SimplePagination;
use TYPO3\CMS\Core\Utility\MathUtility;
use TYPO3\CMS\Extbase\Pagination\QueryResultPaginator;

class AddPaginatorEventListener extends AbstractControllerEventListener
{
    /**
     * @var int
     */
    protected $itemsPerPage = 15;

    protected $allowedControllerActions = [
        'Forum' => [
            'show',
        ],
        'Topic' => [
            'show',
        ],
    ];

    public function __invoke(PostProcessFluidVariablesEvent $event): void
    {
        $typeOfPaginatedItems = 'topics';
        if ($event->getControllerName() === 'Topic') {
            $typeOfPaginatedItems = 'posts';
        }

        if ($this->isValidRequest($event)) {
            $paginator = new QueryResultPaginator(
                $event->getFluidVariables()[$typeOfPaginatedItems],
                $this->getCurrentPage($event),
                $this->getItemsPerPage($event)
            );

            $event->addFluidVariable('actionName', $event->getActionName());
            $event->addFluidVariable('paginator', $paginator);
            $event->addFluidVariable($typeOfPaginatedItems, $paginator->getPaginatedItems());
            $event->addFluidVariable('pagination', new SimplePagination($paginator));
        }
    }

    protected function getCurrentPage(PostProcessFluidVariablesEvent $event): int
    {
        if ($event->getRequest()->hasArgument('currentPage')) {
            return MathUtility::forceIntegerInRange(
                (int) $event->getRequest()->getArgument('currentPage'),
                1
            );
        }

        return 1;
    }

    protected function getItemsPerPage(PostProcessFluidVariablesEvent $event): int
    {
        $itemsPerPage = $event->getSettings()['pageBrowser']['itemsPerPage'] ?? $this->itemsPerPage;

        return (int) $itemsPerPage;
    }
}
