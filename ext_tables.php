<?php

/**
 * This file is part of the package jweiland/pforum.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

use JWeiland\Pforum\Controller\AdministrationController;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

defined('TYPO3') || die('Access denied.');

call_user_func(static function (): void {
    ExtensionUtility::registerModule(
        'pforum',
        'web',
        'administration',
        '',
        [
            AdministrationController::class => 'index, listHiddenTopics, listHiddenPosts, activateTopic, activatePost',
        ],
        [
            'access' => 'user,group',
            'icon'   => 'EXT:pforum/Resources/Public/Icons/module.svg',
            'labels' => 'LLL:EXT:pforum/Resources/Private/Language/locallang_mod_administration.xlf',
        ]
    );
});
