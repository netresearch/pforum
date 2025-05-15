<?php

/**
 * This file is part of the package jweiland/pforum.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

namespace JWeiland\Pforum\ViewHelpers;

use JWeiland\Pforum\Service\FrontendUserAccessService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * ViewHelper to simplify the condition to show create button for topics and posts.
 */
class IsCreateButtonAllowedViewHelper extends AbstractViewHelper
{
    /**
     * Initialize the arguments.
     *
     * @return void
     */
    public function initializeArguments(): void
    {
        parent::initializeArguments();

        $this->registerArgument(
            'authType',
            'int',
            'The authentication type. 1 = None, 2 = Needs authentication.'
        );

        $this->registerArgument(
            'userGroupUid',
            'int',
            'The usergroup UID.'
        );
    }

    /**
     * @return bool
     */
    public function render(): bool
    {
        return GeneralUtility::makeInstance(FrontendUserAccessService::class)
            ->accessCheck(
                (int) $this->arguments['authType'],
                (int) $this->arguments['userGroupUid']
            );
    }
}
