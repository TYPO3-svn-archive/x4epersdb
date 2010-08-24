<?php

########################################################################
# Extension Manager/Repository config file for ext: "x4epersdb"
#
# Auto generated 10-08-2010 17:11
#
# Manual updates:
# Only the data in the array - anything else is removed by next write.
# "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => '4eyes - Person database',
	'description' => '4eyes - Person database',
	'category' => 'plugin',
	'shy' => 0,
	'version' => '1.2.7',
	'dependencies' => 'cms',
	'conflicts' => '',
	'priority' => '',
	'loadOrder' => '',
	'module' => '',
	'state' => 'stable',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearcacheonload' => 0,
	'lockType' => '',
	'author' => 'Markus Stauffiger',
	'author_email' => 'markus@4eyes.ch',
	'author_company' => '4eyes GmbH',
	'CGLcompliance' => '',
	'CGLcompliance_note' => '',
	'constraints' => array(
		'depends' => array(
			'cms' => '',
			'x4epibase' => '',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:74:{s:9:"ChangeLog";s:4:"a4d6";s:10:"README.txt";s:4:"9fa9";s:29:"class.tx_x4epersdb_helper.php";s:4:"7f24";s:36:"class.tx_x4epersdb_tcemainprocdm.php";s:4:"9f5e";s:54:"class.tx_x4epersdb_tcemainprocdm.php.05-04-2010.backup";s:4:"b6b5";s:15:"emhgFeUsers.php";s:4:"8542";s:12:"ext_icon.gif";s:4:"90b3";s:17:"ext_localconf.php";s:4:"d741";s:14:"ext_tables.php";s:4:"e585";s:14:"ext_tables.sql";s:4:"bfd1";s:28:"ext_typoscript_constants.txt";s:4:"f0dc";s:24:"ext_typoscript_setup.txt";s:4:"24cf";s:32:"icon_tx_x4epersdb_department.gif";s:4:"475a";s:30:"icon_tx_x4epersdb_function.gif";s:4:"475a";s:16:"locallang_db.php";s:4:"1826";s:12:"redirect.php";s:4:"91f0";s:7:"tca.php";s:4:"6a3a";s:14:"pi4/ce_wiz.gif";s:4:"5a35";s:30:"pi4/class.tx_x4epersdb_pi4.php";s:4:"9515";s:17:"pi4/locallang.php";s:4:"20c0";s:14:"pi3/ce_wiz.gif";s:4:"5a35";s:30:"pi3/class.tx_x4epersdb_pi3.php";s:4:"5f3c";s:38:"pi3/class.tx_x4epersdb_pi3_wizicon.php";s:4:"fde7";s:19:"pi3/flexform_ds.xml";s:4:"35f7";s:17:"pi3/locallang.php";s:4:"fbe6";s:22:"pi3/locallang_flex.php";s:4:"39a5";s:29:"templates/changePassword.html";s:4:"97ee";s:21:"templates/detail.html";s:4:"04a0";s:23:"templates/function.html";s:4:"8976";s:19:"templates/list.html";s:4:"7ff5";s:19:"templates/menu.html";s:4:"a283";s:24:"templates/shortInfo.html";s:4:"fdd6";s:35:"templates/shortInfoForResearch.html";s:4:"6385";s:19:"templates/style.css";s:4:"12ee";s:14:"pi6/ce_wiz.gif";s:4:"5a35";s:30:"pi6/class.tx_x4epersdb_pi6.php";s:4:"c772";s:19:"pi6/flexform_ds.xml";s:4:"b314";s:17:"pi6/locallang.php";s:4:"2b36";s:22:"pi6/locallang_flex.php";s:4:"07fe";s:24:"pi6/static/editorcfg.txt";s:4:"3938";s:14:"pi1/ce_wiz.gif";s:4:"5a35";s:30:"pi1/class.tx_x4epersdb_pi1.php";s:4:"44cf";s:38:"pi1/class.tx_x4epersdb_pi1_wizicon.php";s:4:"93ce";s:19:"pi1/flexform_ds.xml";s:4:"295d";s:26:"pi1/flexform_ds.xml.backup";s:4:"85b6";s:17:"pi1/locallang.php";s:4:"03fc";s:22:"pi1/locallang_flex.php";s:4:"6eca";s:14:"pi2/ce_wiz.gif";s:4:"5a35";s:30:"pi2/class.tx_x4epersdb_pi2.php";s:4:"95e9";s:19:"pi2/flexform_ds.xml";s:4:"caa5";s:17:"pi2/locallang.php";s:4:"7d5b";s:22:"pi2/locallang_flex.php";s:4:"226c";s:45:"scripts/class.tx_x4epersdb_dni_ftp_import.php";s:4:"c25d";s:26:"scripts/dni_ftp_import.php";s:4:"c261";s:22:"scripts/dni_import.php";s:4:"dbbb";s:31:"scripts/forschungsdb_import.csv";s:4:"c00b";s:21:"scripts/inittypo3.php";s:4:"df35";s:22:"scripts/pub_export.php";s:4:"5857";s:30:"scripts/upload_folder.htaccess";s:4:"6d55";s:42:"scripts/ftp_source/forschungsdb_export.csv";s:4:"41c7";s:37:"mod1/class.tx_x4epersdb_csvImport.php";s:4:"48a8";s:13:"mod1/conf.php";s:4:"28b6";s:14:"mod1/index.php";s:4:"bc29";s:18:"mod1/locallang.php";s:4:"b723";s:22:"mod1/locallang_mod.php";s:4:"9165";s:19:"mod1/moduleicon.gif";s:4:"7b73";s:14:"pi5/ce_wiz.gif";s:4:"5a35";s:30:"pi5/class.tx_x4epersdb_pi5.php";s:4:"d67f";s:14:"pi7/ce_wiz.gif";s:4:"5a35";s:30:"pi7/class.tx_x4epersdb_pi7.php";s:4:"8261";s:38:"pi7/class.tx_x4epersdb_pi7_wizicon.php";s:4:"9269";s:19:"pi7/flexform_ds.xml";s:4:"b620";s:17:"pi7/locallang.php";s:4:"5631";s:22:"pi7/locallang_flex.php";s:4:"041a";}',
	'suggests' => array(
	),
);

?>