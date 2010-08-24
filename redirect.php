<?php

/*
Hardocoded redirect  to http://www.isis.unibas.ch/doz-gibt-veranst.php?dozid=###dozId###
*/

$u = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('*','tx_x4epersdb_person','uid='.intval($_GET['tx_x4epersdb_pi1']['showUid']));

$link = $GLOBALS['TSFE']->cObj->typolink_url(array('parameter' => $u[0]['lecture_link']));

header("Location: ".$link); /* Browser umleiten */

/* Stellen Sie sicher, dass der nachfolgende Code nicht ausgefuehrt wird, wenn
   eine Umleitung stattfindet. */
exit;
?>