<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

call_user_func(static function (): void {
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        'pforum',
        'Forum',
        [
            \JWeiland\Pforum\Controller\ForumController::class => 'list, show',
            \JWeiland\Pforum\Controller\TopicController::class => 'show, new, create, edit, update, delete, activate',
            \JWeiland\Pforum\Controller\PostController::class => 'new, create, edit, update, delete, activate',
        ],
        // non-cacheable actions
        [
            \JWeiland\Pforum\Controller\TopicController::class => 'create, update, delete, activate',
            \JWeiland\Pforum\Controller\PostController::class => 'create, update, delete, activate',
        ]
    );

    // Add pforum plugin to new element wizard
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig(
        '<INCLUDE_TYPOSCRIPT: source="FILE:EXT:pforum/Configuration/TSconfig/ContentElementWizard.tsconfig">'
    );

    $iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
        \TYPO3\CMS\Core\Imaging\IconRegistry::class
    );
    $svgIcons = [
        'ext-pforum-wizard-icon' => 'module.svg',
        'ext-pforum-table-forum' => 'tx_pforum_domain_model_forum.svg',
        'ext-pforum-table-topic' => 'tx_pforum_domain_model_topic.svg',
        'ext-pforum-table-post' => 'tx_pforum_domain_model_post.svg',
    ];
    foreach ($svgIcons as $identifier => $fileName) {
        $iconRegistry->registerIcon(
            $identifier,
            \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
            ['source' => 'EXT:pforum/Resources/Public/Icons/' . $fileName]
        );
    }
});
