<?php

/**
 * This file is part of the package jweiland/pforum.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
    'pforum',
    'web',
    'administration',
    '',
    [
        \JWeiland\Pforum\Controller\AdministrationController::class => 'index, listHiddenTopics, listHiddenPosts, activateTopic, activatePost',
    ],
    [
        'access' => 'user,group',
        'icon' => 'EXT:pforum/Resources/Public/Icons/module.svg',
        'labels' => 'LLL:EXT:pforum/Resources/Private/Language/locallang_mod_administration.xlf',
    ]
);
