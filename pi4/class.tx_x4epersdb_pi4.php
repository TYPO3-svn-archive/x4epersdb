<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2005 Markus Stauffiger (markus@4eyes.ch)
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
/**
 * Plugin 'Update password' for the 'x4epersdb' extension.
 *
 * @author	Markus Stauffiger <markus@4eyes.ch>
 * @deprecated does not support md5 or salted passwords
 */


require_once(PATH_tslib.'class.tslib_pibase.php');

class tx_x4epersdb_pi4 extends tslib_pibase {
	var $prefixId = 'tx_x4epersdb_pi4';		// Same as class name
	var $scriptRelPath = 'pi4/class.tx_x4epersdb_pi4.php';	// Path to this script relative to the extension dir.
	var $extKey = 'x4epersdb';	// The extension key.
	var $pi_checkCHash = TRUE;
	/**
	 * Name of the table containing the persons
	 * @var string
	 */
	var $table = 'tx_x4epersdb_person';

	/**
	 * Instance of persdb/pi1
	 *
	 * @var object
	 */
	var $persDbInstance = null;

	/**
	 * Person record
	 * @var array
	 */
	var $person = array();

	/**
	 * Renders the form to change the user's password
	 *
	 * @param string $content
	 * @param array  $conf    typoscript configuration array
	 * @return string HTML formatted string
	 */
	function main($content,$conf)	{
		$GLOBALS['TSFE']->set_no_cache();
		$this->person = $this->pi_getRecord($this->table,intval($_GET['tx_'.$this->extKey.'_pi1']['showUid']));
		if (!isset($GLOBALS['TSFE']->fe_user->user['uid']) || ($this->person['feuser_id'] != $GLOBALS['TSFE']->fe_user->user['uid'])) {
			die('You are not allowed to edit the password of anybody else than yourself');
		}
		$this->conf=$conf;
		$this->pi_setPiVarDefaults();
		$this->pi_loadLL();

			// include stylesheet
		$GLOBALS['TSFE']->additionalHeaderData[$this->extKey] = '
			<link href="'.$this->conf['stylesheet'].'" rel="stylesheet" type="text/css" />';
			// add fvalidate javascripts to use for validation (INCLUDES FROM OTHER!! EXTENSION)
		$GLOBALS['TSFE']->additionalHeaderData[$this->extKey].='
				<script type="text/javascript" src="typo3conf/ext/publics/templates/code.js"></script>
				<script type="text/javascript" src="typo3conf/ext/publics/fValidate/fValidate.config.js"></script>
				<script type="text/javascript" src="typo3conf/ext/publics/fValidate/fValidate.core.js"></script>
				<script type="text/javascript" src="typo3conf/ext/publics/fValidate/fValidate.lang-enUS.js"></script>
				<script type="text/javascript" src="typo3conf/ext/publics/fValidate/fValidate.validators.js"></script>
				<script type="text/javascript" src="typo3conf/ext/publics/fValidate/fValidate.logical.js"></script>';

		$this->template = $this->cObj->fileResource($this->conf['templateFile']);

		$this->makePersDbInstance();
        $mArr['###author###'] = $this->persDbInstance->addPersonToPageTitle($this->person,1,$this->cObj->getSubpart($this->template,'###pageTitle###'));
        unset($author);
		$content = $this->cObj->getSubpart($this->template,'###form###');
		$mArr['###action###'] = t3lib_div::getIndpEnv('REQUEST_URI');
		$mArr['###errorMessage###'] = '';
		$mArr['###prefixId###'] = $this->prefixId;
		if (isset($this->piVars['submit'])) {
			if ($this->piVars['password'] != $this->piVars['password_confirm']) {
				$mArr['###errorMessage###'] = $this->pi_getLL('passwordNoMatch');
			} else {
				$content = $this->updatePassword($mArr);
			}
		}
		return $this->cObj->substituteMarkerArray($content,$mArr);
	}

	/**
	 * Generates an instance of the person-db. Puts it into member variable
	 * to prevent regenerating instances
	 *
	 * return void
	 */
	function makePersDbInstance() {
		if ($this->persDbInstance == null) {
			require_once(PATH_typo3conf.'ext/x4epersdb/pi1/class.tx_persdb_pi1.php');
			$this->persDbInstance = t3lib_div::makeInstance('tx_'.$this->extKey.'_pi1');
		}
	}

	/**
	 * Updates the users' password
	 *
	 * @param array $mArr	Marker array, by reference
	 * @return string
	 */
	function updatePassword(&$mArr) {
		$upd['password'] = mysql_real_escape_string($this->piVars['password']);
		if ($GLOBALS['TYPO3_DB']->exec_UPDATEquery('fe_users','uid = '.intval($this->person['feuser_id']),$upd)) {
			return $this->cObj->substituteMarkerArray($this->cObj->getSubpart($this->template,'###updated###'),$mArr);
		}

	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/x4epersdb/pi4/class.tx_x4epersdb_pi4.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/x4epersdb/pi4/class.tx_x4epersdb_pi4.php']);
}

?>