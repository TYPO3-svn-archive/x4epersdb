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
 * Plugin 'Personal Menu' for the 'x4epersdb' extension.
 *
 * @author	Markus Stauffiger <markus@4eyes.ch>
 */


require_once('typo3conf/ext/x4epibase/class.x4epibase.php');

class tx_x4epersdb_pi2 extends x4epibase {
	var $prefixId = 'tx_x4epersdb_pi2';		// Same as class name
	var $scriptRelPath = 'pi2/class.tx_x4epersdb_pi2.php';	// Path to this script relative to the extension dir.
	var $extKey = 'x4epersdb';	// The extension key.
	var $pi_checkCHash = TRUE;
	var $template = ''; // the template...
	var $pi_USER_INT_obj = true;
	
	/**
	 * Record of current person
	 *
	 * @var unknown_type
	 */
	var $person = array();

	/**
	 * PrefixId of the list/detail plugin
	 *
	 * @var string
	 */
	var $pi1prefixId = 'tx_x4epersdb_pi1';

	/**
	 * Name of the table containing the publication
	 * @var string
	 */
	var $personTable = 'tx_x4epersdb_person';

	/**
	 * PrefixId of the publication plugin
	 * @var string
	 */
	var $publicationExt = 'tx_x4epublication_pi1';

	/**
	 * Instance of publication db
	 *
	 * @var object
	 */
	var $publ = null;
	
	/**
	 * Array of research groups
	 *
	 * @var array
	 */
	var $researchGroups = array();
	


	/**
	 * Generates a personal menu including menu items defined by page in the "starting-point" field
	 * and adds additional personal-menu items of the selected user
	 *
	 * @param string $content
	 * @param array $conf Typoscript configuration
	 *
	 * @return string	HTML string containing the menu
	 */
	function main($content,$conf)	{

		if (!isset($_GET[$this->pi1prefixId]['showUid']) || intval($_GET[$this->pi1prefixId]['showUid'])==0) {
			return '';
		}
		$this->conf=$conf;
		$this->pi_setPiVarDefaults();
		$this->pi_loadLL();
		$this->pi_initPIflexForm();
		$this->template = $this->cObj->fileResource($this->conf['templateFile']);
		$this->template = $this->cObj->getSubpart($this->template, '###menu###');
		if ($this->template == '') {
			return 'No template found';
		}
		
			// add selected
		$this->conf['persNav.']['1.']['addParams'] = '&'.$this->pi1prefixId.'[showUid]='.intval($_GET[$this->pi1prefixId]['showUid']);
		$pA = t3lib_div::cHashParams($this->conf['persNav.']['1.']['addParams']);	// Added '.$this->linkVars' dec 2003: The need for adding the linkVars is that they will be included in the link, but not the cHash. Thus the linkVars will always be the problem that prevents the cHash from working. I cannot see what negative implications in terms of incompatibilities this could bring, but for now I hope there are none. So here we go... (- kasper)
		$this->conf['persNav.']['1.']['addParams'].= '&cHash='.t3lib_div::shortMD5(serialize($pA));
		$pids =	t3lib_div::trimExplode(',',$this->conf['persNav.']['special.']['value']);
			// add personal pages
		$user = t3lib_div::_GET($this->pi1prefixId);
		$user = $this->pi_getRecord($this->personTable,$user['showUid']);

		$pages = $this->getTSFFvar('displayPages');
		
		if ($pages != ''){
			$pids = array_merge($pids,t3lib_div::trimExplode(',',$pages));
		} else {
			$res = $GLOBALS['TSFE']->cObj->exec_getQuery('pages',Array('pidInList'=>$this->getTSFFvar('entryPageUid'),'orderBy'=>'sorting','where'=>'1'.$this->cObj->enableFields('pages')));
			while($mI = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
				array_push($pids,$mI['uid']);
			}
		}
		
		foreach($pids as $k => $v) {
			switch ($v) {
				case $this->conf['resumePageUid']: // = resume page
					if ($this->hasContent($user['resume_page'])) {
						$pids[$k] = $user['resume_page'];
					} else {
						$pids[$k] = '';
					}
				break;
				case $this->conf['researchPageUid']: // = resume page
					if ($this->hasContent($user['research_page'])) {
						$pids[$k] = $user['research_page'];
					} else {
						$pids[$k] = '';
					}
				break;
				case $this->conf['coursePageUid']: // = course page
					$pids[$k] = $this->getIsisID($user);
				break;
				case $this->conf['projectPageUid']: // = research page
					if (!$this->hasProjects($user)) {
						$pids[$k] = '';
					}
				break;
				case $this->conf['qualificationPageUid']: // = research page
					if (!$this->hasQualificationWorkings($user)) {
						$pids[$k] = '';
					}
				break;
				case $this->conf['publicationPageUid']:
					if (!$this->hasPublications($user)) {
						$pids[$k] = '';
					}
				break;
				case $this->conf['officeHourPageUid']:
					if (!$this->hasOfficeHours($user)){
						$pids[$k] = '';
					}
				break;
				case $this->conf['researchGroupPageUid']:
					if (!$this->hasResearchGroups($user)) {
						$pids[$k] = '';
					} else {
						
					}
				break;
				case $this->conf['displayOwnPageUid']: // add personal pages
					$personalPagesFolder = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('uid','pages','title = "Eigene Seiten" AND pid = '.intval($user['personal_page']));
					$res = $GLOBALS['TSFE']->cObj->exec_getQuery('pages',Array('pidInList'=>$personalPagesFolder[0]['uid'],'orderBy'=>'sorting'));
					while($mI2 = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
						array_push($pids,$mI2['uid']);
					}
				break;
				case $this->conf['congressPageUid']:
					if(!$this->hasCongresses($user)){
						$pids[$k] = '';
					} else {
					
					}
				break;
				default:
				break;
			}
		}
		
		$this->getCurrentPerson();

		$this->conf['persNav.']['special.']['value'] = implode(',',$pids);
		unset($pids);
		$this->conf['persNav.']['excludeUidList'] = $this->getTSFFvar('onlyShowIfSessionEQUserUid');

			// get user data
		require_once(PATH_tslib.'class.tslib_menu.php');
		$menu = t3lib_div::makeInstance('tslib_tmenu');
		$menu->parent_cObj = $this->cObj;	
		$menu->start($GLOBALS['TSFE']->tmpl,$GLOBALS['TSFE']->sys_page ,'',$this->conf['persNav.'],1);
		
		$menu->makeMenu();
		
		if ($this->conf['researchGroupPageUid']>0) {
			$this->addResearchPages($menu);
		}

		if ($this->conf['showPersPagesOnSinglePage']) {
			$this->showPersPagesOnSinglePage($menu,$user);
		}
		
		$mArr['###regularMenu###'] = $menu->writeMenu();		
		
		// get the menu with only the pages to show when userid = logged-in id
		if (($GLOBALS['TSFE']->loginUser) && ($this->getTSFFvar("onlyShowIfSessionEQUserUid") != '') && (($_GET[$this->pi1prefixId]['showUid'] == $this->person['uid']) || ($this->person['publadmin'] == 1) || ($this->person['qualiadmin'] == 1))) {
			$persOptT = $this->cObj->getSubpart($this->template,'###personalOptions###');
			$this->conf['persNav.']['special'] = 'list';
			$this->conf['persNav.']['special.']['value'] = $this->conf['persNav.']['excludeUidList'];
				// exclude publication and/or edit sites if person isn't edition itself or has the apropriate rights
			if ($_GET[$this->pi1prefixId]['showUid'] != $this->person['uid']) {
				$exclude = $this->conf['changePasswordPage'];
				if (!$this->person['publadmin']) {
					$exclude .= ','.$this->conf['editPublicationPages'];
				}
				if (!$this->person['qualiadmin']) {
					$exclude .= ','.$this->conf['editQualificationPages'];
				}
				$this->conf['persNav.']['excludeUidList'] = $exclude;
				unset($exclude);
			} else {
				$this->conf['persNav.']['excludeUidList'] = '';
			}
			$menu->start($GLOBALS['TSFE']->tmpl,$GLOBALS['TSFE']->sys_page ,'',$this->conf['persNav.'],1);
			$menu->makeMenu();
			$subP['###personalOptions###'] = $this->cObj->substituteMarker($persOptT,'###personalMenu###',$menu->writeMenu());
		} else {
			$subP['###personalOptions###'] = '';
		}
		
		$out =$this->cObj->substituteMarkerArrayCached($this->template,$mArr,$subP);
		$addTitle = '';
		if ($this->getTSFFvar('ResearchLinkTitle') != ''){
			$addTitle = "<h1>".$this->getTSFFvar('ResearchLinkTitle')."</h1>";
		}
		return $addTitle . $this->pi_wrapInBaseClass($out);
	}

	/**
	 * Modifies the links to display the content of a personal page on a given page
	 *
	 * @param object $menu Typoscript menu object
	 * @param array  $user Record of a person
	 *
	 * @return void
	 */
	function showPersPagesOnSinglePage(&$menu,&$user) {
		$count = 0;
		foreach($menu->menuArr as $k => $v) {
			switch ($v['uid']) {
				case $user['researchPageUid']: // = resume page
				case $user['resumePageUid']: // = resume page
				case $this->conf['coursePageUid']: // = course page
				case $this->conf['projectPageUid']: // = research page
				case $this->conf['qualificationPageUid']: // = research page
				case $this->conf['publicationPageUid']:
				case $this->conf['detailPageUid']:
				case $this->conf['researchGroupPageUid']:
				case $this->conf['congressPageUid']: // = congress page
				case $this->conf['officeHourPageUid']:
				break;
				default:
					if (!isset($menu->menuArr[$count]['_OVERRIDE_HREF'])) {
						if ($_GET['tx_x4epersdb_pi5']['showContentPid'] == $menu->menuArr[$count]['uid']) {
							$menu->menuArr[$count]['title'] = '<strong>'.$menu->menuArr[$count]['title'].'</strong>';
						}
						$menu->menuArr[$count]['_OVERRIDE_TARGET'] = '_self';
						$params['tx_x4epersdb_pi1[showUid]'] = $_GET['tx_x4epersdb_pi1']['showUid'];
						$params['tx_x4epersdb_pi5[showContentPid]'] = $v['uid'];
						// handle external page
						if ($v['doktype']==3) {
							//$menu->menuArr[$count]['_OVERRIDE_HREF'] = 'http://'.$menu->menuArr[$count]['url'];
							$menu->menuArr[$count]['_OVERRIDE_HREF'] = $menu->menuArr[$count]['url'];
							$menu->menuArr[$count]['_OVERRIDE_TARGET'] = '_blank';
						} else {
							$lConf = array(
								'parameter'=>$this->conf['displayOwnPageUid'],
								'additionalParams'=>'
									&tx_x4epersdb_pi1[showUid]='.$_GET['tx_x4epersdb_pi1']['showUid'].'
									&tx_x4epersdb_pi5[showContentPid]='.$v['uid'],
								'useCacheHash'=>1
							);
							$menu->menuArr[$count]['_OVERRIDE_HREF'] = $this->cObj->typolink_url($lConf);
						}
					}
				break;
			}
			$count++;
		}
	}
	
	/**
	 * Gets current (logged in) person
	 *
	 * @return 	void
	 */
	function getCurrentPerson() {
		if ($GLOBALS['TSFE']->fe_user->user['uid'] != '') {
			$person = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('*',$this->personTable,'feuser_id='.$GLOBALS['TSFE']->fe_user->user['uid'].$this->cObj->enableFields($this->personTable));
			$this->person = $person[0];
		}
	}

	/**
	 * Checks whether user has publications, only relevant if x4epublication is installed
	 *
	 * @todo Check if x4epublication is installed, otherwise return false
	 *
	 * @param 	array	$user	User record
	 * @return 	boolean
	 */
	function hasPublications(&$user) {
		$this->makePublicationInstance();
		return $this->publ->hasPublication($user['uid']);
	}

	/**
	 * Makes a publication-db instance. Uses member variable to avoid multiple
	 * instances of publ-db
	 *
	 * @todo Check if x4epublication is installed
	 *
	 * @return void
	 */
	function makePublicationInstance() {
		if ($this->publ == null) {
			require_once(PATH_typo3conf.'ext/x4epublication/pi1/class.'.$this->publicationExt.'.php');
			$this->publ = t3lib_div::makeInstance($this->publicationExt);
			$this->publ->cObj = $this->cObj;
		}
	}

	/**
	 * Checks whether user has projects, only relevant if x4eprojectsgeneral is installed
	 * 
	 * @param 	array	$user	User record
	 * @return 	boolean
	 */
	function hasProjects(&$user) {
		$ret = false;
		if (t3lib_extMgm::isLoaded('x4euniprojectsgeneral')) {
			$q = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('count(*)','tx_x4euniprojectsgeneral_list','1 AND (FIND_IN_SET('.$user['uid'].',tx_x4euniprojectsgeneral_list.projectmanagement) OR FIND_IN_SET('.$user['uid'].',tx_x4euniprojectsgeneral_list.personsinvolved))'.$this->cObj->enableFields('tx_x4euniprojectsgeneral_list'));
			if ($q[0]['count(*)'] > 0) {
				$ret = true;
			}
		}
		return $ret;
	}

	/**
	 * Checks whether user has qualification workings, only relevant if x4equalificationgeneral is installed
	 *
	 * @todo Check if x4equalificationgeneral is installed, otherwise return false
	 *
	 * @param 	array	$user	User record
	 * @return 	boolean
	 */
	function hasQualificationWorkings(&$user) {
		$q = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('count(*)','tx_x4equalificationgeneral_list','1 AND FIND_IN_SET('.$user['uid'].',tx_x4equalificationgeneral_list.organizer)'.$this->cObj->enableFields('tx_x4equalificationgeneral_list'));
		if ($q[0]['count(*)'] > 0) {
			return true;
		}
		return false;
	}
	
	/**
	 * Checks whether user has office hours, only relevant if x4eofficehour is installed
	 * 
	 * @todo Check if x4eofficehour is installed, otherwise return false
	 *
	 * @param 	array	$user	User record
	 * @return 	boolean
	 */
	function hasOfficeHours(&$user){
		$q = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('count(*)','tx_x4eofficehour_events', 'owner ='.$user['uid'].' AND startd >= '.strtotime("today").$this->cObj->enableFields('tx_x4eofficehour_events'));
		if ($q[0]['count(*)'] > 0) {
			return true;
		}
		return false;
	}
	
	/**
	 * Checks whether user has congresses, only relevant if x4econgress is installed
	 *
	 * @todo Check if x4econgress is installed, otherwise return false
	 *
	 * @param 	array	$user	User record
	 * @return 	boolean
	 */
	function hasCongresses(&$user) {
	$headSubQ = $GLOBALS['TYPO3_DB']->SELECTquery('uid_local','tx_x4econgress_congresses_persons_mm','uid_foreign = '.intval($user['uid']));
		$memberSubQ = $GLOBALS['TYPO3_DB']->SELECTquery('uid_local','tx_x4econgress_congresses_persons_mm','uid_foreign = '.intval($user['uid']));
		$this->researchGroups = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('*','tx_x4econgress_congresses','(uid IN ('.$headSubQ.') OR uid IN ('.$memberSubQ.'))'.$this->cObj->enableFields('tx_x4econgress_congresses'));
		if (isset($this->researchGroups[0]['uid'])) {
			return true;
		
		} else {
			return false;
		}
	}
	
	/**
	 * Checks whether user is a group leader, only relevant if x4eresearchgroup is installed
	 *
	 * @todo Check if x4econgress is installed, otherwise return false
	 * 
	 * @author Manuel Kammermann <manuel@4eyes.ch>
	 * @param 	array	$user	User record
	 * @return 	boolean
	 */
	function isGroupLeader(&$user){
		$q = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('count(*)','tx_x4eresearch_researchgroup_head_mm','uid_foreign IN ('.$user['uid'].')');
		if ($q[0]['count(*)'] > 0) {
			return true;
		}
		return false;
	}

	/**
	 * Checks whether link is supposed to show
	 *
	 * @param 	array	$user	User record
	 * @return 	integer		Page uid or nothing
	 */
	function getIsisID($user) {
		if ($user['lecture_link'] != '') {
			return $this->conf['coursePageUid'];
		}
	}
	
	/**
	 * Checks if the given page contains active content.
	 *
	 * @param int $pageUid
	 * @return boolean
	 */
	function hasContent($pageUid) {
		$page = $this->pi_getRecord('pages',intval($pageUid));
		if ($page['tx_templavoila_flex'] != '') {
			$contentArr = t3lib_div::xml2array($page['tx_templavoila_flex']);
			$contentArr = t3lib_div::trimExplode(',',$contentArr['data']['sDEF']['lDEF']['field_content']['vDEF'],1);
			if(count($contentArr) > 0) {
				$numContent = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('count(uid)','tt_content','uid IN ('.implode(',',$contentArr).')'.$GLOBALS['TSFE']->cObj->enableFields('tt_content'));
				if($numContent[0]['count(uid)']>0) {
					return true;
				} else {
					return false;
				}
			} else {
				return false;
			}
			
		}
	}
	
	/**
	 * Checks if user is in any researchgroups, only relevant if x4eresearchgroup is installed
	 *
	 * @todo Check if x4econgress is installed, otherwise return false
	 *
	 * @param array $user
	 * @return boolean
	 */
	function hasResearchGroups(&$user) {
	
		$lang = intval(t3lib_div::GPvar('L'));
		
		$addWhere = ' sys_language_uid = '.$lang;
		
		$headSubQ = $GLOBALS['TYPO3_DB']->SELECTquery('uid_local','tx_x4eresearch_researchgroup_head_mm','uid_foreign = '.intval($user['uid']));
		$memberSubQ = $GLOBALS['TYPO3_DB']->SELECTquery('uid_local','tx_x4eresearch_researchgroup_members_mm','uid_foreign = '.intval($user['uid']));
		$this->researchGroups = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('*','tx_x4eresearch_researchgroup','(uid IN ('.$headSubQ.') OR uid IN ('.$memberSubQ.'))'.$this->cObj->enableFields('tx_x4eresearch_researchgroup').' AND '.$addWhere);
		if (isset($this->researchGroups[0]['uid'])) {
			return true;
		
		} else {
			return false;
		}
	}
	
	/**
	 * Returns the page uid for the researchgroups as many
	 * times as the user is in a researchgroup, only relevant if x4eresearchgroup is installed
	 *
	 * @todo Check if x4econgress is installed, otherwise return false
	 *
	 * @param object $menu	Typoscript menu object (by reference)
	 * @retun void
	 */
	function addResearchPages(&$menu) {
		if (count($this->researchGroups)>0) {
			
			$count = 0;
			$menuArr = array();
			//Saving the menu structure temp.
			$tempMenuRes = $menu->result;
			//Clear the menu structure...
			unset($menu->result);
			$menu->result = array();
			//...and reassemble it by going through each menu point... 
			foreach($menu->menuArr as $k => $v) {
				switch ($v['uid']) {
					case $this->conf['researchGroupPageUid']:
						
						foreach($this->researchGroups as $rg) {
							$menuElement['title'] = $rg['name'];
							
							$menuElement['_OVERRIDE_TARGET'] = '_self';
							$params['tx_x4eresearch_pi1[showUid]'] = $rg['uid'];
							$menuElement['_OVERRIDE_HREF'] = $GLOBALS['TSFE']->cObj->getTypoLink_URL($this->conf['researchGroupDetailPageUid'],$params);
							$menuArr[]=$menuElement;
							//If a new research group page is inserted into the menu, a not active menu point is inserted into the menu structure 
							$menu->result[] = $tempMenuRes[$count];
						}
					break;
					default:
						//Keep the old menu point...
						$menu->result[] = $tempMenuRes[$count];
						$menuArr[]=$v;
					break;
				}
				$count++;
			} 
			$menu->menuArr = $menuArr;
		}
	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/x4epersdb/pi2/class.tx_x4epersdb_pi2.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/x4epersdb/pi2/class.tx_x4epersdb_pi2.php']);
}

?>