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
 * Plugin 'PersonDB - display content of page' for the 'x4epersdb' extension.
 *
 * @author	Markus Stauffiger <markus@4eyes.ch>
 */

require_once(PATH_tslib.'class.tslib_pibase.php');
class tx_x4epersdb_pi5 extends tslib_pibase {
	var $prefixId = 'tx_x4epersdb_pi5';		// Same as class name
	var $scriptRelPath = 'pi5/class.tx_x4epersdb_pi5.php';	// Path to this script relative to the extension dir.
	var $extKey = 'x4epersdb';	// The extension key.
	var $pi_checkCHash = TRUE;

	/**
	 * Renders the content of a different page on this page
	 *
	 * @param string $content
	 * @param array  $conf    typoscript configuration array
	 * @return string html formated string
	 */
	function main($content,$conf)	{
		$this->template = $this->cObj->fileResource($GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_x4epersdb_pi1.']['detailView.']['templateFile']);
		$out = '';
		$page = $this->pi_getRecord('pages',intval($this->piVars['showContentPid']));
		if ($page['tx_templavoila_flex'] != '') {
			$contentArr = t3lib_div::xml2array($page['tx_templavoila_flex']);
			$contentArr = t3lib_div::trimExplode(',',$contentArr['data']['sDEF']['lDEF']['field_content']['vDEF']);
			foreach($contentArr as $content) {
				$elementconfig = array(
		            'source' => 'tt_content_'.$content,
		            'tables' => 'tt_content',
		            'dontCheckPid' => 1,
		        );
				$out .= $this->cObj->cObjGetSingle('RECORDS',$elementconfig);
			}
		}
       
        $authorId = t3lib_div::_GET('tx_x4epersdb_pi1');
		if ($authorId['showUid'] != '') {
			require_once('typo3conf/ext/x4epersdb/pi1/class.tx_x4epersdb_pi1.php');
	        $author = tx_x4epersdb_pi1::addPersonToPageTitle($this->pi_getRecord('tx_x4epersdb_person',intval($authorId['showUid'])),2);
		}
		return $this->pi_wrapInBaseClass($out);
	}

}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/x4epersdb/pi5/class.tx_x4epersdb_pi5.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/x4epersdb/pi5/class.tx_x4epersdb_pi5.php']);
}

?>