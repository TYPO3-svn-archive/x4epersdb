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
 * Plugin 'Link to detail page' for the 'listfeuser_uni' extension.
 *
 * @author	Markus Stauffiger <markus@4eyes.ch>
 */


require_once(PATH_tslib.'class.tslib_pibase.php');

class tx_x4epersdb_pi6 extends tslib_pibase {
	var $prefixId = 'tx_x4epersdb_pi6';		// Same as class name
	var $scriptRelPath = 'pi6/class.tx_x4epersdb_pi6.php';	// Path to this script relative to the extension dir.
	var $extKey = 'x4epersdb';	// The extension key.
	var $pi_checkCHash = TRUE;

	/**
	 * Uid of the detail page
	 * @var integer
	 */
	var $detailPageUid = '';

	/**
	 * Name of the table containing the person
	 * @var string
	 */
	var $personTable = 'tx_x4epersdb_person';
	var $linkPageUid = 0;

	/**
	 * Text to display the link with
	 * @var string
	 */
	var $text = '-> Publikationen und Abschlussarbeiten bearbeiten';

	/**
	 * Initalizing variables
	 *
	 * @param string $content
	 * @param array $conf typoscript configuration
	 * @return void
	 */
	function init($content,$conf) {
		$this->conf = $conf;
		if ($this->conf['linkPageUid'] != '') {
			$this->linkPageUid = $this->conf['linkPageUid'];
		}
		if ($this->conf['extKey'] != '') {
			$this->extKey = $this->conf['extKey'];
			$this->personTable = 'tx_'.$this->extKey.'_person';
			$this->prefixId = 'tx_'.$this->extKey.'_pi6';
			$this->scriptRelPath = 'pi6/class.tx_'.$this->extKey.'_pi6.php';
		}
		if ($this->conf['text']) {
			$this->text = htmlentities($this->conf['text']);
		}
	}

	/**
	 * Renders the link to the linkpage
	 *
	 * @param string $content
	 * @param array $conf typoscript configuration
	 * @return string Link
	 */
	function main($content,$conf)	{
		$this->init($content,$conf);
		if ($GLOBALS['TSFE']->loginUser) {
			$person = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('*',$this->personTable,'feuser_id = '.$GLOBALS['TSFE']->fe_user->user['uid']);
			$person = $person[0];
			return '<p>'.$this->pi_linkToPage($this->text,$this->linkPageUid,'',array('tx_'.$this->extKey.'_pi1[showUid]'=>$person['uid'])).'</p>';
		}
	}

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/x4epersdb/pi6/class.tx_x4epersdb_pi6.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/x4epersdb/pi6/class.tx_x4epersdb_pi6.php']);
}
?>