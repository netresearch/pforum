<?php

/**
 * This file is part of the package jweiland/pforum.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

namespace JWeiland\Pforum\Domain\Repository;

use JWeiland\Pforum\Domain\Model\Post;
use JWeiland\Pforum\Domain\Model\Topic;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

/**
 * Repo to retrieve records for postings
 */
class PostRepository extends Repository implements HiddenRepositoryInterface
{
    /**
     * @var array
     */
    protected $defaultOrderings = [
        'crdate' => QueryInterface::ORDER_DESCENDING,
    ];

    /**
     * Always return hidden and deleted records from this Repository
     */
    public function initializeObject(): void
    {
        $querySettings = GeneralUtility::makeInstance(Typo3QuerySettings::class);

        // Ignore hidden and deleted records
        $querySettings
            ->setRespectStoragePage(false)
            ->setIgnoreEnableFields(true)
            ->setIncludeDeleted(true);

        $this->setDefaultQuerySettings($querySettings);
    }

    /**
     * @param Topic $topic
     *
     * @return QueryResultInterface
     */
    public function findByTopic(Topic $topic): QueryResultInterface
    {
        $query = $this->createQuery();
        $query->getQuerySettings()->setIgnoreEnableFields(true);
        $query->getQuerySettings()->setEnableFieldsToBeIgnored(['disabled']);
        $query->getQuerySettings()->setRespectStoragePage(false);

        return $query
            ->matching(
                $query->logicalAnd(
//                    $query->equals('deleted', 1),
                    $query->equals('topic', $topic)
                )
            )
            ->execute();
    }

    public function findAllHidden(): QueryResultInterface
    {
        $query = $this->createQuery();
        $query->setOrderings([
            'title' => QueryInterface::ORDER_ASCENDING,
            'description' => QueryInterface::ORDER_ASCENDING
        ]);

        return $query->matching($query->equals('hidden', 1))->execute();
    }

    /**
     * @param mixed $value
     */
    public function findHiddenObject($value, string $property = 'uid'): ?Post
    {
        $query = $this->createQuery();
        $query->getQuerySettings()->setIgnoreEnableFields(true);
        $query->getQuerySettings()->setEnableFieldsToBeIgnored(['disabled']);
        $query->getQuerySettings()->setRespectStoragePage(false);

        $firstObject = $query->matching($query->equals($property, $value))->execute()->getFirst();
        if ($firstObject instanceof Post) {
            return $firstObject;
        }

        return null;
    }
}
