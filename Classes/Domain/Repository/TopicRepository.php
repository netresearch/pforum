<?php

/**
 * This file is part of the package netresearch/pforum.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

namespace JWeiland\Pforum\Domain\Repository;

use JWeiland\Pforum\Domain\Model\Forum;
use JWeiland\Pforum\Domain\Model\Topic;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;

/**
 * Repo to retrieve records for topics.
 *
 * @method QueryResultInterface findByForum(Forum $forum)
 */
class TopicRepository extends Repository implements HiddenRepositoryInterface
{
    /**
     * @var array
     */
    protected $defaultOrderings = [
        'crdate' => QueryInterface::ORDER_DESCENDING,
    ];

    public function findAllHidden(): QueryResultInterface
    {
        $query = $this->createQuery();
        $query->setOrderings([
            'title'       => QueryInterface::ORDER_ASCENDING,
            'description' => QueryInterface::ORDER_ASCENDING,
        ]);

        return $query->matching($query->equals('hidden', 1))->execute();
    }

    /**
     * @param mixed $value
     *
     * @return Topic|null
     */
    public function findHiddenObject($value, string $property = 'uid'): ?Topic
    {
        $query = $this->createQuery();
        $query->getQuerySettings()->setIgnoreEnableFields(true);
        $query->getQuerySettings()->setEnableFieldsToBeIgnored(['disabled']);
        $query->getQuerySettings()->setRespectStoragePage(false);

        $firstObject = $query->matching($query->equals($property, $value))->execute()->getFirst();
        if ($firstObject instanceof Topic) {
            return $firstObject;
        }

        return null;
    }
}
