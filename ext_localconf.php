<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

  ## Extending TypoScript from static template uid=43 to set up userdefined tag:
t3lib_extMgm::addTypoScript($_EXTKEY,'editorcfg','
	tt_content.CSS_editor.ch.tx_'.$_EXTKEY.'_pi1 = < plugin.tx_'.$_EXTKEY.'_pi1.CSS_editor
',43);


t3lib_extMgm::addPItoST43($_EXTKEY,'pi1/class.tx_'.$_EXTKEY.'_pi1.php','_pi1','list_type',1);


  ## Extending TypoScript from static template uid=43 to set up userdefined tag:
t3lib_extMgm::addTypoScript($_EXTKEY,'editorcfg','
	tt_content.CSS_editor.ch.tx_'.$_EXTKEY.'_pi2 = < plugin.tx_'.$_EXTKEY.'_pi2.CSS_editor
',43);


t3lib_extMgm::addPItoST43($_EXTKEY,'pi2/class.tx_'.$_EXTKEY.'_pi2.php','_pi2','list_type',1);


  ## Extending TypoScript from static template uid=43 to set up userdefined tag:
t3lib_extMgm::addTypoScript($_EXTKEY,'editorcfg','
	tt_content.CSS_editor.ch.tx_'.$_EXTKEY.'_pi3 = < plugin.tx_'.$_EXTKEY.'_pi3.CSS_editor
',43);


t3lib_extMgm::addPItoST43($_EXTKEY,'pi3/class.tx_'.$_EXTKEY.'_pi3.php','_pi3','list_type',1);

t3lib_extMgm::addUserTSConfig('
	options.saveDocNew.tx_'.$_EXTKEY.'_function=1
');

  ## Extending TypoScript from static template uid=43 to set up userdefined tag:
t3lib_extMgm::addTypoScript($_EXTKEY,'editorcfg','
	tt_content.CSS_editor.ch.tx_'.$_EXTKEY.'_pi3 = < plugin.tx_'.$_EXTKEY.'_pi3.CSS_editor
',43);


t3lib_extMgm::addPItoST43($_EXTKEY,'pi4/class.tx_'.$_EXTKEY.'_pi4.php','_pi4','list_type',1);
t3lib_extMgm::addPItoST43($_EXTKEY,'pi5/class.tx_'.$_EXTKEY.'_pi5.php','_pi5','list_type',1);
t3lib_extMgm::addPItoST43($_EXTKEY,'pi6/class.tx_'.$_EXTKEY.'_pi6.php','_pi6','list_type',1);


  ## Extending TypoScript from static template uid=43 to set up userdefined tag:
t3lib_extMgm::addTypoScript($_EXTKEY,'editorcfg','
	tt_content.CSS_editor.ch.tx_'.$_EXTKEY.'_pi7 = < plugin.tx_'.$_EXTKEY.'_pi7.CSS_editor
',43);

t3lib_extMgm::addPItoST43($_EXTKEY,'pi7/class.tx_'.$_EXTKEY.'_pi7.php','_pi7','list_type',1);

  ## Extending TypoScript from static template uid=43 to set up userdefined tag:
t3lib_extMgm::addTypoScript($_EXTKEY,'editorcfg','
	tt_content.CSS_editor.ch.tx_'.$_EXTKEY.'_pi7 = < plugin.tx_'.$_EXTKEY.'_pi7.CSS_editor
',43);

// add post-process hook

$GLOBALS ['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] = 'EXT:'.$_EXTKEY.'/class.tx_'.$_EXTKEY.'_tcemainprocdm.php:tx_'.$_EXTKEY.'_tcemainprocdm';

// DNI-Import-Cron als Tast registrieren...
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks']['tx_x4epersdb_dniftpimport'] = array(
    'extension'        => $_EXTKEY,
    'title'            => 'Import der Personen-ID',
    'description'      => 'Dieser Task verarbeitet vordefinierte CSV-Datei mit Personendaten und importiert Personen IDs, falls sie noch nicht exsistieren.'
);
?>