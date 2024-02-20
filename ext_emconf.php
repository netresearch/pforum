<?php

/**
 * This file is part of the package jweiland/pforum.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

$EM_CONF[$_EXTKEY] = [
    'title' => 'Forum',
    'description' => 'Forum',
    'category' => 'plugin',
    'author' => 'Stefan Froemken',
    'author_email' => 'sfroemken@jweiland.net',
    'author_company' => 'jweiland.net',
    'state' => 'stable',
    'clearCacheOnLoad' => 0,
    'version' => '4.0.3',
    'constraints' => [
        'depends' => [
            'typo3' => '10.4.32-11.5.99',
        ],
        'conflicts' => [
        ],
        'suggests' => [
        ],
    ],
];
