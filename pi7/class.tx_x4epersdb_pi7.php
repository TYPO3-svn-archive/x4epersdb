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
 * Plugin 'Person Info' for the 'x4epersdb' extension.
 *
 * @author	Markus Stauffiger <markus@4eyes.ch>
 */
require_once('typo3conf/ext/x4epibase/class.x4epibase.php');

class tx_x4epersdb_pi7 extends x4epibase {
	var $prefixId = 'tx_x4epersdb_pi7';		// Same as class name
	var $scriptRelPath = 'pi7/class.tx_x4epersdb_pi7.php';	// Path to this script relative to the extension dir.
	var $extKey = 'x4epersdb';	// The extension key.
	var $pi_checkCHash = TRUE;

	/**
	 * Uid ot the detail page
	 *
	 * @var integer
	 */
	var $detailPageUid;

	/**
	 * Name of the table containing the persons
	 * @var string
	 */
	var $table = 'tx_x4epersdb_person';

	/**
	 * Initalizing variables
	 *
	 * @param string $content
	 * @param array $conf typoscript configuration
	 * @return void
	 */
	function init($content,$conf) {
		$this->conf=$conf;

		if ($this->conf['extKey'] != '') {
			$this->extKey = $this->conf['extKey'];
			$this->table = 'tx_'.$this->extKey.'_person';
			$this->prefixId = 'tx_'.$this->extKey.'_pi7';
			$this->scriptRelPath = 'pi7/class.tx_'.$this->extKey.'_pi7.php';
		}

		$this->pi_setPiVarDefaults();
		$this->pi_loadLL();
		$this->pi_initPIflexForm();

	}

	/**
	 * Renders person information for use in x4eresearchgroup
	 *
	 * @param string $content
	 * @param array $conf typoscript configuration
	 * @return string Link
	 */
	function main($content,$conf)	{
		$this->init($content,$conf);

		global $TCA;
			// get template
		$template = $this->cObj->fileResource($this->conf['templateFile']);
		$template = $this->cObj->getSubpart($template,'###personView###');
		$out = '';

			$this->detailPageUid = $this->getTSFFvar('detailPageUid');

			$this->personUids=array();
		 	$this->personUids=array($this->piVars['showUid']);

				// get persons (all fields defined in feInterface, but without password
			if ($this->personUids != '') {
				$uids = $this->personUids;
				$i = 0;
				foreach($uids as $u) {
					$persons = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*',$this->table,'uid ='.$u.$this->cObj->enableFields($this->table));
					while($this->internal['currentRow'] = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($persons)) {
						foreach($this->internal['currentRow'] as $k => $v) {
							$tmpl = $this->cObj->getSubpart($template,'###'.$k.'_box###');
							if (($tmpl != '')) {
								if ($this->getFieldContent($k) == '') {
									$sp['###'.$k.'_box###'] = '';
								} else {
									$mArr[$k] = $this->getFieldContent($k);
									$mArr['listFieldHeader_'.$k] = $this->getFieldHeader($k);
									$sp['###'.$k.'_box###'] = $this->cObj->substituteMarkerArray($tmpl,$mArr,'###|###');
								}
							}
						}
						if ($this->pi_getFFvalue($this->cObj->data['pi_flexform'],'showDetailLink') && $this->detailPageUid) {
							$p['tx_'.$this->extKey.'_pi1[showUid]'] = $this->internal['currentRow']['uid'];
							$pA = t3lib_div::cHashParams($this->conf['persNav.']['1.']['addParams']);	// Added '.$this->linkVars' dec 2003: The need for adding the linkVars is that they will be included in the link, but not the cHash. Thus the linkVars will always be the problem that prevents the cHash from working. I cannot see what negative implications in terms of incompatibilities this could bring, but for now I hope there are none. So here we go... (- kasper)
							$p['cHash'] = t3lib_div::shortMD5(serialize($pA));
							$mArr['###linkStart###'] = str_replace('&nbsp;</a>','',$this->pi_linkToPage('&nbsp;',$this->detailPageUid,'',$p));
							$mArr['###linkEnd###'] = '</a>';
						} else {
							$mArr['###linkStart###'] = $mArr['###linkEnd###'] = '';
						}
						if($this->piVars['originPageID']){
							$mArr['###backLink###']='javascript:back();';
						}else{
							$mArr['###backLink###']='javascript:back();';
						}
						$out .= $this->cObj->substituteMarkerArrayCached($template,$mArr,$sp);
						$i++;
					}
				}
			//}
		}
		return $this->pi_wrapInBaseClass($out);
	}

	
	/**
	 * Field content, processed, prepared for HTML output.
	 *
	 * @param	string		Fieldname
	 * @return	string		Content, ready for HTML output.
	 */
	function getFieldContent($fN)	{
		switch($fN) {
			case 'image':
				$image='';
				if ($this->internal['currentRow']['image'])	{
					/*$imgArr = t3lib_div::trimExplode(',',$this->internal['currentRow']['image'],1);
					$GLOBALS['TSFE']->make_seed();
					$randval = intval(rand(0,count($imgArr)-1));
					$imgFile = 'uploads/x4epersdb/'.$imgArr[$randval];
					$imgInfo = getimagesize(PATH_site.$imgFile);
					if (is_array($imgInfo))	{
						$image='<img src="'.$imgFile.'" '.$imgInfo[3].' alt="'.$this->internal['currentRow']['name'].'" />';
					}*/
				}

				$imgTSConfig = $this->conf['images.'];
				$imgTSConfig['file'] = 'uploads/'.$this->extKey.'/'.$this->internal['currentRow']['image'];
				return $this->cObj->IMAGE($imgTSConfig);

				//return $image;
			break;
			case 'function':
				$f = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('title','function','uid ='.$this->internal['currentRow'][$fN].$this->cObj->enableFields('function'));
				return htmlspecialchars($f[0]['title']);
			break;
			case 'news':
			case 'research':
			case 'membership':
				return $this->pi_RTEcssText($this->internal['currentRow'][$fN]);
			break;
			case 'www':
			case 'email':
				return $this->cObj->gettypolink(htmlspecialchars($this->internal['currentRow'][$fN]),$this->internal['currentRow'][$fN]);
			break;
			case 'address':
			case 'office_address':
				return nl2br(htmlspecialchars($this->internal['currentRow'][$fN]));
			break;
			case 'office_country':
				if (strtoupper($this->internal['currentRow'][$fN]) != 'CH') {
					return $this->internal['currentRow'][$fN];
				} else {
					return '';
				}
			break;
			default:
				return htmlspecialchars($this->internal['currentRow'][$fN]);
			break;
		}
	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/x4epersdb/pi7/class.tx_x4epersdb_pi7.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/x4epersdb/pi7/class.tx_x4epersdb_pi7.php']);
}

?>