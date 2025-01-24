<?php

/**
 * This file is part of the package netresearch/pforum.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

namespace JWeiland\Pforum\ViewHelper;

use Closure;
use JWeiland\Pforum\Service\FrontendUserAccessService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithContentArgumentAndRenderStatic;

/**
 * ViewHelper to simplify the condition to show create button for topics and posts.
 */
class IsCreateButtonAllowedViewHelper extends AbstractViewHelper
{
    use CompileWithContentArgumentAndRenderStatic;

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
     * @param array                     $arguments
     * @param Closure                   $renderChildrenClosure
     * @param RenderingContextInterface $renderingContext
     *
     * @return bool
     */
    public static function renderStatic(
        array $arguments,
        Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext,
    ): bool {
        return GeneralUtility::makeInstance(FrontendUserAccessService::class)
            ->accessCheck(
                (int) $arguments['authType'],
                (int) $arguments['userGroupUid']
            );
    }
}
