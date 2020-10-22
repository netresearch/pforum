<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    'JWeiland.pforum',
    'Forum',
    'LLL:EXT:pforum/Resources/Private/Language/locallang_db.xlf:plugin.title'
);

$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist']['pforum_forum'] = 'pi_flexform';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
    'pforum_forum',
    'FILE:EXT:pforum/Configuration/FlexForms/Forum.xml'
);
