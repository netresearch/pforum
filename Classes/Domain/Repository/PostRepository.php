<?php
namespace JWeiland\Pforum\Domain\Repository;

/*
 * This file is part of the package jweiland/pforum.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use JWeiland\Pforum\Domain\Model\Post;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;

/**
 * Class PostRepository
 *
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class PostRepository extends Repository
{
    /**
     * @var array
     */
    protected $defaultOrderings = [
        'crdate' => QueryInterface::ORDER_DESCENDING,
    ];

    /**
     * find all hidden posts.
     *
     * @return \TYPO3\CMS\Extbase\Persistence\QueryResultInterface
     */
    public function findAllHidden()
    {
        $query = $this->createQuery();

        return $query->matching($query->equals('hidden', 1))->execute();
    }

    /**
     * find post by uid whether it is hidden or not.
     *
     * @param int $postUid
     *
     * @return Post
     */
    public function findHiddenEntryByUid($postUid)
    {
        $query = $this->createQuery();
        $query->getQuerySettings()->setIgnoreEnableFields(true);
        $query->getQuerySettings()->setEnableFieldsToBeIgnored(['disabled']);

        return $query->matching($query->equals('uid', (int) $postUid))->execute()->getFirst();
    }
}
