<?php

if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    'JWeiland.'.$_EXTKEY,
    'Forum',
    'LLL:EXT:pforum/Resources/Private/Language/locallang_db.xlf:plugin.title'
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile($_EXTKEY, 'Configuration/TypoScript', 'Forum');

if (TYPO3_MODE === 'BE') {
    /*
     * Registers a Backend Module
     */
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
        'JWeiland.'.$_EXTKEY,
        'web',     // Make module a submodule of 'web'
        'forum',    // Submodule key
        '',                        // Position
        array(
            'Forum' => 'listHidden, activate',
        ),
        array(
            'access' => 'user,group',
            'icon' => 'EXT:'.$_EXTKEY.'/ext_icon.gif',
            'labels' => 'LLL:EXT:'.$_EXTKEY.'/Resources/Private/Language/locallang_forum.xlf',
        )
    );
}

// load tt_content to $TCA array and add flexform
$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY.'_forum'] = 'pi_flexform';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue($_EXTKEY.'_forum', 'FILE:EXT:'.$_EXTKEY.'/Configuration/FlexForms/Forum.xml');

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_pforum_domain_model_forum', 'EXT:pforum/Resources/Private/Language/locallang_csh_tx_pforum_domain_model_forum.xlf');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_pforum_domain_model_forum');
$TCA['tx_pforum_domain_model_forum'] = array(
    'ctrl' => array(
        'title' => 'LLL:EXT:pforum/Resources/Private/Language/locallang_db.xlf:tx_pforum_domain_model_forum',
        'label' => 'title',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'dividers2tabs' => true,
        'sortby' => 'sorting',
        'versioningWS' => 2,
        'versioning_followPages' => true,
        'origUid' => 't3_origuid',
        'languageField' => 'sys_language_uid',
        'transOrigPointerField' => 'l10n_parent',
        'transOrigDiffSourceField' => 'l10n_diffsource',
        'delete' => 'deleted',
        'enablecolumns' => array(
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime',
        ),
        'searchFields' => 'title,teaser,topics,',
        'dynamicConfigFile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY).'Configuration/TCA/Forum.php',
        'iconfile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath($_EXTKEY).'Resources/Public/Icons/tx_pforum_domain_model_forum.gif',
    ),
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_pforum_domain_model_topic', 'EXT:pforum/Resources/Private/Language/locallang_csh_tx_pforum_domain_model_topic.xlf');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_pforum_domain_model_topic');
$TCA['tx_pforum_domain_model_topic'] = array(
    'ctrl' => array(
        'title' => 'LLL:EXT:pforum/Resources/Private/Language/locallang_db.xlf:tx_pforum_domain_model_topic',
        'label' => 'title',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'dividers2tabs' => true,

        'versioningWS' => 2,
        'versioning_followPages' => true,
        'origUid' => 't3_origuid',
        'languageField' => 'sys_language_uid',
        'transOrigPointerField' => 'l10n_parent',
        'transOrigDiffSourceField' => 'l10n_diffsource',
        'delete' => 'deleted',
        'enablecolumns' => array(
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime',
        ),
        'searchFields' => 'title,description,posts,user,',
        'dynamicConfigFile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY).'Configuration/TCA/Topic.php',
        'iconfile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath($_EXTKEY).'Resources/Public/Icons/tx_pforum_domain_model_topic.gif',
    ),
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_pforum_domain_model_post', 'EXT:pforum/Resources/Private/Language/locallang_csh_tx_pforum_domain_model_post.xlf');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_pforum_domain_model_post');
$TCA['tx_pforum_domain_model_post'] = array(
    'ctrl' => array(
        'title' => 'LLL:EXT:pforum/Resources/Private/Language/locallang_db.xlf:tx_pforum_domain_model_post',
        'label' => 'title',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'dividers2tabs' => true,

        'versioningWS' => 2,
        'versioning_followPages' => true,
        'origUid' => 't3_origuid',
        'languageField' => 'sys_language_uid',
        'transOrigPointerField' => 'l10n_parent',
        'transOrigDiffSourceField' => 'l10n_diffsource',
        'delete' => 'deleted',
        'enablecolumns' => array(
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime',
        ),
        'searchFields' => 'title,description,user,',
        'dynamicConfigFile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY).'Configuration/TCA/Post.php',
        'iconfile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath($_EXTKEY).'Resources/Public/Icons/tx_pforum_domain_model_post.gif',
    ),
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_pforum_domain_model_anonymoususer', 'EXT:pforum/Resources/Private/Language/locallang_csh_tx_pforum_domain_model_anonymoususer.xlf');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_pforum_domain_model_anonymoususer');
$TCA['tx_pforum_domain_model_anonymoususer'] = array(
    'ctrl' => array(
        'title' => 'LLL:EXT:pforum/Resources/Private/Language/locallang_db.xlf:tx_pforum_domain_model_anonymoususer',
        'label' => 'name',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'dividers2tabs' => true,

        'versioningWS' => 2,
        'versioning_followPages' => true,
        'origUid' => 't3_origuid',
        'languageField' => 'sys_language_uid',
        'transOrigPointerField' => 'l10n_parent',
        'transOrigDiffSourceField' => 'l10n_diffsource',
        'delete' => 'deleted',
        'enablecolumns' => array(
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime',
        ),
        'searchFields' => 'name,username,email',
        'dynamicConfigFile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY).'Configuration/TCA/Anonymoususer.php',
        'iconfile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath($_EXTKEY).'Resources/Public/Icons/tx_pforum_domain_model_anonymoususer.gif',
    ),
);
