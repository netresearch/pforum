<?php

use JWeiland\Pforum\Controller\AdministrationController;

return [
    'web_pforumAdministration' => [
        'parent' => 'web',
        'access' => 'user',
        'labels' => 'LLL:EXT:pforum/Resources/Private/Language/locallang_mod_administration.xlf',
        'extensionName' => 'pforum',
        'controllerActions' => [
            AdministrationController::class => [
                'index',
                'listHiddenTopics',
                'listHiddenPosts',
                'activateTopic',
                'activatePost',
            ],
        ],
    ],
];
