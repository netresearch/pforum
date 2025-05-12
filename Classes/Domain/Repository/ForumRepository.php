<?php

/**
 * This file is part of the package netresearch/pforum.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

namespace JWeiland\Pforum\Domain\Repository;

use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;

/**
 * Main Repo to manage forum records.
 */
class ForumRepository extends Repository
{
    /**
     * @var array<string, string>
     */
    protected $defaultOrderings = [
        'sorting' => QueryInterface::ORDER_ASCENDING,
    ];

    /**
     * @return QueryResultInterface
     */
    public function findAllNotArchived(): QueryResultInterface
    {
        $query = $this->createQuery();
        $query->getQuerySettings()
            ->setIgnoreEnableFields(true)
            ->setEnableFieldsToBeIgnored(['disabled'])
            ->setRespectStoragePage(false);

        return $query
            ->matching(
                $query->equals('archived', 0)
            )
            ->execute();
    }
}
