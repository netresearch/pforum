<?php

/**
 * This file is part of the package netresearch/pforum.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

namespace JWeiland\Pforum\ViewHelper;

use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Extends the existing fluid script view helper to set dependencies between scripts.
 *
 * @author  Axel Seemann <axel.seemann@netresearch.de>
 * @license Netresearch https://www.netresearch.de
 * @link    https://www.netresearch.de
 */
class NumerusWordViewHelper extends AbstractViewHelper
{
    public function initializeArguments(): void
    {
        $this->registerArgument('number', 'int', 'The content to count the words for', true);
        $this->registerArgument('singular', 'string', 'The singular form of the word', true);
        $this->registerArgument('plural', 'string', 'The plural form of the word', true);
    }

    public function render(): string
    {
        $number   = $this->arguments['number'];
        $singular = $this->arguments['singular'];
        $plural   = $this->arguments['plural'];

        if ($number === 0) {
            return LocalizationUtility::translate('LLL:EXT:pforum/Resources/Private/Language/locallang.xlf:numword.no') . ' ' . $plural;
        }

        if ($number === 1) {
            return $number . ' ' . $singular;
        }

        return $number . ' ' . $plural;
    }
}
