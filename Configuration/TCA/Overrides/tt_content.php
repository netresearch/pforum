<?php
// load tt_content to $TCA array and add flexform
$pluginSignature = 'pforum_forum';
$TCA['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue($pluginSignature, 'FILE:EXT:pforum/Configuration/FlexForms/Forum.xml');
