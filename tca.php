<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

$TCA['tx_x4epersdb_function'] = Array (
	'ctrl' => $TCA['tx_x4epersdb_function']['ctrl'],
	'interface' => Array (
		'showRecordFieldList' => 'hidden,title,sys_language_uid,l18n_parent,l18n_diffsource'
	),
	'feInterface' => $TCA['tx_x4epersdb_function']['feInterface'],
	'columns' => Array (
        'sys_language_uid' => array (
            'exclude' => 1,
            'label'  => 'LLL:EXT:lang/locallang_general.xml:LGL.language',
            'config' => array (
                'type'                => 'select',
                'foreign_table'       => 'sys_language',
                'foreign_table_where' => 'ORDER BY sys_language.title',
                'items' => array(
                    array('LLL:EXT:lang/locallang_general.xml:LGL.allLanguages', -1),
                    array('LLL:EXT:lang/locallang_general.xml:LGL.default_value', 0)
                )
            )
        ),
        'l18n_parent' => array (
            'displayCond' => 'FIELD:sys_language_uid:>:0',
            'exclude'     => 1,
            'label'       => 'LLL:EXT:lang/locallang_general.xml:LGL.l18n_parent',
            'config'      => array (
                'type'  => 'select',
                'items' => array (
                    array('', 0),
                ),
                'foreign_table'       => 'tx_x4epersdb_function',
                'foreign_table_where' => 'AND tx_x4epersdb_function.pid=###CURRENT_PID### AND tx_x4epersdb_function.sys_language_uid IN (-1,0)',
            )
        ),
        'l18n_diffsource' => array (
            'config' => array (
                'type' => 'passthrough'
            )
        ),
		'hidden' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.php:LGL.hidden',
			'config' => Array (
				'type' => 'check',
				'default' => '0'
			)
		),
		'title' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:x4epersdb/locallang_db.php:tx_x4epersdb_function.title',
			'config' => Array (
				'type' => 'input',
				'size' => '30',
				'eval' => 'required',
			)
		),
	),
	'types' => Array (
		'0' => Array('showitem' => 'sys_language_uid;;;;1-1-1,l18n_parent,l18n_diffsource,hidden;;1;;1-1-1, title;;;;2-2-2')
	),
	'palettes' => Array (
		'1' => Array('showitem' => '')
	)
);

$TCA['tx_x4epersdb_department'] = Array (
	'ctrl' => $TCA['tx_x4epersdb_department']['ctrl'],
	'interface' => Array (
		'showRecordFieldList' => 'hidden,title'
	),
	'feInterface' => $TCA['tx_x4epersdb_department']['feInterface'],
	'columns' => Array (
		'hidden' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.php:LGL.hidden',
			'config' => Array (
				'type' => 'check',
				'default' => '0'
			)
		),
		'title' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:x4epersdb/locallang_db.php:tx_x4epersdb_department.title',
			'config' => Array (
				'type' => 'input',
				'size' => '30',
				'eval' => 'required',
			)
		),

	),
	'types' => Array (
		'0' => Array('showitem' => 'hidden;;1;;1-1-1, title;;;;2-2-2')
	),
	'palettes' => Array (
		'1' => Array('showitem' => '')
	)
);

$TCA['tx_x4epersdb_institutes'] = Array (
	'ctrl' => $TCA['tx_x4epersdb_institutes']['ctrl'],
	'interface' => Array (
		'showRecordFieldList' => 'hidden,title'
	),
	'feInterface' => $TCA['tx_x4epersdb_department']['feInterface'],
	'columns' => Array (
		'hidden' => Array (
			'exclude' => 0,
			'label' => 'LLL:EXT:lang/locallang_general.php:LGL.hidden',
			'config' => Array (
				'type' => 'check',
				'default' => '0'
			)
		),
		'title' => Array (
			'exclude' => 0,
			'label' => 'LLL:EXT:x4epersdb/locallang_db.php:tx_x4epersdb_institutes.title',
			'config' => Array (
				'type' => 'input',
				'size' => '30',
				'eval' => 'required',
			)
		),

	),
	'types' => Array (
		'0' => Array('showitem' => 'hidden;;1;;1-1-1, title;;;;2-2-2')
	),
	'palettes' => Array (
		'1' => Array('showitem' => '')
	)
);

$TCA['tx_x4epersdb_buildings'] = Array (
	'ctrl' => $TCA['tx_x4epersdb_buildings']['ctrl'],
	'interface' => Array (
		'showRecordFieldList' => 'hidden,title,faculty, street,zip, city'
	),
	'feInterface' => $TCA['tx_x4epersdb_department']['feInterface'],
	'columns' => Array (
		'hidden' => Array (
			'exclude' => 0,
			'label' => 'LLL:EXT:lang/locallang_general.php:LGL.hidden',
			'config' => Array (
				'type' => 'check',
				'default' => '0'
			)
		),
		'title' => Array (
			'exclude' => 0,
			'label' => 'LLL:EXT:x4epersdb/locallang_db.php:tx_x4epersdb_buildings.title',
			'config' => Array (
				'type' => 'input',
				'size' => '30',
				'eval' => 'required',
			)
		),
		'faculty' => Array (
			'exclude' => 0,
			'label' => 'LLL:EXT:x4epersdb/locallang_db.php:tx_x4epersdb_buildings.faculty',
			'config' => Array (
				'type' => 'input',
				'size' => '30',
				'eval' => 'required',
			)
		),
		'street' => Array (
			'exclude' => 0,
			'label' => 'LLL:EXT:x4epersdb/locallang_db.php:tx_x4epersdb_buildings.street',
			'config' => Array (
				'type' => 'input',
				'size' => '30',
				'eval' => 'required',
			)
		),
		'zip' => Array (
			'exclude' => 0,
			'label' => 'LLL:EXT:x4epersdb/locallang_db.php:tx_x4epersdb_buildings.zip',
			'config' => Array (
				'type' => 'input',
				'size' => '30',
				'eval' => 'required',
			)
		),
		'city' => Array (
			'exclude' => 0,
			'label' => 'LLL:EXT:x4epersdb/locallang_db.php:tx_x4epersdb_buildings.city',
			'config' => Array (
				'type' => 'input',
				'size' => '30',
				'eval' => 'required',
			)
		),
		'page_id' => Array (
			'exclude' => 0,
			'label' => 'LLL:EXT:x4epersdb/locallang_db.php:tx_x4epersdb_buildings.page_id',
			'config' => Array (
				'type' => 'group',
				'internal_type' => 'db',
				'allowed' => 'pages',
				'size' => 1,
				'minitems' => 0,
				'maxitems' => 1,
			)
		),

	),
	'types' => Array (
		'0' => Array('showitem' => 'hidden;;1;;1-1-1, title;;;;2-2-2, faculty, street, zip, city, page_id')
	),
	'palettes' => Array (
		'1' => Array('showitem' => '')
	)
);

$TCA['tx_x4epersdb_person'] = Array (
	'ctrl' => $TCA['tx_x4epersdb_person']['ctrl'],
	'interface' => Array (
		'showRecordFieldList' => 'hidden,title,sys_language_uid,l18n_parent,l18n_diffsource,title,lastname,firstname',
		'maxDBListItems' => 50,
		'maxSingleDBListItems' => 1000
	),
	'feInterface' => $TCA['tx_x4epersdb_person']['feInterface'],
	'columns' => Array (
		'sys_language_uid' => array (
            'exclude' => 1,
            'label'  => 'LLL:EXT:lang/locallang_general.xml:LGL.language',
            'config' => array (
                'type'                => 'select',
                'foreign_table'       => 'sys_language',
                'foreign_table_where' => 'ORDER BY sys_language.title',
                'items' => array(
                    array('LLL:EXT:lang/locallang_general.xml:LGL.allLanguages', -1),
                    array('LLL:EXT:lang/locallang_general.xml:LGL.default_value', 0)
                )
            )
        ),
        'l18n_parent' => array (
            'displayCond' => 'FIELD:sys_language_uid:>:0',
            'exclude'     => 1,
            'label'       => 'LLL:EXT:lang/locallang_general.xml:LGL.l18n_parent',
            'config'      => array (
                'type'  => 'select',
                'items' => array (
                    array('', 0),
                ),
                'foreign_table'       => 'tx_x4epersdb_person',
                'foreign_table_where' => 'AND tx_x4epersdb_person.pid=###CURRENT_PID### AND tx_x4epersdb_person.sys_language_uid IN (-1,0)',
            )
        ),
        'l18n_diffsource' => array (
            'config' => array (
                'type' => 'passthrough'
            )
        ),
		'hidden' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.php:LGL.hidden',
			'config' => Array (
				'type' => 'check',
				'default' => '0'
			)
		),
		'username' => Array (
			'exclude' => 1,
                        'l10n_mode' => 'exclude',
			'label' => 'LLL:EXT:x4epersdb/locallang_db.php:username',
			'config' => Array (
				'type' => 'none',
			)
		),
		'password' => Array (
			'exclude' => 1,
                        'l10n_mode' => 'exclude',
			'label' => 'LLL:EXT:x4epersdb/locallang_db.php:password',
			'config' => Array (
				'type' => 'input',
				'eval' => 'password',
			)
		),
		'alumni' => Array (
			'exclude' => 0,
                        'l10n_mode' => 'exclude',
			'label' => 'LLL:EXT:x4epersdb/locallang_db.php:alumni',
			'config' => Array (
				'type' => 'check',
			)
		),
		'function' => Array (
			'exclude' => 1,
                        'l10n_mode' => 'exclude',
			'label' => 'LLL:EXT:x4epersdb/locallang_db.php:function',
			'config' => Array (
				'type' => 'select',
				'foreign_table' => 'tx_x4epersdb_function',
				'foreign_table_where' => 'AND tx_x4epersdb_function.pid=###CURRENT_PID### AND sys_language_uid = 0 ORDER BY tx_x4epersdb_function.sorting',
				'size' => 10,
				'minitems' => 0,
				'maxitems' => 999,
			)
		),
		'fe_groups' => Array (
			'exclude' => 1,
                        'l10n_mode' => 'exclude',
			'label' => 'LLL:EXT:x4epersdb/locallang_db.php:fe_groups',
			'config' => Array (
				'type' => 'select',
				'foreign_table' => 'fe_groups',
				'foreign_table_where' => 'AND (fe_groups.pid = ###CURRENT_PID### OR fe_groups.pid=###PAGE_TSCONFIG_ID###)',
				'size' => 6,
				'minitems' => 0,
				'maxitems' => 50,
			)
		),
		'departments' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:x4epersdb/locallang_db.php:department',
			'config' => Array (
				'type' => 'select',
				'foreign_table' => 'tx_x4epersdb_department',
				'foreign_table_where' => 'AND tx_x4epersdb_department.pid=###CURRENT_PID### ORDER BY tx_x4epersdb_department.sorting',
				'size' => 10,
				'minitems' => 0,
				'maxitems' => 999,
				'MM' => 'tx_x4epersdb_person_department_mm',
			)
		),
		'institutes' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:x4epersdb/locallang_db.php:institutes',
			'config' => Array (
				'type' => 'select',
				'foreign_table' => 'tx_x4epersdb_institutes',
				'foreign_table_where' => 'AND tx_x4epersdb_institutes.pid=###CURRENT_PID### ORDER BY tx_x4epersdb_institutes.sorting',
				'size' => 10,
				'minitems' => 0,
				'maxitems' => 999,
				'MM' => 'tx_x4epersdb_person_institute_mm',
			)
		),
		'buildings' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:x4epersdb/locallang_db.php:buildings',
			'config' => Array (
				'type' => 'select',
				'foreign_table' => 'tx_x4epersdb_buildings',
				'foreign_table_where' => 'AND tx_x4epersdb_buildings.pid=###CURRENT_PID### ORDER BY tx_x4epersdb_buildings.sorting',
				'size' => 10,
				'minitems' => 0,
				'maxitems' => 999,
				'MM' => 'tx_x4epersdb_person_building_mm',
			)
		),
		'function_suffix' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:x4epersdb/locallang_db.php:function_suffix',
			'config' => Array (
				'type' => 'input',
				'size' => '20',
			)
		),
		'title_after' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:x4epersdb/locallang_db.php:title_after',
			'config' => Array (
				'type' => 'input',
				'size' => '20',
			)
		),
		'title' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:x4epersdb/locallang_db.php:title',
			'config' => Array (
				'type' => 'input',
				'size' => '20',
			)
		),
		'firstname' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:x4epersdb/locallang_db.php:firstname',
			'config' => Array (
				'type' => 'input',
				'size' => '20',
				'eval' => 'required',
			)
		),
		'lastname' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:x4epersdb/locallang_db.php:lastname',
			'config' => Array (
				'type' => 'input',
				'size' => '20',
				'eval' => 'required',
			)
		),
		'alias' => Array (
			'exclude' => 1,
                        'l10n_mode' => 'exclude',
			'label' => 'LLL:EXT:x4epersdb/locallang_db.php:alias',
			'config' => Array (
				'type' => 'input',
				'size' => '20',
			)
		),
		'email' => Array (
			'exclude' => 1,
                        'l10n_mode' => 'exclude',
			'label' => 'LLL:EXT:x4epersdb/locallang_db.php:email',
			'config' => Array (
				'type' => 'input',
				'size' => '40',
				'eval' => 'required,email',
			)
		),
		'email2' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:x4epersdb/locallang_db.php:email2',
			'config' => Array (
				'type' => 'input',
				'size' => '40',
				'eval' => 'email',
			)
		),
		'mobile_phone' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:x4epersdb/locallang_db.php:mobile_phone',
			'config' => Array (
				'type' => 'input',
				'size' => '20',
				'eval' => 'tx_x4epersdb_tcaEvals',
			)
		),
		'office_address' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:x4epersdb/locallang_db.php:office_address',
			'config' => Array (
				'type' => 'text',
				'cols' => '30',
				'rows' => '2',
			)
		),
		'office_roomnumber' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:x4epersdb/locallang_db.php:office_roomnumber',
			'config' => Array (
				'type' => 'input',
				'size' => '20',
			)
		),
		'office_zip' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:x4epersdb/locallang_db.php:office_zip',
			'config' => Array (
				'type' => 'input',
				'size' => '20',
			)
		),
		'office_location' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:x4epersdb/locallang_db.php:office_location',
			'config' => Array (
				'type' => 'input',
				'size' => '20',
			)
		),
		'office_country' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:x4epersdb/locallang_db.php:office_country',
			'config' => Array (
				'type' => 'input',
				'size' => '20',
			)
		),
		'office_phone' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:x4epersdb/locallang_db.php:office_phone',
			'config' => Array (
				'type' => 'input',
				'size' => '20',
				'eval' => 'tx_x4epersdb_tcaEvals',
			)
		),
		'office_phone2' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:x4epersdb/locallang_db.php:office_phone2',
			'config' => Array (
				'type' => 'input',
				'size' => '20',
				'eval' => 'tx_x4epersdb_tcaEvals',
			)
		),
		'office_fax' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:x4epersdb/locallang_db.php:office_fax',
			'config' => Array (
				'type' => 'input',
				'size' => '20',
				'eval' => 'tx_x4epersdb_tcaEvals',
			)
		),
		'address' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:x4epersdb/locallang_db.php:address',
			'config' => Array (
				'type' => 'text',
				'cols' => '30',
				'rows' => '5',
			)
		),
		'zip' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:x4epersdb/locallang_db.php:zip',
			'config' => Array (
				'type' => 'input',
				'size' => '20',
			)
		),
		'city' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:x4epersdb/locallang_db.php:city',
			'config' => Array (
				'type' => 'input',
				'size' => '20',
			)
		),
		'country' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:x4epersdb/locallang_db.php:country',
			'config' => Array (
				'type' => 'input',
				'size' => '20',
			)
		),
		'phone' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:x4epersdb/locallang_db.php:phone',
			'config' => Array (
				'type' => 'input',
				'size' => '20',
				'eval' => 'tx_x4epersdb_tcaEvals',
			)
		),
		'phone2' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:x4epersdb/locallang_db.php:phone2',
			'config' => Array (
				'type' => 'input',
				'size' => '20',
				'eval' => 'tx_x4epersdb_tcaEvals',
			)
		),
		'mobile' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:x4epersdb/locallang_db.php:mobile',
			'config' => Array (
				'type' => 'input',
				'size' => '20',
				'eval' => 'tx_x4epersdb_tcaEvals',
			)
		),
		'fax' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:x4epersdb/locallang_db.php:fax',
			'config' => Array (
				'type' => 'input',
				'size' => '20',
				'eval' => 'tx_x4epersdb_tcaEvals',
			)
		),
		'url' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:x4epersdb/locallang_db.php:url',
			'config' => Array (
				'type' => 'input',
				'size' => '20',
			)
		),
		'url2' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:x4epersdb/locallang_db.php:url2',
			'config' => Array (
				'type' => 'input',
				'size' => '20',
			)
		),
		'lecture_link' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:x4epersdb/locallang_db.php:lecture_link',
			'config' => Array (
				'type' => 'input',
				'size' => '15',
				'max' => '255',
				'checkbox' => '',
				'eval' => 'trim',
				'wizards' => Array(
					'_PADDING' => 2,
					'link' => Array(
						'type' => 'popup',
						'title' => 'Link',
						'icon' => 'link_popup.gif',
						'script' => 'browse_links.php?mode=wizard',
						'JSopenParams' => 'height=300,width=500,status=0,menubar=0,scrollbars=1'
					)
				)
			)
		),
		'beuser' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:x4epersdb/locallang_db.php:beuser',
			'config' => Array (
				'type' => 'passthrough',
				'internal_type' => 'db',
				'allowed' => 'be_users',
				'size' => 3,
				'minitems' => 0,
				'maxitems' => 999,
				'foreign_table_prefix' => '',
				'wizards' => Array(
						'_PADDING' => 1,
						'_VERTICAL' => 1,
						'list' => Array(
							'type' => 'popup',
							'title' => 'List',
							'script' => '../typo3conf/ext/x4ebeuserlist/mod1/index.php',
							'icon' => 'list.gif',
							'JSopenParams' => 'height=400,width=200,status=0,menubar=0,scrollbars=1',
						)
					)
			)
		),
		'personal_page' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:x4epersdb/locallang_db.php:personal_page',
			'config' => Array (
				'type' => 'group',
				'internal_type' => 'db',
				'allowed' => 'pages',
				'size' => 1,
				'minitems' => 0,
				'maxitems' => 1,
			)
		),
		'resume_page' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:x4epersdb/locallang_db.php:resume_page',
			'config' => Array (
				'type' => 'group',
				'internal_type' => 'db',
				'allowed' => 'pages',
				'size' => 1,
				'minitems' => 0,
				'maxitems' => 1,
			)
		),
		'course_page' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:x4epersdb/locallang_db.php:course_page',
			'config' => Array (
				'type' => 'group',
				'internal_type' => 'db',
				'allowed' => 'pages',
				'size' => 1,
				'minitems' => 0,
				'maxitems' => 1,
			)
		),
		'research_page' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:x4epersdb/locallang_db.php:research_page',
			'config' => Array (
				'type' => 'group',
				'internal_type' => 'db',
				'allowed' => 'pages',
				'size' => 1,
				'minitems' => 0,
				'maxitems' => 1,
			)
		),
		'office_mobile_phone' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:x4epersdb/locallang_db.php:office_mobile_phone',
			'config' => Array (
				'type' => 'input',
				'size' => '20',
				'eval' => 'tx_x4epersdb_tcaEvals',
			)
		),
		'phone2' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:x4epersdb/locallang_db.php:phone2',
			'config' => Array (
				'type' => 'input',
				'size' => '20',
				'eval' => 'tx_x4epersdb_tcaEvals',
			)
		),
		'room' => Array (
			'exclude' => 0,
			'label' => 'LLL:EXT:x4epersdb/locallang_db.php:room',
			'config' => Array (
				'type' => 'input',
				'size' => '20',
			)
		),
		'floor' => Array (
			'exclude' => 0,
			'label' => 'LLL:EXT:x4epersdb/locallang_db.php:floor',
			'config' => Array (
				'type' => 'input',
				'size' => '20',
			)
		),
		'image' => Array (
			'exclude' => 1,
                        'l10n_mode' => 'exclude',
			'label' => 'LLL:EXT:x4epersdb/locallang_db.php:image',
			'config' => Array (
				'type' => 'group',
				'internal_type' => 'file',
				'allowed' => $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext'],
				'max_size' => '2000',
				'uploadfolder' => 'uploads/x4epersdb/',
				'show_thumbs' => '1',
				'size' => '3',
				'maxitems' => '1',
				'minitems' => '0'
			)
		),
		'profile' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:x4epersdb/locallang_db.php:profile',
			'config' => Array (
				'type' => 'text',
				'cols' => '30',
				'rows' => '5',
				'wizards' => Array(
					'_PADDING' => 2,
					'RTE' => Array(
						'notNewRecords' => 1,
						'RTEonly' => 1,
						'type' => 'script',
						'title' => 'Full screen Rich Text Editing|Formatteret redigering i hele vinduet',
						'icon' => 'wizard_rte2.gif',
						'script' => 'wizard_rte.php',
					),
				),
			)
		),
		'news' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:x4epersdb/locallang_db.php:news',
			'config' => Array (
				'type' => 'text',
				'cols' => '30',
				'rows' => '5',
				'wizards' => Array(
					'_PADDING' => 2,
					'RTE' => Array(
						'notNewRecords' => 1,
						'RTEonly' => 1,
						'type' => 'script',
						'title' => 'Full screen Rich Text Editing|Formatteret redigering i hele vinduet',
						'icon' => 'wizard_rte2.gif',
						'script' => 'wizard_rte.php',
					),
				),
			)
		),
		'research' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:x4epersdb/locallang_db.php:research',
			'config' => Array (
				'type' => 'text',
				'cols' => '30',
				'rows' => '5',
				'wizards' => Array(
					'_PADDING' => 2,
					'RTE' => Array(
						'notNewRecords' => 1,
						'RTEonly' => 1,
						'type' => 'script',
						'title' => 'Full screen Rich Text Editing|Formatteret redigering i hele vinduet',
						'icon' => 'wizard_rte2.gif',
						'script' => 'wizard_rte.php',
					),
				),
			)
		),
		'membership' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:x4epersdb/locallang_db.php:membership',
			'config' => Array (
				'type' => 'text',
				'cols' => '30',
				'rows' => '5',
				'wizards' => Array(
					'_PADDING' => 2,
					'RTE' => Array(
						'notNewRecords' => 1,
						'RTEonly' => 1,
						'type' => 'script',
						'title' => 'Full screen Rich Text Editing|Formatteret redigering i hele vinduet',
						'icon' => 'wizard_rte2.gif',
						'script' => 'wizard_rte.php',
					),
				),
			)
		),
		'publadmin' => Array (
			'exclude' => 1,
                        'l10n_mode' => 'exclude',
			'label' => 'LLL:EXT:x4epersdb/locallang_db.php:publadmin',
			'config' => Array (
				'type' => 'check',
			)
		),
		'qualiadmin' => Array (
			'exclude' => 1,
                        'l10n_mode' => 'exclude',
			'label' => 'LLL:EXT:x4epersdb/locallang_db.php:qualiadmin',
			'config' => Array (
				'type' => 'check',
			)
		),
		'showpublics' => Array (
			'exclude' => 1,
                        'l10n_mode' => 'exclude',
			'label' => 'LLL:EXT:x4epersdb/locallang_db.php:showpublics',
			'config' => Array (
				'type' => 'check',
			)
		),
		'showpublicsinmenu' => Array (
			'exclude' => 1,
                        'l10n_mode' => 'exclude',
			'label' => 'LLL:EXT:x4epersdb/locallang_db.php:showpublicsinmenu',
			'config' => Array (
				'type' => 'check',
			)
		),
		'feuser_id' => Array (
			'exclude' => 0,
                        'l10n_mode' => 'exclude',
			'label' => 'LLL:EXT:x4epersdb/locallang_db.php:feuser_id',
			'config' => Array (
				'type' => 'select',
				'foreign_table' => 'fe_users',
				//'foreign_table_where' => 'AND (fe_groups.pid = ###CURRENT_PID### OR fe_groups.pid=###PAGE_TSCONFIG_ID###)',
				'foreign_table_where' => ' ORDER BY username',
				'size' => 1,
				'minitems' => 1,
				'maxitems' => 1,
			)
		),
		'main_entry' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:x4epersdb/locallang_db.php:main_entry',
			'config' => Array (
				'type' => 'check',
				'default' => '0'
			)
		),
		'dni' => Array (
			'exclude' => 1,
                        'l10n_mode' => 'exclude',
			'label' => 'LLL:EXT:x4epersdb/locallang_db.php:dni',
			'config' => Array (
				'type' => 'input',
				'size' => '10',
				'eval' => 'int'
			)
		),
		'mcss_id' => Array (
			'exclude' => 1,
                           'l10n_mode' => 'exclude',
			'label' => 'LLL:EXT:x4epersdb/locallang_db.php:mcss_id',
			'config' => Array (
				'type' => 'input',
				'size' => '10',
				'eval' => 'int'
			)
		),
		'company' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:x4epersdb/locallang_db.php:company',
			'config' => Array (
				'type' => 'input',
				'size' => '100',
			)
		),
		'static_info_country' => Array (
			"exclude" => 1,
			"label" => 'LLL:EXT:x4epersdb/locallang_db.php:static_info_country',
			"config" => Array (
				"type" => "select",
				"foreign_table" => 'static_countries',
				"size" => 1,
				"minitems" => 0,
				"maxitems" => 1,
			)
		),
		'tx_x4emutation_department' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:x4epersdb/locallang_db.php:tx_x4emutation_department',
			'config' => Array (
				'type' => 'input',
				'size' => '100',
			)
		),
		'tx_x4emutation_affiliation' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:x4epersdb/locallang_db.php:tx_x4emutation_affiliation',
			'config' => Array (
				'type' => 'input',
				'size' => '100',
			)
		),
		'tx_x4emutation_speciality' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:x4epersdb/locallang_db.php:tx_x4emutation_speciality',
			'config' => Array (
				'type' => 'input',
				'size' => '100',
			)
		),
		'tx_x4epersdbfeedit_officehour' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:x4epersdb/locallang_db.php:news',
			'config' => Array (
				'type' => 'text',
				'cols' => '30',
				'rows' => '5',
				'wizards' => Array(
					'_PADDING' => 2,
					'RTE' => Array(
						'notNewRecords' => 1,
						'RTEonly' => 1,
						'type' => 'script',
						'title' => 'Full screen Rich Text Editing|Formatteret redigering i hele vinduet',
						'icon' => 'wizard_rte2.gif',
						'script' => 'wizard_rte.php',
					),
				),
			)
		),
		'external_id' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:x4epersdb/locallang_db.php:external_id',
			'config' => Array (
				'type' => 'input',
				'size' => '100',
			)
		),
		'rssUrl' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:x4epersdb/locallang_db.php:rssUrl',
			'config' => Array (
				'type' => 'input',
				'size' => '100',
			)
		),
		'add_info' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:x4epersdb/locallang_db.php:add_info',
			'config' => Array (
				'type' => 'text',
				'cols' => '30',
				'rows' => '5',
				'wizards' => Array(
					'_PADDING' => 2,
					'RTE' => Array(
						'notNewRecords' => 1,
						'RTEonly' => 1,
						'type' => 'script',
						'title' => 'Full screen Rich Text Editing|Formatteret redigering i hele vinduet',
						'icon' => 'wizard_rte2.gif',
						'script' => 'wizard_rte.php',
					),
				),
			)
		),
	),
	'types' => Array (
		'0' => Array('showitem' => 'sys_language_uid;;;;1-1-1,l18n_parent,l18n_diffsource,hidden,alumni, title, firstname, lastname, alias, title_after, email,function,function_suffix,fe_groups,
								--div--;Bild/Profil,image,profile;;;richtext[paste|bold|italic|underline|formatblock|class|left|center|right|orderedlist|unorderedlist|outdent|indent|link|image]:rte_transform[flag=rte_enabled|mode=ts],membership;;;richtext[paste|bold|italic|underline|formatblock|class|left|center|right|orderedlist|unorderedlist|outdent|indent|link|image]:rte_transform[flag=rte_enabled|mode=ts],add_info;;;richtext[paste|bold|italic|underline|formatblock|class|left|center|right|orderedlist|unorderedlist|outdent|indent|link|image]:rte_transform[flag=rte_enabled|mode=ts],
								--div--;Büro,buildings,institutes,departments,floor,room,office_address,office_roomnumber,office_zip,office_location,office_country,office_phone,office_phone2,office_mobile_phone,office_fax,email2,
								--div--;Zweitadresse / Privat,address,zip,city,country,phone,phone2,mobile,fax,url,url2,
								--div--;Spezial,username,password,lecture_link,publadmin, qualiadmin, showpublics,showpublicsinmenu,personal_page,beuser,feuser_id,company,static_info_country,tx_x4emutation_department,tx_x4emutation_affiliation,tx_x4emutation_speciality,rssUrl,
								--div--;Zentrale Forschungsdatenbank,dni,mcss_id,main_entry')
	),
	'palettes' => Array (
		'1' => Array('showitem' => '')
	)
);
?>