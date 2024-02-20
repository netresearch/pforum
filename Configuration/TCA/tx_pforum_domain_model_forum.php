<?php

/**
 * This file is part of the package jweiland/pforum.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

return [
    'ctrl'     => [
        'title'                    => 'LLL:EXT:pforum/Resources/Private/Language/locallang_db.xlf:tx_pforum_domain_model_forum',
        'label'                    => 'title',
        'tstamp'                   => 'tstamp',
        'crdate'                   => 'crdate',
        'cruser_id'                => 'cruser_id',
        'sortby'                   => 'sorting',
        'versioningWS'             => true,
        'origUid'                  => 't3_origuid',
        'languageField'            => 'sys_language_uid',
        'transOrigPointerField'    => 'l10n_parent',
        'transOrigDiffSourceField' => 'l10n_diffsource',
        'delete'                   => 'deleted',
        'enablecolumns'            => [
            'disabled'  => 'hidden',
            'starttime' => 'starttime',
            'endtime'   => 'endtime',
        ],
        'searchFields'             => 'title,teaser,topics,',
        'iconfile'                 => 'EXT:pforum/Resources/Public/Icons/tx_pforum_domain_model_forum.svg',
    ],
    'types'    => [
        '1' => [
            'showitem' => '
            --palette--;;languageHidden, title, teaser, topics,
            --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.tabs.access, 
            --palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.palettes.access;access',
        ],
    ],
    'palettes' => [
        'languageHidden' => [
            'showitem' => 'sys_language_uid, l10n_parent, hidden',
        ],
        'access'         => [
            'showitem' => 'starttime;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:starttime_formlabel,endtime;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:endtime_formlabel',
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
                'foreign_table'       => 'tx_pforum_domain_model_forum',
                'foreign_table_where' => 'AND tx_pforum_domain_model_forum.pid=###CURRENT_PID### AND tx_pforum_domain_model_forum.sys_language_uid IN (-1,0)',
                'default'             => 0,
            ],
        ],
        'l10n_diffsource'  => [
            'config' => [
                'type'    => 'passthrough',
                'default' => '',
            ],
        ],
        't3ver_label'      => [
            'label'  => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.versionLabel',
            'config' => [
                'type' => 'input',
                'size' => '30',
                'max'  => '255',
            ],
        ],
        'hidden'           => [
            'exclude' => true,
            'label'   => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.hidden',
            'config'  => [
                'type'  => 'check',
                'items' => [
                    '1' => [
                        '0' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:hidden.I.0',
                    ],
                ],
            ],
        ],
        'starttime'        => [
            'exclude'      => true,
            'label'        => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.starttime',
            'config'       => [
                'type'       => 'input',
                'size'       => '13',
                'eval'       => 'datetime',
                'default'    => 0,
                'renderType' => 'inputDateTime',
            ],
            'l10n_mode'    => 'exclude',
            'l10n_display' => 'defaultAsReadonly',
        ],
        'endtime'          => [
            'exclude'      => true,
            'label'        => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.endtime',
            'config'       => [
                'type'       => 'input',
                'size'       => '13',
                'eval'       => 'datetime',
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
                'renderType' => 'inputDateTime',
            ],
            'l10n_mode'    => 'exclude',
            'l10n_display' => 'defaultAsReadonly',
        ],
        'title'            => [
            'exclude' => true,
            'label'   => 'LLL:EXT:pforum/Resources/Private/Language/locallang_db.xlf:tx_pforum_domain_model_forum.title',
            'config'  => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim,required',
            ],
        ],
        'teaser'           => [
            'exclude' => true,
            'label'   => 'LLL:EXT:pforum/Resources/Private/Language/locallang_db.xlf:tx_pforum_domain_model_forum.teaser',
            'config'  => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'topics'           => [
            'exclude' => 0,
            'label'   => 'LLL:EXT:pforum/Resources/Private/Language/locallang_db.xlf:tx_pforum_domain_model_forum.topics',
            'config'  => [
                'type'          => 'inline',
                'foreign_table' => 'tx_pforum_domain_model_topic',
                'foreign_field' => 'forum',
                'maxitems'      => 9999,
                'appearance'    => [
                    'collapseAll'                     => 0,
                    'levelLinksPosition'              => 'top',
                    'showSynchronizationLink'         => 1,
                    'showPossibleLocalizationRecords' => 1,
                    'showAllLocalizationLink'         => 1,
                ],
            ],
        ],
    ],
];
