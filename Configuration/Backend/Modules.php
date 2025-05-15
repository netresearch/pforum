<?php

/**
 * This file is part of the package jweiland/pforum.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

use JWeiland\Pforum\Controller\AdministrationController;

// Caution, variable name must not exist within \TYPO3\CMS\Core\Package\AbstractServiceProvider::configureBackendModules
return [
    'pforum_module' => [
        'labels'         => 'LLL:EXT:pforum/Resources/Private/Language/locallang_mod.xlf',
        'iconIdentifier' => 'extension-pforum-module',
        'position'       => [
            'after' => 'web',
        ],
    ],
    'pforum_module_administration' => [
        'parent'                                   => 'pforum_module',
        'position'                                 => [],
        'access'                                   => 'user',
        'iconIdentifier'                           => 'extension-pforum-module-administration',
        'path'                                     => '/module/pforum/administration',
        'labels'                                   => 'LLL:EXT:pforum/Resources/Private/Language/locallang_mod_administration.xlf',
        'inheritNavigationComponentFromMainModule' => false,
        'navigationComponent'                      => '@typo3/backend/tree/page-tree-element',

        // Extbase module configuration options
        'extensionName'     => 'Pforum',
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
