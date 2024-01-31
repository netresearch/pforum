<?php

/**
 * This file is part of the package jweiland/pforum.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

namespace JWeiland\Pforum\Service;

use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Context\Exception\AspectNotFoundException;
use TYPO3\CMS\Core\Context\UserAspect;
use TYPO3\CMS\Core\Utility\GeneralUtility;

use function in_array;

/**
 * Controller to manage (list and show) postings
 */
class FrontendUserAccessService
{
    /**
     * Returns TRUE if the current frontend user is allowed to access.
     *
     * @param int $authenticationType The authentication type (1 or 2)
     * @param int $userGroupUid       The desired frontend user group to check.
     *
     * @return bool
     */
    public function accessCheck(int $authenticationType, int $userGroupUid): bool
    {
        // Everyone can create new topics/posts
        if ($authenticationType === 1) {
            return true;
        }

        // Values other than 1 or 2 are not allowed
        if ($authenticationType !== 2) {
            return false;
        }

        $userAspect = $this->getUserAspect();

        if ($userAspect === null) {
            return false;
        }

        if (!$userAspect->isLoggedIn()) {
            return false;
        }

        return in_array(
            $userGroupUid,
            $userAspect->getGroupIds(),
            true
        );
    }

    /**
     * Returns the user aspect with information about a user.
     *
     * @return null|UserAspect
     */
    protected function getUserAspect(): ?UserAspect
    {
        try {
            return GeneralUtility::makeInstance(Context::class)
                ->getAspect('frontend.user');
        } catch (AspectNotFoundException) {
            return null;
        }
    }
}
