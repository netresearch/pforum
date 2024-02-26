<?php

/**
 * This file is part of the package jweiland/pforum.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

return [
    'ctrl'     => [
        'title'                    => 'LLL:EXT:pforum/Resources/Private/Language/locallang_db.xlf:tx_pforum_domain_model_topic',
        'label'                    => 'title',
        'tstamp'                   => 'tstamp',
        'crdate'                   => 'crdate',
        'cruser_id'                => 'cruser_id',
        'delete'                   => 'deleted',
        'default_sortby'           => 'tstamp DESC',
        'versioningWS'             => true,
        'iconfile'                 => 'EXT:pforum/Resources/Public/Icons/tx_pforum_domain_model_topic.svg',
        'origUid'                  => 't3_origuid',
        'languageField'            => 'sys_language_uid',
        'transOrigPointerField'    => 'l10n_parent',
        'transOrigDiffSourceField' => 'l10n_diffsource',
        'translationSource'        => 'l10n_source',
        'searchFields'             => 'title,description,posts',
        'enablecolumns'            => [
            'disabled'  => 'hidden',
            'starttime' => 'starttime',
            'endtime'   => 'endtime',
        ],
    ],
    'types'    => [
        '1' => [
            'showitem' => '
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
                    --palette--;;standard,
                    --palette--;;posts,
                    --palette--;;users,
                    --palette--;;media,
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:language,
                    --palette--;;language,
                --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.tabs.access,
                    --palette--;;visibility,
                    --palette--;;access,
            ',
        ],
    ],
    'palettes' => [
        'standard'   => [
            'label'    => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:CType.div.standard',
            'showitem' => 'title, --linebreak--, description',
        ],
        'posts'   => [
            'showitem' => 'posts',
        ],
        'users'   => [
            'showitem' => 'anonymous_user, --linebreak--, frontend_user',
        ],
        'media'   => [
            'showitem' => 'images',
        ],
        'language'   => [
            'label'    => 'LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.palettes.language',
            'showitem' => 'sys_language_uid, l10n_parent',
        ],
        'visibility' => [
            'label'    => 'LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.palettes.visibility',
            'showitem' => 'hidden;LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.hidden',
        ],
        'access'     => [
            'label'    => 'LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.palettes.access',
            'showitem' => 'starttime;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:starttime_formlabel, endtime;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:endtime_formlabel',
        ],
    ],
    'columns'  => [
        'sys_language_uid' => [
            'exclude' => true,
            'label'   => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.language',
            'config'  => [
                'type' => 'language',
            ],
        ],
        'l10n_parent'      => [
            'displayCond' => 'FIELD:sys_language_uid:>:0',
            'label'       => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.l18n_parent',
            'config'      => [
                'type'                => 'select',
                'renderType'          => 'selectSingle',
                'items'               => [
                    [
                        '',
                        0,
                    ],
                ],
                'foreign_table'       => 'tx_pforum_domain_model_topic',
                'foreign_table_where' => 'AND tx_pforum_domain_model_topic.pid=###CURRENT_PID### AND tx_pforum_domain_model_topic.sys_language_uid IN (-1,0)',
                'default'             => 0,
            ],
        ],
        'l10n_diffsource'  => [
            'config' => [
                'type'    => 'passthrough',
                'default' => '',
            ],
        ],
        'hidden'           => [
            'exclude' => true,
            'label'   => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.hidden',
            'config'  => [
                'type'       => 'check',
                'renderType' => 'checkboxToggle',
                'default'    => 0,
                'items'      => [
                    [
                        0 => '',
                        1 => '',
                    ],
                ],
            ],
        ],
        'starttime'        => [
            'exclude' => true,
            'label'   => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.starttime',
            'config'  => [
                'type'       => 'input',
                'renderType' => 'inputDateTime',
                'eval'       => 'datetime,int',
                'default'    => 0,
                'behaviour'  => [
                    'allowLanguageSynchronization' => true,
                ],
            ],
        ],
        'endtime'          => [
            'exclude' => true,
            'label'   => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.endtime',
            'config'  => [
                'type'       => 'input',
                'renderType' => 'inputDateTime',
                'eval'       => 'datetime,int',
                'default'    => 0,
                'range'      => [
                    'upper' => mktime(
                        0,
                        0,
                        0,
                        1,
                        1,
                        2038
                    ),
                ],
                'behaviour'  => [
                    'allowLanguageSynchronization' => true,
                ],
            ],
        ],
        'crdate'           => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],
        'title'            => [
            'exclude' => true,
            'label'   => 'LLL:EXT:pforum/Resources/Private/Language/locallang_db.xlf:tx_pforum_domain_model_topic.title',
            'config'  => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim,required',
            ],
        ],
        'description'      => [
            'exclude' => true,
            'label'   => 'LLL:EXT:pforum/Resources/Private/Language/locallang_db.xlf:tx_pforum_domain_model_topic.description',
            'config'  => [
                'type' => 'text',
                'cols' => 30,
                'rows' => 10,
                'eval' => 'trim',
            ],
        ],
        'posts'            => [
            'exclude' => false,
            'label'   => 'LLL:EXT:pforum/Resources/Private/Language/locallang_db.xlf:tx_pforum_domain_model_topic.posts',
            'config'  => [
                'type'          => 'inline',
                'foreign_table' => 'tx_pforum_domain_model_post',
                'foreign_field' => 'topic',
                'maxitems'      => 9999,
                'appearance'    => [
                    'collapseAll'                     => true,
                    'levelLinksPosition'              => 'top',
                    'showSynchronizationLink'         => true,
                    'showPossibleLocalizationRecords' => true,
                    'showAllLocalizationLink'         => true,
                ],
            ],
        ],
        'anonymous_user'   => [
            'exclude' => false,
            'label'   => 'LLL:EXT:pforum/Resources/Private/Language/locallang_db.xlf:tx_pforum_domain_model_topic.user',
            'config'  => [
                'type'          => 'select',
                'renderType'    => 'selectSingle',
                'foreign_table' => 'tx_pforum_domain_model_anonymoususer',
                'minitems'      => 0,
                'maxitems'      => 1,
            ],
        ],
        'frontend_user'    => [
            'exclude' => false,
            'label'   => 'LLL:EXT:pforum/Resources/Private/Language/locallang_db.xlf:tx_pforum_domain_model_topic.user',
            'config'  => [
                'type'          => 'select',
                'renderType'    => 'selectSingle',
                'foreign_table' => 'fe_users',
                'items'         => [
                    [
                        '',
                        '',
                    ],
                ],
                'minitems'      => 0,
                'maxitems'      => 1,
            ],
        ],
        'images'           => [
            'exclude' => true,
            'label'   => 'LLL:EXT:pforum/Resources/Private/Language/locallang_db.xlf:tx_pforum_domain_model_topic.images',
            'config'  => ExtensionManagementUtility::getFileFieldTCAConfig(
                'images',
                [
                    'minitems' => 0,
                    'maxitems' => 5,
                ]
            ),
        ],
        'forum'            => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],
    ],
];
