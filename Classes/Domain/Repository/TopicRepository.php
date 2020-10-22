<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/pforum.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Pforum\Domain\Repository;

use JWeiland\Pforum\Domain\Model\Topic;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;

/**
 * Repo to retrieve records for topics
 */
class TopicRepository extends Repository
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

        return $query->matching($query->equals('hidden', 1))->execute();
    }

    public function findHiddenEntryByUid(int $topicUid): Topic
    {
        $query = $this->createQuery();
        $query->getQuerySettings()->setIgnoreEnableFields(true);
        $query->getQuerySettings()->setEnableFieldsToBeIgnored(['disabled']);

        /** @var Topic $topic */
        $topic = $query->matching($query->equals('uid', $topicUid))->execute()->getFirst();
        return $topic;
    }
}
