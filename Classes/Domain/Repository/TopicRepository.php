<?php
namespace JWeiland\Pforum\Domain\Repository;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use JWeiland\Pforum\Domain\Model\Topic;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;

/**
 * Class TopicRepository
 *
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class TopicRepository extends Repository
{
    /**
     * @var array
     */
    protected $defaultOrderings = array(
        'crdate' => QueryInterface::ORDER_DESCENDING,
    );

    /**
     * find all hidden topics.
     *
     * @return \TYPO3\CMS\Extbase\Persistence\QueryResultInterface
     */
    public function findAllHidden()
    {
        $query = $this->createQuery();

        return $query->matching($query->equals('hidden', 1))->execute();
    }

    /**
     * find topic by uid whether it is hidden or not.
     *
     * @param int $topicUid
     *
     * @return Topic
     */
    public function findHiddenEntryByUid($topicUid)
    {
        $query = $this->createQuery();
        $query->getQuerySettings()->setIgnoreEnableFields(true);
        $query->getQuerySettings()->setEnableFieldsToBeIgnored(array('disabled'));

        return $query->matching($query->equals('uid', (int) $topicUid))->execute()->getFirst();
    }
}
