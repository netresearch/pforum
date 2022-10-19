<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
    'pforum',
    'web',
    'administration',
    '',
    [
        \JWeiland\Pforum\Controller\AdministrationController::class => 'listHidden, activateTopic, activatePost',
    ],
    [
        'access' => 'user,group',
        'icon' => 'EXT:pforum/Resources/Public/Icons/module.svg',
        'labels' => 'LLL:EXT:pforum/Resources/Private/Language/locallang_mod_administration.xlf',
    ]
);
