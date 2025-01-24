<?php

/**
 * This file is part of the package netresearch/pforum.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

namespace JWeiland\Pforum\Helper;

use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

/**
 * Helper to check FE groups for existing UID.
 */
class FrontendGroupHelper
{
    public function uidExistsInGroupData(int $groupUid): bool
    {
        if ($groupUid === 0) {
            return false;
        }

        return in_array($groupUid, $this->getGroupUidsOfCurrentUser(), true);
    }

    protected function getGroupUidsOfCurrentUser(): array
    {
        $groupUids = $this->getTypoScriptFrontendController()->fe_user->groupData['uid'];
        if (!is_array($groupUids)) {
            return [];
        }

        return array_map('intval', $groupUids);
    }

    protected function getTypoScriptFrontendController(): TypoScriptFrontendController
    {
        return $GLOBALS['TSFE'];
    }
}
