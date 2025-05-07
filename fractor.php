<?php
/**
 * This file is part of the package jweiland/pforum.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

return \a9f\Fractor\Configuration\FractorConfiguration::configure()
    ->withPaths(
        [
            __DIR__ . '/Classes',
            __DIR__ . '/Configuration',
            __DIR__ . '/Resources',
            __DIR__ . '/ext_*',
        ]
    )->withSets(
        [\a9f\Typo3Fractor\Set\Typo3LevelSetList::UP_TO_TYPO3_13]
    );
