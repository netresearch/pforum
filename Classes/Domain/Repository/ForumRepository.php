<?php

/**
 * This file is part of the package jweiland/pforum.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

namespace JWeiland\Pforum\Domain\Repository;

use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;

/**
 * Main Repo to manage forum records
 */
class ForumRepository extends Repository
{
    /**
     * @var array
     */
    protected $defaultOrderings = [
        'sorting' => QueryInterface::ORDER_ASCENDING,
    ];
}
