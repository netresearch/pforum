<?php
$EM_CONF[$_EXTKEY] = [
    'title' => 'Forum',
    'description' => 'Forum',
    'category' => 'plugin',
    'author' => 'Stefan Froemken',
    'author_email' => 'sfroemken@jweiland.net',
    'author_company' => 'jweiland.net',
    'state' => 'stable',
    'clearCacheOnLoad' => 0,
    'version' => '4.0.1',
    'constraints' => [
        'depends' => [
            'typo3' => '10.4.32-11.5.16',
        ],
        'conflicts' => [
        ],
        'suggests' => [
        ],
    ],
];
