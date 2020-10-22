<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

call_user_func(function () {
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

    // add pforum plugin to new element wizard
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig(
        '<INCLUDE_TYPOSCRIPT: source="FILE:EXT:pforum/Configuration/TSconfig/ContentElementWizard.txt">'
    );

    $iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
        \TYPO3\CMS\Core\Imaging\IconRegistry::class
    );
    $svgIcons = [
        'ext-pforum-wizard-icon' => 'plugin_wizard.svg',
    ];
    foreach ($svgIcons as $identifier => $fileName) {
        $iconRegistry->registerIcon(
            $identifier,
            \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
            ['source' => 'EXT:pforum/Resources/Public/Icons/' . $fileName]
        );
    }
});
