<?php

if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    'JWeiland.'.$_EXTKEY,
    'Forum',
    'LLL:EXT:pforum/Resources/Private/Language/locallang_db.xlf:plugin.title'
);

if (TYPO3_MODE === 'BE') {
    /*
     * Registers a Backend Module
     */
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
        'JWeiland.'.$_EXTKEY,
        'web',     // Make module a submodule of 'web'
        'forum',    // Submodule key
        '',                        // Position
        [
            'Forum' => 'listHidden, activate',
        ],
        [
            'access' => 'user,group',
            'icon' => 'EXT:'.$_EXTKEY.'/Resources/Public/Icons/module.svg',
            'labels' => 'LLL:EXT:'.$_EXTKEY.'/Resources/Private/Language/locallang_forum.xlf',
        ]
    );
}

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_pforum_domain_model_forum', 'EXT:pforum/Resources/Private/Language/locallang_csh_tx_pforum_domain_model_forum.xlf');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_pforum_domain_model_forum');

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_pforum_domain_model_topic', 'EXT:pforum/Resources/Private/Language/locallang_csh_tx_pforum_domain_model_topic.xlf');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_pforum_domain_model_topic');

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_pforum_domain_model_post', 'EXT:pforum/Resources/Private/Language/locallang_csh_tx_pforum_domain_model_post.xlf');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_pforum_domain_model_post');

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_pforum_domain_model_anonymoususer', 'EXT:pforum/Resources/Private/Language/locallang_csh_tx_pforum_domain_model_anonymoususer.xlf');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_pforum_domain_model_anonymoususer');
