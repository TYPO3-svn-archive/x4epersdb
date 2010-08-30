<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

$personTable = 'tx_'.$_EXTKEY.'_person';
$functionTable = 'tx_'.$_EXTKEY.'_function';
$departmentTable = 'tx_'.$_EXTKEY.'_department';


t3lib_div::loadTCA('tt_content');
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi1']='layout,select_key,pages';
t3lib_extMgm::addPlugin(Array('LLL:EXT:'.$_EXTKEY.'/locallang_db.php:tt_content.list_type_pi1', $_EXTKEY.'_pi1'),'list_type');
t3lib_extMgm::addStaticFile($_EXTKEY,"pi1/static/","Person List");


t3lib_div::loadTCA('tt_content');
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi2']='layout,select_key,pages';
t3lib_extMgm::addPlugin(Array('LLL:EXT:'.$_EXTKEY.'/locallang_db.php:tt_content.list_type_pi2', $_EXTKEY.'_pi2'),'list_type');
t3lib_extMgm::addStaticFile($_EXTKEY,"pi2/static/","Personal Menu");


t3lib_div::loadTCA('tt_content');
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi3']='layout,select_key,pages';
t3lib_extMgm::addPlugin(Array('LLL:EXT:'.$_EXTKEY.'/locallang_db.php:tt_content.list_type_pi3', $_EXTKEY.'_pi3'),'list_type');
t3lib_extMgm::addStaticFile($_EXTKEY,"pi3/static/","Person Info");

t3lib_div::loadTCA('tt_content');
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi4']='layout,select_key,pages';
t3lib_extMgm::addPlugin(Array('LLL:EXT:'.$_EXTKEY.'/locallang_db.php:tt_content.list_type_pi4', $_EXTKEY.'_pi4'),'list_type');
t3lib_extMgm::addStaticFile($_EXTKEY,"pi4/static/","Change password");

t3lib_div::loadTCA('tt_content');
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi5']='layout,select_key,pages';
t3lib_extMgm::addPlugin(Array('LLL:EXT:'.$_EXTKEY.'/locallang_db.php:tt_content.list_type_pi5', $_EXTKEY.'_pi5'),'list_type');
t3lib_extMgm::addStaticFile($_EXTKEY,"pi5/static/","Inhalt der persönlichen Seiten");

t3lib_div::loadTCA('tt_content');
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi6']='layout,select_key,pages';
t3lib_extMgm::addPlugin(Array('LLL:EXT:'.$_EXTKEY.'/locallang_db.php:tt_content.list_type_pi6', $_EXTKEY.'_pi6'),'list_type');

t3lib_div::loadTCA('tt_content');
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi7']='layout,select_key,pages';
t3lib_extMgm::addPlugin(Array('LLL:EXT:'.$_EXTKEY.'/locallang_db.php:tt_content.list_type_pi7', $_EXTKEY.'_pi7'),'list_type');
t3lib_extMgm::addStaticFile($_EXTKEY,"pi7/static/","Person Info for research groups");


$TCA[$functionTable] = Array (
	"ctrl" => Array (
		"title" => "LLL:EXT:x4epersdb/locallang_db.php:tx_x4epersdb_function",
		"label" => "title",
		"tstamp" => "tstamp",
		"crdate" => "crdate",
        "languageField"            => "sys_language_uid",
        "transOrigPointerField"    => "l18n_parent",
        "transOrigDiffSourceField" => "l18n_diffsource",
		"cruser_id" => "cruser_id",
		"sortby" => "sorting",
		"delete" => "deleted",
		"enablecolumns" => Array (
			"disabled" => "hidden",
		),
		"dynamicConfigFile" => t3lib_extMgm::extPath($_EXTKEY)."tca.php",
		"iconfile" => t3lib_extMgm::extRelPath($_EXTKEY)."icon_tx_x4epersdb_function.gif",
	),
	"feInterface" => Array (
		"fe_admin_fieldList" => "sys_language_uid, l18n_parent, l18n_diffsource, hidden, title",
	)
);


$TCA[$departmentTable] = Array (
	"ctrl" => Array (
		"title" => "LLL:EXT:x4epersdb/locallang_db.php:tx_x4epersdb_department",
		"label" => "title",
		"tstamp" => "tstamp",
		"crdate" => "crdate",
		"cruser_id" => "cruser_id",
		"sortby" => "sorting",
		"delete" => "deleted",
		"enablecolumns" => Array (
			"disabled" => "hidden",
		),
		"dynamicConfigFile" => t3lib_extMgm::extPath($_EXTKEY)."tca.php",
		"iconfile" => t3lib_extMgm::extRelPath($_EXTKEY)."icon_tx_x4epersdb_department.gif",
	),
	"feInterface" => Array (
		"fe_admin_fieldList" => "hidden, title",
	)
);

$TCA['tx_x4epersdb_institutes'] = Array (
	"ctrl" => Array (
		"title" => "LLL:EXT:x4epersdb/locallang_db.php:tx_x4epersdb_institutes",
		"label" => "title",
		"tstamp" => "tstamp",
		"crdate" => "crdate",
		"cruser_id" => "cruser_id",
		"sortby" => "sorting",
		"delete" => "deleted",
		"enablecolumns" => Array (
			"disabled" => "hidden",
		),
		"dynamicConfigFile" => t3lib_extMgm::extPath($_EXTKEY)."tca.php",
		"iconfile" => t3lib_extMgm::extRelPath($_EXTKEY)."icon_tx_x4epersdb_department.gif",
	),
	"feInterface" => Array (
		"fe_admin_fieldList" => "hidden, title",
	)
);

$TCA['tx_x4epersdb_buildings'] = Array (
	"ctrl" => Array (
		"title" => "LLL:EXT:x4epersdb/locallang_db.php:tx_x4epersdb_buildings",
		"label" => "title",
		"tstamp" => "tstamp",
		"crdate" => "crdate",
		"cruser_id" => "cruser_id",
		"sortby" => "sorting",
		"delete" => "deleted",
		"enablecolumns" => Array (
			"disabled" => "hidden",
		),
		"dynamicConfigFile" => t3lib_extMgm::extPath($_EXTKEY)."tca.php",
		"iconfile" => t3lib_extMgm::extRelPath($_EXTKEY)."icon_tx_x4epersdb_department.gif",
	),
	"feInterface" => Array (
		"fe_admin_fieldList" => "hidden, title",
	)
);

$TCA[$personTable] = Array (
	"ctrl" => Array (
		"title" => "LLL:EXT:x4epersdb/locallang_db.php:tx_x4epersdb_person",
		"label" => "lastname",
		'label_alt' => 'firstname',
		'label_alt_force' => 1,
		"tstamp" => "tstamp",
		"crdate" => "crdate",
		"cruser_id" => "cruser_id",
		"languageField" => "sys_language_uid",
		"transOrigPointerField"    => "l18n_parent",
        "transOrigDiffSourceField" => "l18n_diffsource",
		"default_sortby" => "ORDER BY lastname",
		"delete" => "deleted",
		'dividers2tabs' => 1,
		"enablecolumns" => Array (
			"disabled" => "hidden",
		),
		"dynamicConfigFile" => t3lib_extMgm::extPath($_EXTKEY)."tca.php",
		"iconfile" => t3lib_extMgm::extRelPath($_EXTKEY)."icon_tx_x4epersdb_function.gif",
	),
	"feInterface" => Array (
		"fe_admin_fieldList" => "hidden, title",
	)
);


$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY.'_pi1']='pi_flexform';
t3lib_extMgm::addPiFlexFormValue($_EXTKEY.'_pi1', 'FILE:EXT:'.$_EXTKEY.'/pi1/flexform_ds.xml');
$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY.'_pi2']='pi_flexform';
t3lib_extMgm::addPiFlexFormValue($_EXTKEY.'_pi2', 'FILE:EXT:'.$_EXTKEY.'/pi2/flexform_ds.xml');
$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY.'_pi3']='pi_flexform';
t3lib_extMgm::addPiFlexFormValue($_EXTKEY.'_pi3', 'FILE:EXT:'.$_EXTKEY.'/pi3/flexform_ds.xml');
$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY.'_pi7']='pi_flexform';
t3lib_extMgm::addPiFlexFormValue($_EXTKEY.'_pi7', 'FILE:EXT:'.$_EXTKEY.'/pi7/flexform_ds.xml');

if (TYPO3_MODE=='BE')	{
    $TBE_MODULES_EXT[ 'xMOD_db_new_content_el' ][ 'addElClasses' ][ 'tx_x4epersdb_pi3_wizicon' ] = t3lib_extMgm::extPath( $_EXTKEY ).'pi3/class.tx_x4epersdb_pi3_wizicon.php';
	$TBE_MODULES_EXT[ 'xMOD_db_new_content_el' ][ 'addElClasses' ][ 'tx_x4epersdb_pi7_wizicon' ] = t3lib_extMgm::extPath( $_EXTKEY ).'pi7/class.tx_x4epersdb_pi7_wizicon.php';
	$TBE_MODULES_EXT[ 'xMOD_db_new_content_el' ][ 'addElClasses' ][ 'tx_x4epersdb_pi1_wizicon' ] = t3lib_extMgm::extPath( $_EXTKEY ).'pi1/class.tx_x4epersdb_pi1_wizicon.php';
	require_once(t3lib_extMgm::extPath($_EXTKEY).'class.tx_'.$_EXTKEY.'_helper.php');
	t3lib_extMgm::addModule("web","txx4epersdbM1","",t3lib_extMgm::extPath($_EXTKEY)."mod1/");
	require_once(t3lib_extMgm::extPath("x4epersdb")."class.tx_x4epersdb_tx_x4epersdb_tca_proc.php");
}
?>