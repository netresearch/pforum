<?php

/**
 * This file is part of the package netresearch/pforum.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

defined('TYPO3') || exit('Access denied.');

call_user_func(static function () {
    ExtensionManagementUtility::addStaticFile(
        'pforum',
        'Configuration/TypoScript/',
        'Forum'
    );
});
