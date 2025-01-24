<?php

/**
 * This file is part of the package netresearch/pforum.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

defined('TYPO3') || exit('Access denied.');

call_user_func(static function () {
    ExtensionUtility::registerPlugin(
        'Pforum',
        'Forum',
        'LLL:EXT:pforum/Resources/Private/Language/locallang_db.xlf:plugin.pforum.title'
    );

    $GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist']['pforum_forum'] = 'pi_flexform';

    ExtensionManagementUtility::addPiFlexFormValue(
        'pforum_forum',
        'FILE:EXT:pforum/Configuration/FlexForms/Forum.xml'
    );
});
