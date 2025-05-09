<?php

/**
 * This file is part of the package netresearch/pforum.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

use JWeiland\Pforum\Controller\ForumController;
use JWeiland\Pforum\Controller\PostController;
use JWeiland\Pforum\Controller\TopicController;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

defined('TYPO3') || exit('Access denied.');

call_user_func(static function (): void {
    ExtensionUtility::configurePlugin(
        'pforum',
        'Forum',
        [
            ForumController::class => 'list, show',
            TopicController::class => 'show, new, create, edit, update, delete, activate',
            PostController::class  => 'new, create, edit, update, delete, activate',
        ],
        // non-cacheable actions
        [
            TopicController::class => 'create, update, delete, activate',
            PostController::class  => 'create, update, delete, activate',
        ]
    );

    $GLOBALS['TYPO3_CONF_VARS']['MAIL']['templateRootPaths'][1_666_352_112] = 'EXT:pforum/Resources/Private/Templates/Mail';
});
