<?php

/**
 * This file is part of the package netresearch/pforum.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

namespace JWeiland\Pforum\Helper;

use TYPO3\CMS\Extbase\Mvc\Request;
use TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication;

use function in_array;
use function is_array;

/**
 * Helper to check FE groups for existing UID.
 */
class FrontendGroupHelper
{
    /**
     * @var Request
     */
    private Request $request;

    /**
     * @param Request $request
     *
     * @return FrontendGroupHelper
     */
    public function setRequest(Request $request): FrontendGroupHelper
    {
        $this->request = $request;
        return $this;
    }

    /**
     * @param int $groupUid
     *
     * @return bool
     */
    public function uidExistsInGroupData(int $groupUid): bool
    {
        if ($groupUid === 0) {
            return false;
        }

        return in_array($groupUid, $this->getGroupUidsOfCurrentUser(), true);
    }

    /**
     * @return array
     */
    protected function getGroupUidsOfCurrentUser(): array
    {
        $groupUids = $this->getFrontendUserAuthentication()->groupData['uid'];

        if (!is_array($groupUids)) {
            return [];
        }

        return array_map('\intval', $groupUids);
    }

    /**
     * @return FrontendUserAuthentication
     */
    private function getFrontendUserAuthentication(): FrontendUserAuthentication
    {
        return $this->request->getAttribute('frontend.user');
    }
}
