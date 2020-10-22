<?php

if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
    'JWeiland.pforum',
    'web',
    'forum',
    '',
    [
        'Forum' => 'listHidden, activate',
    ],
    [
        'access' => 'user,group',
        'icon' => 'EXT:pforum/Resources/Public/Icons/module.svg',
        'labels' => 'LLL:EXT:pforum/Resources/Private/Language/locallang_forum.xlf',
    ]
);
