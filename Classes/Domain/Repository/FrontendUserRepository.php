<?php

/**
 * This file is part of the package jweiland/pforum.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

namespace JWeiland\Pforum\Domain\Repository;

use JWeiland\Pforum\Domain\Model\FrontendUser;
use TYPO3\CMS\Extbase\Persistence\Repository;

/**
 * Repo to manage users of table fe_users.
 *
 * @extends Repository<FrontendUser>
 */
class FrontendUserRepository extends Repository
{
}
