<?php

/**
 * This file is part of the package netresearch/pforum.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

$EM_CONF['pforum'] = [
    'title'          => 'Forum',
    'description'    => 'Lightweight forum extension for TYPO3',
    'category'       => 'plugin',
    'author'         => 'Stefan Froemken',
    'author_email'   => 'sfroemken@jweiland.net',
    'author_company' => 'jweiland.net',
    'state'          => 'stable',
    'version'        => '5.0.1',
    'constraints'    => [
        'depends' => [
            'typo3' => '11.0.0-11.5.99',
        ],
        'conflicts' => [
        ],
        'suggests' => [
        ],
    ],
];
