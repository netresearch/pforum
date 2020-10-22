<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'JWeiland.pforum',
    'Forum',
    [
        'Forum' => 'list, show',
        'Topic' => 'show, new, create, edit, update, delete, activate',
        'Post' => 'new, create, edit, update, delete, activate',
    ],
    // non-cacheable actions
    [
        'Topic' => 'create, update, delete, activate',
        'Post' => 'create, update, delete, activate',
    ]
);
