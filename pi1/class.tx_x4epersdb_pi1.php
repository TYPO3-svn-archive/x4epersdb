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
 * Plugin '4eyes Person database' for the 'x4epersdb' extension.
 *
 * @author	Markus Stauffiger <markus-at-4eyes.ch>
 */

require_once('typo3conf/ext/x4epibase/class.x4epibase.php');
class tx_x4epersdb_pi1 extends x4epibase {
	var $prefixId = 'tx_x4epersdb_pi1';		// Same as class name
	var $scriptRelPath = 'pi1/class.tx_x4epersdb_pi1.php';	// Path to this script relative to the extension dir.
	var $extKey = 'x4epersdb';	// The extension key.
	var $pi_checkCHash = TRUE;

	/**
	 * Name of the table containing the persons
	 * @var string
	 */
	var $table = 'tx_x4epersdb_person';

	/**
	 * Name of the table containing the functions
	 * @var string
	 */
	var $functionTable = 'tx_x4epersdb_function';

	/**
	 * Name of the table containing the departments
	 *
	 * @var string
	 */
	var $departmentTable = 'tx_x4epersdb_department';
        var $affiliationTable =	'tx_x4epersdb_department';

	/**
	 * Name of the table containing the mm relationships between
	 * persons (uid_local) and departments (uid_foreign)
	 *
	 * @var string
	 */
	var $departmentTableMM = 'tx_x4epersdb_person_department_mm';

	/**
	 * PHP Script which handles the publications
	 * @var sting
	 */
	var $publicationScript = 'typo3conf/ext/x4epublication/pi1/class.tx_x4epublication_pi1.php';

	/**
	 * Name of the publication plugin
	 * @var string
	 */
	var $publicationPrefixId = 'tx_x4epublication_pi1';

	/**
	 * Name of the table containing the publications
	 * @var string
	 */
	var $publicationTable = 'tx_x4epublication_publication';

	/**
	 * Name of the table containing the mm relationships between
	 * persons (authors) and publications
	 * @var <type>
	 */
	var $publicationAuthorMM = 'tx_x4epublication_publication_persons_auth_mm';

	/**
	 * Enables debug functionality (if implemented :))
	 *
	 * @var boolean
	 */
	var $debug = false;

	/**
	 * Overriding the person and publicationssettings from the member variables
	 * otherwise just calling the parent init function
	 *
	 * @param   string  $content
	 * @param   array   $conf   Typoscript configuration
	 *
	 * @return void
	 */
	function init($content,$conf) {
		if ($this->conf['publDB.']['extKey'] != '') {
			$this->publicationScript = 'typo3conf/ext/'.$this->conf['publDB.']['extKey'].'/pi1/class.tx_'.$this->conf['publDB.']['extKey'].'_pi1.php';
			$this->publicationPrefixId = 'tx_'.$this->conf['publDB.']['extKey'].'_pi1';
			$this->publicationTable = 'tx_'.$this->conf['publDB.']['extKey'].'_publication';
			$this->publicationAuthorMM = 'tx_'.$this->conf['publDB.']['extKey'].'_publication_persons_auth_mm';
		}
		parent::init($content,$conf);
	}

	/**
	 * Setting various configuration values and handles the delivery of
	 * the correct view
	 *
	 * @param   string  $content
	 * @param   array   $conf   Typoscript configuration
	 *
	 * @return  string  HTML-Output of the plugin
	 */
	function main($content,$conf)	{
		$this->conf = $conf;
		$this->init($content,$conf);
		$this->pi_initPIflexForm();
		$this->pi_loadLL();

		// TODO
		// clean up and correct source for options -> typoscript!
		$this->internal['searchFieldList']=$this->pi_getFFvalue($this->cObj->data['pi_flexform'],'field_orderList');
		$this->internal['orderByList']=$this->internal['searchFieldList'];
		$this->internal['currentTable'] = $this->table;
		$this->manualFieldOrder = $this->pi_getFFvalue($this->cObj->data['pi_flexform'],'field_manualOrder') ? TRUE : FALSE;
		$this->manualFieldOrder_details = t3lib_div::trimExplode(',',$this->pi_getFFvalue($this->cObj->data['pi_flexform'],'field_orderDetails'),1);
		$this->manualFieldOrder_list = t3lib_div::trimExplode(',',$this->getTSFFvar('field_orderList'),1);
		$this->tableName = $this->table;
		
		if (strstr($this->cObj->currentRecord,'tt_content'))	{
			$this->conf['pidList'] = $this->cObj->data['pages'];
		}
		$this->conf['recursive'] = 0;
			// add publication folders
		if ($this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'publicationUids') != '') {
			$this->conf['publications']['pidList'] = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'publicationUids');
		}
			// add fe_user_folder
		if ($this->getTSFFvar('feUserPageUids') != '') {
			$this->conf['feUsers']['pidList'] = $this->getTSFFvar('feUserPageUids');
		}

			// add viewMode
		if ($this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'modeSelection') != '') {
			$this->conf['listView.']['viewMode'] = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'modeSelection');
		}
		$this->internal['showFirstLast'] = $this->conf['listView.']['showFirstLast'];
		if (($this->piVars['showUid'] != '') && ($this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'modeSelection') > 1))	{
			
			if($this->getTSFFvar('templateFile') == ''){
					$this->template = $this->cObj->fileResource($this->conf['detailView.']['templateFile']);
			}else{
				$this->template = $this->cObj->fileResource($this->getTSFFvar('templateFile'));
			}
			
			if ($this->debug && ($this->template == '')) {
				return "Detail template not found or empty";
			}

			list($t) = explode(':',$this->cObj->currentRecord);
			$this->internal['currentTable']=$t;
			$this->internal['currentRow']=$this->cObj->data;
			switch ($this->conf['listView.']['viewMode']) {
				case '2':
					$content = $this->singleView('left');
				break;
				default:
					$content = $this->singleView('right');
				break;
			}
		} else {
				// add detailPageUid
			if ($this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'detailPageUid') != '') {
				$this->conf['listView.']['detailPageUid'] = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'detailPageUid');
			}
			$this->template = $this->cObj->fileResource($this->conf['listView.']['templateFile']);
			if ($this->debug && ($this->template == '')) {
				return "List template not found or empty";
			}
			// handle different list modes
			switch($this->conf['listView.']['viewMode']) {
				case '1':
					$content = $this->listViewByFunction();
				break;
				case 'listByDepartment':
					$content = $this->listViewByDepartment();
				break;
				case 4:
					$content = $this->listAlumni();
				break;
				default:
					$content = $this->listView();
				break;
			}
		}
		return $this->pi_wrapInBaseClass($content);
	}

	/**
	 * Displaying a single person's record
	 *
	 * @param   string  $side   Defining which subpart to load
	 *
	 * @return  string  HTML-Ouput
	 */
	function singleView($side) {
		global $TCA;
		t3lib_div::loadTCA($this->table);
		$this->piVars['pointer'] = 0;
		$localTmpl = $this->cObj->getSubpart($this->template,'###'.$side.'###');

		$this->viewMode = 'singleView';
			// Make listing query, pass query to MySQL:
		$addWhere = ' AND uid = '.intval($this->piVars['showUid']);
		$this->conf['pidList'] = $this->conf['feUsers']['pidList'];
		$query = $this->pi_list_query($this->table,0,$addWhere);

		$res = $GLOBALS['TYPO3_DB']->sql_query($query);
		$data = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
		$this->internal['currentRow'] = $data;
		
		// get language id
		$lang = intval(t3lib_div::GPvar('L'));

		if($lang > 0){
			$olMode = 'strict';
			$this->internal['currentRow'] = $GLOBALS['TSFE']->sys_page->getRecordOverlay($this->table,$this->internal['currentRow'],$lang,$olMode);
		}

			// redirect if person is not available anymore
		if (!isset($data['uid'])) {
			$this->personNotAvailableRedirect();
		}
		
		// setting default image
		if ($data['image'] == ''){
			$data['image'] = 'default|'.$this->conf['detailView.']['defaultImage'];
		}
		
		// add all columns to marker-array
		foreach($data as $key => $value) {
			$tmpl = $this->cObj->getSubpart($localTmpl,'###'.$key.'_box###');
			if ($value != '') {
				switch ($key) {
					case 'image':
 						$temp = explode('|',$data['image']);
						if($temp['0'] == 'default'){
							// show default image
							$imageconf["file"] = $temp['1'];
							$mArr['###'.$key.'###'] = $this->cObj->IMAGE($imageconf);
							unset($imageconf);
						}else{
							// add image according to ts-setup
							$imageconf = $this->conf['detailView.']['image.'];
							$imageconf["file"] = 'uploads/'.$this->extKey.'/'.$value;
							$mArr['###'.$key.'###'] = $this->cObj->IMAGE($imageconf);
							unset($imageconf);
						}
					break;
					default:
						$mArr['###'.$key.'###'] = $this->getFieldContent($key);
					break;
				}
				
				$mArr['###listFieldHeader_'.$key.'###'] = $this->getFieldHeader($key);
				$sub['###'.$key.'_box###'] = $this->cObj->substituteMarkerArray($tmpl,$mArr);
			} else {
				$sub['###'.$key.'_box###'] = '';
			}
		}
		
		$sub['###fgname_box###'] = $this->getResearchgroupNames($data['uid']);
		$sub['###officeBox###'] = $this->getOffice($data);
		$mArr = array();

		$sub['###lastestPublications_box###'] = '';
			// only add latest publications if in "left"-mode
		if (($side == 'left') && ($this->publicationScript != '') && t3lib_extMgm::isLoaded('x4epublication')) {


				// Get latest publications (if any)
			$subQ = $GLOBALS['TYPO3_DB']->SELECTquery('uid_local',$this->publicationAuthorMM,'uid_foreign='.intval($this->internal['currentRow']['uid']));
			$where = 'uid IN ('.$subQ.')'.$this->cObj->enableFields($this->publicationTable);
			$count = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('count(*)',$this->publicationTable,$where);
				// if there are no publications or publications are not supposed to be shown

			if ($count[0]['count(*)'] > 0 && $this->internal['currentRow']['showpublics'] ) {
				require_once($this->publicationScript);
				$publ = t3lib_div::makeInstance($this->publicationPrefixId);
				$publ->cObj = t3lib_div::makeInstance('tslib_cObj');
				$this->conf['detailView.']['publication.']['pidList'] = $this->conf['publications']['pidList'];
				$publ->init('',$this->conf['detailView.']['publication.']);
				$tmpTmpl = $this->cObj->getSubpart($this->template,'###lastestPublications_box##');
				$tmpMar['###lastestPublications###'] = $publ->getLatestByAuthor($this->internal['currentRow']['uid'],$this->conf['detailView.']['numberOfLatestPublications']);

				$tmpMar['###allPublicationByAuthorLinkStart###'] = str_replace('&nbsp;</a>','',$this->pi_linkTP_keepPIvars('&nbsp;',array(),1,0,$this->getTSFFvar('allPublicationsSiteUid')));
				$tmpMar['###allPublicationByAuthorLinkEnd###'] = '</a>';

				$sub['###lastestPublications_box###'] = $this->cObj->substituteMarkerArray($tmpTmpl,$tmpMar);
				unset($tmpTmpl,$tmpMar);
			}
			unset($subQ,$where,$count);
			$this->addPersonToPageTitle($data);
		}
		if (($side=='left') && ($this->publicationScript == '' )) {
			$this->addPersonToPageTitle($data);
		}

		unset($linkParam);
		$this->removeEmptyBlocks($sub,$data);
		return $this->cObj->substituteMarkerArrayCached($localTmpl,$mArr,$sub);
	}

	/**
	 * Return a list of researchgroups (only works with x4eresearch installed)
	 *
	 * @author Matyas Filep
	 * @param	integer $persID
	 * @return	string
	 */
	function getResearchgroupNames($persID){
		if (t3lib_extMgm::isLoaded('x4eresearch')) {
			// idea: create instance of x4eresearch_pi1 and run renderCategory
			require_once('typo3conf/ext/x4eresearch/pi1/class.tx_x4eresearch_pi1.php');
			$research = t3lib_div::makeInstance('tx_x4eresearch_pi1');
			$research->cObj = t3lib_div::makeInstance('tslib_cObj');
			$research->main('',$research->conf);
			$research->overrideMethod['renderCategory']=false;
			$stuff=$research->listRelatedResearchgroups($persID);
			return($stuff);
		} else {
			return '';
		}
	}

	/**
	 * Removes empty markers
	 *
	 * @param	array	$sub	Array of subparts (by reference)
	 * @param	array	$record Person record (by reference)
	 *
	 * @return void
	 */
	function removeEmptyBlocks(&$sub,&$record) {

		if (isset($this->conf['detailView.']['blocks.'])) {
			foreach($this->conf['detailView.']['blocks.'] as $k => $v) {
				$found = false;
				foreach(t3lib_div::trimExplode(',',$v['fields']) as $w) {
					if ($record[$w] != '') {
						$found = true;
					}
				}
				if (!$found) {
					$sub['###'.substr($k,0,strlen($k)-1).'_box###'] = '';
				}
			}
		}
	}

	/**
	 * Adds person's name etc. to page title
	 *
	 * @param	reference	$author		Reference to data-array to avoid reloading
	 * @param 	int			$mode		Mode of title-modification: 0: replace, 1: prepend
	 * @param 	string		$localTmpl	Template to use
	 * @return	string					HTML-String... if you want the "rendered" author back to use e.g. for the content-header
	 */
	function addPersonToPageTitle(&$author,$mode=0,$localTmpl='') {
		if ($this->cObj == null) {
			$this->cObj = $GLOBALS['TSFE']->cObj;
		}
		if ($localTmpl == '') {
			$localTmpl = $this->cObj->getSubpart($this->template,'###pageTitle###');
		}
		$title = '';
		if (is_array($author)) {
			foreach($author as $key => $value) {
				$tmpl = $this->cObj->getSubpart($localTmpl,'###'.$key.'_box###');
				if (($tmpl != '') && ($value != '')) {
					$subNoEntities['###'.$key.'_box###'] = $this->cObj->substituteMarker($tmpl,'###'.$key.'###',$value);
					$sub['###'.$key.'_box###'] = $this->cObj->substituteMarker($tmpl,'###'.$key.'###',($value));			// before: htmlentities($value)

				} else {
					$sub['###'.$key.'_box###'] = $subNoEntities['###'.$key.'_box###'] = '';
				}
			}
		}
			// handle selected mode
		switch($mode) {
			case 1:
				$GLOBALS['TSFE']->page['title'] = $this->cObj->substituteMarkerArrayCached($localTmpl,array(),$subNoEntities).$GLOBALS['TSFE']->page['title'];
			break;
			case 2:
				$GLOBALS['TSFE']->page['title'] = $GLOBALS['TSFE']->page['title'].$this->cObj->substituteMarkerArrayCached($localTmpl,array(),$subNoEntities);
			break;
			default:
				$GLOBALS['TSFE']->page['title'] = $this->cObj->substituteMarkerArrayCached($localTmpl,array(),$subNoEntities);
			break;
		}
		return $this->cObj->substituteMarkerArrayCached($localTmpl,array(),$sub);
	}

	/**
	 * List view, listing the records from the table.
	 * Does also provide the single view if the "showUid" piVar is set.
	 *
	 * @return	string		HTML content for the listing.
	 */
	function listView()	{
		$this->lConf = $lConf = $this->conf['listView.'];	// Local settings for the listView function
		$this->pi_alwaysPrev = $this->lConf['alwaysPrev'];
		$this->viewMode = 'listView';

		$hookObjectsArr = array();
		if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['x4epersdb/pi1/class.tx_x4epersdb_pi1.php']['listView'])) {
			foreach($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['x4epersdb/pi1/class.tx_x4epersdb_pi1.php']['listView'] as $classRef) {
				$hookObjectsArr[] = &t3lib_div::getUserObj($classRef);
			}
		}

		foreach($hookObjectsArr as $hookObj) {
			if (method_exists($hookObj, 'preListView')) {
				$hookObj->preListView($this);
			}
		}

		if (!isset($this->piVars['pointer']))	$this->piVars['pointer']=0;
		if (!isset($this->piVars['sort']))	$this->piVars['sort']='lastname';

			// Initializing the query parameters:
		list($this->internal['orderBy'],$this->internal['descFlag']) = explode(':',$this->piVars['sort']);
		$this->internal['results_at_a_time']=t3lib_div::intInRange($lConf['results_at_a_time'],0,1000,3);		// Number of results to show in a listing.
		$this->internal['maxPages']=t3lib_div::intInRange($lConf['maxPages'],0,1000,2);;		// The maximum number of "pages" in the browse-box: "Page 1", 'Page 2', etc.
		$this->internal['results_at_a_time'] = 1000;

			// generate additional where statement
		$addWhere = $this->getDepartmentWhere();
			// Generate additional where statement
		if ($functionUid) {
			$addWhere .= ' AND '.$this->functionTable.' = '.$functionUid;
		}

		if ($this->getTSFFvar('hideAlumni') == 1) {
			$addWhere .= ' AND '.$this->table.'.alumni=0';
		}
		
		// sort out alumnis which are defined by function
		if(!empty($this->conf['alumniFuncUid'])){
			$addWhere .= ' AND FIND_IN_SET(\''.$this->conf['alumniFuncUid'].'\','.$this->table.'.function) = 0';
			
		}

		$start = intval($this->piVars['start']);
		$end = intval($this->piVars['end']);

		if ($start == 0 && $this->getTSFFVar('startListWithFirstLetterCombo')) {
			$this->piVars['start'] = 65;
			$start=65;
			$end=$start+$this->getTSFFVar('alphabeticPageBrowserStepSize')-1;
			$this->piVars['end'] = $end;
		}
		if ($this->piVars['sword'] != '') {
			$this->piVars['start'] = '';
			$this->piVars['end'] = '';
		} elseif ($start > 0 || $this->getTSFFVar('startListWithFirstLetterCombo')) {
			if (t3lib_div::intInRange($start,64,86) && t3lib_div::intInRange($end,68,90)) {
				$addWhere .= ' AND SUBSTRING(lastname,1) >= "'.chr($start).'" AND SUBSTRING(lastname,1) <= "'.chr($end+1).'"';
			}
		}
		
			// handle translated person-records
		$addWhere .= " AND sys_language_uid = 0";

			// Get number of records:
		$this->conf['pidList'] = $this->conf['feUsers']['pidList'];

		foreach($hookObjectsArr as $hookObj) {
			if (method_exists($hookObj, 'modifyWhereClause')) {
				$hookObj->modifyWhereClause($addWhere, $this);
			}
		}

		$query = $this->pi_list_query($this->table,1,$addWhere);
		$res = $GLOBALS['TYPO3_DB']->sql_query($query);
		list($this->internal['res_count']) = $GLOBALS['TYPO3_DB']->sql_fetch_row($res);

		if ($this->internal['res_count'] > 0) {
			$subArr['###list###'] = $this->generateList($addWhere);
			$mArr['###noResultFound###'] = '';
		} else {
			$subArr['###list###'] = '';
			$mArr['###noResultFound###'] = $this->noResultFound();
		}
			// Adds the search box:
		$mArr['###search###'] = $this->pi_list_searchBox();

			// Adds the result browser:
		$wrapArr = $this->conf['listView.'];
		$mArr['###pageBrowser###'] = $this->alphabeticPageBrowser($this->getTSFFVar('alphabeticPageBrowserStepSize'));
		$tmpl = $this->cObj->getSubpart($this->template,'###listView###');

		foreach($hookObjectsArr as $hookObj) {
			if (method_exists($hookObj, 'modifyMarkers')) {
				$hookObj->modifyMarkers($mArr, $tmpl, $this);
			}
		}

		return $this->cObj->substituteMarkerArrayCached($tmpl,$mArr,$subArr);
	}

	/**
	 * Returns a list of person. Handles sorting by function if necessary
	 *
	 * @param 	String	$addWhere	Additional where clause
	 * @return 	String				List of persons (HTML)
	 */
	function generateList($addWhere) {
		if ($this->internal['orderBy'] == 'function') {
			// get reverse order
			$orderByDir = '';
			if ($this->internal['descFlag']==1) {
				$orderByDir = ' DESC';
			}
			$funcs = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid,title',$this->functionTable,'1'.$this->cObj->enableFields($this->functionTable),'','title'.$orderByDir);

			// now make regular lists and connect them
			$this->internal['orderBy'] = 'lastname';
			$this->internal['desc'] = 0;
			$queries = array();
			if ($addWhere == '') {
				$addWhere = '1 ';
			}
			while($f = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($funcs)) {
				$funcAddWhere = ' AND FIND_IN_SET('.$f['uid'].',function)';

				$persRes = $this->pi_exec_query($this->table,0,$addWhere.$funcAddWhere);
				while($p = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($persRes)) {
					$persons[] = $p;
				}
			}

			$this->internal['orderBy'] = 'function';
			if ($orderByDir != '') {
				$this->internal['descFlag'] = 1;
			}
			return $this->pi_list_makelist(null,$persons);
		} else {
			$res = $this->pi_exec_query($this->table,0,$addWhere);

				// Adds the whole list table
			return $this->pi_list_makelist($res);
		}
	}

	/**
	 * Returns a list of persons, sorted by department
	 *
	 * @return string	HTML, list of persons
	 */
	function listViewByDepartment() {
		$departments = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*',$this->departmentTable,'pid IN ('.$this->conf['feUsers']['pidList'].')'.$this->cObj->enableFields($this->departmentTable));
		$tmplBak = $this->template;
		$this->template = $this->cObj->getSubpart($this->template,'###listViewByDepartment###');
		$departmentT = $this->cObj->getSubpart($this->template,'###department###');
		
		$jumpBoxT = $this->cObj->getSubpart($tmplBak,'###jumpToDepartmentBox###');
		$departmentJumpT = $this->cObj->getSubpart($jumpBoxT,'###jumpToDepartment###');
		
		if ($this->template == ''){
			return 'No template for list view by department found';
		}
		$out = '';
		while($dep = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($departments)) {
			$addWhere = ' AND uid IN ('.$GLOBALS['TYPO3_DB']->SELECTquery('uid_local',$this->departmentTableMM,'uid_foreign = '.$dep['uid']).')';
						
						
								// Get number of records:
			$this->conf['pidList'] = $this->conf['feUsers']['pidList'];
			$query = $this->pi_list_query($this->table,1,$addWhere);
			$res = $GLOBALS['TYPO3_DB']->sql_query($query);

			list($this->internal['res_count']) = $GLOBALS['TYPO3_DB']->sql_fetch_row($res);
			
			$numRes += $this->internal['res_count'];
			if ($this->internal['res_count'] > 0) {
				$mArr['###departmentAnchor###'] = $_SERVER['REQUEST_URI'].'#department_'.$dep['uid'];
				$mArr['###depAnchor###'] = 'department_'.$dep['uid'];
				$mArr['###departmentTitle###'] = $dep['title'];
				$sub['###listViewByFunction###'] = $this->listViewByFunction('',$addWhere);
	
				if (trim($sub['###listViewByFunction###']) != '') {
					$subArr['###department###'] .= $this->cObj->substituteMarkerArrayCached($departmentT,$mArr,$sub);		
				}
				
				$t['###subDepartments###'] .= $this->cObj->substituteMarkerArray($departmentJumpT,$mArr);
			} else {
				
			}
		}
		
		$tmplBak2 = $this->template;
		$this->template = $tmplBak;
		$mArr['###search###'] = $this->pi_list_searchBox();
		
		if ($numRes > 0) {
			$mArr['###noResultFound###'] = '';
			$subArr['###jumpToDepartmentBox###'] = $this->cObj->substituteSubpart($jumpBoxT,'###jumpToDepartment###',$t['###subDepartments###']);
		} else {
			$mArr['###noResultFound###'] = $this->noResultFound();
			$subArr['###jumpToDepartmentBox###'] = '';
			$subArr['###department###'] = '';
		}
						
		$this->template = $tmplBak2;
		return $this->cObj->substituteMarkerArrayCached($this->template,$mArr,$subArr);
	}
	
	
	/**
	 * List view, listing the records from the table.
	 * Does also provide the single view if the "showUid" piVar is set.
	 *
	 * @param	string		HTML input content - not used, ignore.
	 * @param	array		TypoScript configuration array
	 * @return	string		HTML content for the listing.
	 */
	function listViewByFunction($table = '',$addWhere='')	{
		if ($table=='') {
			$table = $this->functionTable;
		}

		// init
		$this->lConf = $lConf = $this->conf['listView.'];	// Local settings for the listView function
		$this->pi_alwaysPrev = $this->lConf['alwaysPrev'];
		$this->viewMode = 'listView';
			// Initializing the query parameters:
		list($this->internal['orderBy'],$this->internal['descFlag']) = explode(':',$this->piVars['sort']);
		if ($this->internal['orderBy'] == '') {
			$this->internal['orderBy'] = 'lastname';
		}
		$this->internal['results_at_a_time']=999;
		$this->internal['showFirstLast'] = $this->conf['list.']['showFirstLast'];
		$wrapArr = $this->conf['listView.'];

		// filter a single function to display and trigger alumniByFunctionTemplate
		if(isset($this->piVars['functionUid'])){
			$fAddWhere = ' AND uid IN ('.$this->piVars['functionUid'].')';
		}
		
		if(!empty($this->conf['listViewByFunction.']['alumniByFunctionTemplate'])){
			$this->template = $this->cObj->fileResource($this->conf['listViewByFunction.']['alumniByFunctionTemplate']);
		}
		
		// remove unwanted function uids
		if (isset($this->conf['listViewByFunction.']['excludeFunctionUids']) && (trim($this->conf['listViewByFunction.']['excludeFunctionUids']) != '')) {
			$fAddWhere = ' AND uid NOT IN ('.$this->conf['listViewByFunction.']['excludeFunctionUids'].')';
		}
		
		$funcs = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*',$table,'pid IN ('.$this->conf['feUsers']['pidList'].')'.$fAddWhere.$this->cObj->enableFields($table),'','sorting');
		
		// Adds the search box:
		$mArr['###search###'] = $this->pi_list_searchBox();
		$tmpl = $this->cObj->getSubpart($this->template,'###listViewByFunction###');
		$jumpBoxT = $this->cObj->getSubpart($tmpl,'###jumpToFunctionBox###');
		$linkBoxT = $this->cObj->getSubpart($tmpl,'###linkToFunctionBox###');
		$functionJumpT = $this->cObj->getSubpart($jumpBoxT,'###jumpToFunction###');
		$functionLinkT = $this->cObj->getSubpart($linkBoxT,'###linkToFunction###');
		$this->listT = $this->cObj->getSubpart($tmpl,'###list###');
		$funcT = $this->cObj->getSubpart($tmpl,'###function###');

		$this->conf['pidList'] = $this->conf['feUsers']['pidList'];
		
		$alphabeticLinkPageUid = $this->getTSFFVar('alphabeticPageUid');
		$mArr['###alphabeticLink###'] = '';
		if (intval($alphabeticLinkPageUid)>0) {
			$mArr['###alphabeticLink###'] = $this->pi_linkToPage(htmlentities($this->pi_getLL('alphabeticLink')),$alphabeticLinkPageUid);
		}
		
		$subArr['###function###'] = '';
			// count results to display "no results found" if necessary
		$numRes = 0;
		
		$addWhere .= $this->getDepartmentWhere();

		if ($this->getTSFFvar('hideAlumni') == 1) {
			$addWhere .= ' AND '.$this->table.'.alumni=0';
		}
		
		$funcUid = intval($this->piVars['functionUid']);
		
		if(isset($this->conf['showOnlyWithEvents'])){
			$subWhere = ' AND uid IN (' . $this->conf['feUserUids'].')';
		}
			
		while($f = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($funcs)) {
			
			$addFuncWhere = ' AND FIND_IN_SET('.$f['uid'].',function)';

			// Get number of records:
			$this->conf['pidList'] = $this->conf['feUsers']['pidList'];
			$res = $this->pi_exec_query($this->table,1,$addWhere.$subWhere.$addFuncWhere);

			list($this->internal['res_count']) = $GLOBALS['TYPO3_DB']->sql_fetch_row($res);
			
			$numRes += $this->internal['res_count'];
			if ($this->internal['res_count'] > 0) {
				
				$f = $this->handleTranslations($f,$table);
								
				if(count($f)>0){
					$mark['###categoryAnchor###'] = 'function_'.$f['uid'];
					$mark['###functionTitle###'] = $f['title'];
					
					// Make listing query, pass query to MySQL:
					if ($this->conf['listViewByFunction.']['hideListByDefault']==1) {
						
					 	if ((($funcUid > 0) && ($funcUid == $f['uid'])) || ($this->piVars['sword'] != '')) {
					 		$res = $this->pi_exec_query($this->table,0,$addWhere.$subWhere.$addFuncWhere);
					 		$sub['###list###'] = $this->pi_list_makelist($res);
					 	} else {
					 		$sub['###list###'] = '';
					 	}
						
						// only show list of persons containing one funtion (defined in piVars)
						if ($this->conf['listViewByFunction.']['hideListByDefault'] && isset($this->piVars['functionUid'])) {
							$addFuncWhere .= ' AND function='.intval($this->piVars['functionUid']);
						}
					} else {
						$addWhere .= ' AND sys_language_uid = 0';
						$res = $this->pi_exec_query($this->table,0,$addWhere.$subWhere.$addFuncWhere);

							// Adds the whole list table
						$sub['###list###'] = $this->pi_list_makelist($res);
					}
					$m['###functionTitle###'] = $f['title'];

					if ($this->conf['listViewByFunction.']['hideListByDefault']==0) {
						// Adds the result browser:
						$subArr['###function###'] .= $this->cObj->substituteMarkerArrayCached($funcT,$mark,$sub);
						$m['###functionAnchor###'] = $_SERVER['REQUEST_URI'].'#function_'.$f['uid'];
						$t['###subCategories###'] .= $this->cObj->substituteMarkerArray($functionJumpT,$m);
					} else {
						if ((($funcUid > 0) && ($funcUid == $f['uid'])) || ($this->piVars['sword'])) {
							$subArr['###function###'] .= $this->cObj->substituteMarkerArrayCached($funcT,$mark,$sub);
						}
						$m['###functionLink###'] = $this->pi_linkTP_keepPIvars_url(array('functionUid'=>$f['uid']));
						$t['###subCategories###'] .= $this->cObj->substituteMarkerArray($functionLinkT,$m);
						
					}
				}
			}
		}
		
		$subArr['###jumpToFunctionBox###'] = '';
		$subArr['###linkToFunctionBox###'] = '';
		if ($numRes > 0) {
			$mArr['###noResultFound###'] = '';
			if ($this->conf['listViewByFunction.']['hideListByDefault']) {
				$subArr['###linkToFunctionBox###'] = $this->cObj->substituteSubpart($linkBoxT,'###linkToFunction###',$t['###subCategories###']);
			} else {
				$subArr['###jumpToFunctionBox###'] = $this->cObj->substituteSubpart($jumpBoxT,'###jumpToFunction###',$t['###subCategories###']);
			}
		} else {
			$mArr['###noResultFound###'] = $this->noResultFound();
		}
		
		return $this->cObj->substituteMarkerArrayCached($tmpl,$mArr,$subArr);
	}

	/**
	 * List view, listing the records from the table.
	 * Does also provide the single view if the "showUid" piVar is set.
	 *
	 * @param	string		HTML input content - not used, ignore.
	 * @param	array		TypoScript configuration array
	 * @return	string		HTML content for the listing.
	 */
	function listAlumni()	{
		$this->lConf = $lConf = $this->conf['listView.'];	// Local settings for the listView function
		$this->pi_alwaysPrev = $this->lConf['alwaysPrev'];
		$this->viewMode = 'listView';

		if (!isset($this->piVars['pointer']))	$this->piVars['pointer']=0;
		if (!isset($this->piVars['sort']))	$this->piVars['sort']='lastname';

			// Initializing the query parameters:
		list($this->internal['orderBy'],$this->internal['descFlag']) = explode(':',$this->piVars['sort']);
		$this->internal['results_at_a_time']=t3lib_div::intInRange($lConf['results_at_a_time'],0,1000,3);		// Number of results to show in a listing.
		$this->internal['maxPages']=t3lib_div::intInRange($lConf['maxPages'],0,1000,2);;		// The maximum number of "pages" in the browse-box: "Page 1", 'Page 2', etc.
		$this->internal['results_at_a_time'] = 1000;

			// generate additional where statement
		if(intval($this->conf['alumniFuncUid']) > 0){
			$addWhere .= ' AND FIND_IN_SET('.$this->conf['alumniFuncUid'].',function)';
		} else {
			$addWhere .= ' AND alumni = 1';
		}

				// Get number of records:
		$this->conf['pidList'] = $this->conf['feUsers']['pidList'];
		$query = $this->pi_list_query($this->table,1,$addWhere);
		$res = $GLOBALS['TYPO3_DB']->sql_query($query);
		list($this->internal['res_count']) = $GLOBALS['TYPO3_DB']->sql_fetch_row($res);

		if ($this->internal['res_count'] > 0) {
				// Make listing query, pass query to MySQL:
			$query = $this->pi_list_query($this->table,0,$addWhere);
			$res = $GLOBALS['TYPO3_DB']->sql_query($query);

				// Adds the whole list table
			$subArr['###list###'] = $this->pi_list_makelist($res);
			$mArr['###noResultFound###'] = '';
		} else {
			$subArr['###list###'] = '';
			$mArr['###noResultFound###'] = $this->noResultFound();
		}
			// Adds the search box:
		$mArr['###search###'] = $this->pi_list_searchBox();



			// Adds the result browser:
		$wrapArr = $this->conf['listView.'];
		$mArr['###pageBrowser###'] = $this->alphabeticPageBrowser($this->getTSFFVar('alphabeticPageBrowserStepSize'));
		$tmpl = $this->cObj->getSubpart($this->template,'###listView###');
		return $this->cObj->substituteMarkerArrayCached($tmpl,$mArr,$subArr);
	}

	/**
	 * Returns the no-Result-found-Text
	 *
	 * @return string
	 */
	function noResultFound() {
		return $this->cObj->substituteMarker($this->cObj->getSubpart($this->template,'###noResultFoundBox###'),'###noResultFoundText###',$this->pi_getLL('noResultFound'));
	}

	/**
	 * Returns the name of the author formatted in the appropriate way
	 *
	 * @param	int			Uid of author
	 * @return	string		Content, ready for HTML output.
	 */
	function getAuthor($authorUid)	{
		if (intval($authorUid)>0) {
			return tslib_pibase::pi_getRecord($this->table,intval($authorUid));
		}
	}

	/**
	 * Field content, processed, prepared for HTML output.
	 *
	 * @param	string		Fieldname
	 * @return	string		Content, ready for HTML output.
	 */
	function getFieldContent($fN)	{
		global $TCA;

		if($this->conf[$this->viewMode.'.']['customProcessing.'][$fN]) {
				// keep old data array for later restorage
			$temp = $this->cObj->data;
				// load currentRow as cObj->data
			$this->cObj->data = $this->internal['currentRow'];
				// "execute" the TypoScript, i.e. do the custom processing for the field
			$content = $this->cObj->cObjGetSingle($this->conf[$this->viewMode.'.']['customProcessing.'][$fN],$this->conf[$this->viewMode.'.']['customProcessing.'][$fN.'.']);
				// restore old data array
			$this->cObj->data = $temp;
			return $content;
		}
		switch($fN) {
			case 'image':
				$image='';
				if ($this->internal['currentRow']['image'])	{
					$imgArr = t3lib_div::trimExplode(',',$this->internal['currentRow']['image'],1);
					$GLOBALS['TSFE']->make_seed();
					$randval = intval(rand(0,count($imgArr)-1));
					$imgFile = 'uploads/pics/'.$imgArr[$randval];
					$imgInfo = getimagesize(PATH_site.$imgFile);
					if (is_array($imgInfo))	{
						$image='<img src="'.$imgFile.'" '.$imgInfo[3].' alt="'.$this->internal['currentRow']['lastname'].'" />';
					}
				}
				return $image;
			break;
			case 'function':
				// shows the functions
				if ($this->internal['currentRow'][$fN] != '') {
					$f = $GLOBALS['TYPO3_DB']->exec_SELECTquery('title',$this->functionTable,'uid IN ('.$this->internal['currentRow'][$fN].')'.$this->cObj->enableFields($this->functionTable));
					$funcs = array();
					while($fu = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($f)) {
                                                $fu = $GLOBALS['TSFE']->sys_page->getRecordOverlay($this->functionTable,$fu,$GLOBALS['TSFE']->sys_language_content,'strict');
						$funcs[] = htmlspecialchars($fu['title']);          
					}
					
					return implode('</li><li>',$funcs);
				} else {
					return '';
				}
			break;
			case 'institutes':
			case 'buildings':
			case 'departments':
				$foreignTable = $TCA[$this->table]['columns'][$fN]['config']['foreign_table'];
				$foreignMMTable = $TCA[$this->table]['columns'][$fN]['config']['MM'];
				if ($this->internal['currentRow'][$fN] != '') {
					$subQ = $GLOBALS['TYPO3_DB']->SELECTquery('uid_foreign',$foreignMMTable,'uid_local = '.intval($this->internal['currentRow']['uid']));
					$f = $GLOBALS['TYPO3_DB']->exec_SELECTquery('title',$foreignTable,'uid IN ('.$subQ.')'.$this->cObj->enableFields($foreignTable));
					$funcs = array();
					while($fu = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($f)) {
						$funcs[] = htmlspecialchars($fu['title']);
					}
					return implode(', ',$funcs);
				} else {
					return '';
				}
			break;
			case 'news':
			case 'research':
			case 'membership':
			case 'profile':
			case 'addInfo':
				return $this->pi_RTEcssText($this->internal['currentRow'][$fN]);
			break;
			case 'lastname':
				if (($this->piVars['showUid'] != $this->internal['currentRow']['uid']) || ($this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'modeSelection') < 2)) {
					$params['showUid']= $this->internal['currentRow']['uid'];
					$params['pointer'] = '';
					$params['sword'] = '';
					$params['sort'] = '';
					$params['start'] = '';
					$params['end'] = '';
					return $this->pi_linkTP_keepPIvars(parent::getFieldContent($fN),$params,1,0,$this->conf['listView.']['detailPageUid']);
				} else {
					return parent::getFieldContent($fN);
				}
			break;
			case 'www':
			case 'email':
				return $this->cObj->gettypolink(htmlspecialchars($this->internal['currentRow'][$fN]),$this->internal['currentRow'][$fN]);
			break;
			case 'address':
			case 'office_address':
				return nl2br(htmlspecialchars($this->internal['currentRow'][$fN]));
			break;
			default:
				return parent::getFieldContent($fN);
			break;
		}
	}

	/**
	 * Returns a list row. Get data from $this->internal['currentRow'];
	 *
	 * @param integer $c	Row number
	 * @return string	html, one record as a row
	 */
	function pi_list_row($c) {
		$cells = '';
		if(empty($lang)){
			// get language parameter
			$lang = $GLOBALS['TSFE']->sys_page->sys_language_uid;
		}

		if($lang > 0){
	    	$olMode = 'strict';
    		$this->internal['currentRow'] = $GLOBALS['TSFE']->sys_page->getRecordOverlay($this->table,$this->internal['currentRow'],$lang,$olMode);
		}
		
		foreach($this->manualFieldOrder_list as $fieldName)	{
			if ($fieldName == 'office_phone') {
				$mArr['###cellClass###'] = 'class="no-wrap"';
			} else if($fieldName == 'lastname'){
				$mArr['###cellClass###'] = 'class="lastname"';
			}  else if($fieldName == 'function_suffix'){
				$mArr['###cellClass###'] = 'class="function-suffix"';
			}  else if($fieldName == 'title'){
				$mArr['###cellClass###'] = 'class="no-wrap"';
			} else {
				$mArr['###cellClass###'] = '';
			}
			// add either link of field-value-only
			if (is_array($this->conf['listView.']['detailLinkFields']) && in_array($fieldName,$this->conf['listView.']['detailLinkFields'])) {
				$mArr['###content###'] = $this->pi_list_linkSingle($this->internal['currentRow'][$fieldName],$this->internal['currentRow']['uid'],false,array(),false,$this->conf['listView.']['detailPageUid']);
			} else {
				$mArr['###content###'] = $this->getFieldContent($fieldName);
			}
			$cells .= $this->cObj->substituteMarkerArray($this->cellT[$c%2],$mArr);
		}

		return $this->cObj->substituteSubpart($this->rowT[$c%2],'###cell###',$cells);
	}

	/**
	 * Creates links which work like a "page selector", but using letters
	 *
	 * @param integer $step	Number of chars for each step, e.g. 3 => A-C
	 *
	 * @return string HTML formated string
	 */
	function alphabeticPageBrowser($step=4) {
		$t = $this->cObj->getSubpart($this->template,'###alphabeticPageBrowser###');

		if ($step == '1') {
			$elT = $this->cObj->getSubpart($t,'###aPBElementSingle###');
			$elActT = $this->cObj->getSubpart($t,'###aPBElementActiveSingle###');
				// remove active and single from main template
			$t = $this->cObj->substituteSubpart($t,'###aPBElementActiveSingle###','');
			$t = $this->cObj->substituteSubpart($t,'###aPBElement###','');
			$t = $this->cObj->substituteSubpart($t,'###aPBElementActive###','');
		} else {
			$elT = $this->cObj->getSubpart($t,'###aPBElement###');
			$elActT = $this->cObj->getSubpart($t,'###aPBElementActive###');
				// remove active and single from main template
			$t = $this->cObj->substituteSubpart($t,'###aPBElementActive###','');
			$t = $this->cObj->substituteSubpart($t,'###aPBElementSingle###','');
			$t = $this->cObj->substituteSubpart($t,'###aPBElementActiveSingle###','');
		}

		$out = '';
		for ($i=65;$i<90;$i=$i+$step) {
			$upper = $i+$step-1;
			if ($upper > (90-1*$step)) {
				$upper = 90;
			}
			$m['###start###'] = chr($i);
			$m['###end###'] = chr($upper);
			$m['###linkStart###'] = str_replace('&nbsp;</a>','',$this->pi_linkTP_keepPIvars('&nbsp;',array('start'=>$i,'end'=>$upper,'sword'=>'')));
			$m['###linkEnd###'] = '</a>';
			if ($this->piVars['start'] == $i) {
				$out .= $this->cObj->substituteMarkerArray($elActT,$m);
			} else {
				$out .= $this->cObj->substituteMarkerArray($elT,$m);
			}
			if ($upper == 90) {
				$i = 90;
			}
		}

		if ($step == '1') {
			return $this->cObj->substituteSubpart($t,'###aPBElementSingle###',$out);
		} else {
			return $this->cObj->substituteSubpart($t,'###aPBElement###',$out);
		}
	}

	/**
	 * Returns office information
	 *
	 * @param 	array	$data	Record of person
	 * @return 	string			HTML-Code of office information
	 */
	function getOffice(&$data) {
		$bakTemplate = $this->template;

		$this->template = $this->cObj->getSubpart($this->template,'###officeBox###');

		if ($this->template == '' || ($data[$this->conf['officeBoxTriggerField']])) {
			$this->template = $bakTemplate;
			return '';
		}
		$officeFields = t3lib_div::trimExplode($this->conf['officeFields']);
		$this->internal['currentRow'] = $data;

		foreach($officeFields as $f) {
			$sub['###'.$f.'Box###'] = $this->getBoxedFieldContent($f);
		}
		$out = $this->cObj->substituteMarkerArrayCached($this->template,array(),$sub);

		$this->template = $bakTemplate;
		return $out;
	}

	/**
	 * Returns the where-statement to include only selected departments
	 *
	 * @return string	SQL-WHERE String
	 */
	function getDepartmentWhere() {
		$addWhere = '';
		if ($this->getTSFFvar('departmentUids')!='') {
			$addWhere = ' AND uid IN ('.$GLOBALS['TYPO3_DB']->SELECTquery('uid_local',$this->departmentTableMM,'uid_foreign IN ('.$this->getTSFFvar('departmentUids').'))');
		}
		return $addWhere;
	}

	/**
	 * Redirects person if it doesn't exist anymore
	 *
	 * return void
	 */
	function personNotAvailableRedirect() {
		if (intval($this->conf['personNotAvailablePageUid'])) {
			header('HTTP/1.1 301 Moved Permanently');
			header("Location: http://".$_SERVER['HTTP_HOST'].'/'.$this->pi_getPageLink($this->conf['personNotAvailablePageUid']));
		} else {
			header('HTTP/1.1 301 Moved Permanently');
			header('Location: http://'.$_SERVER['HTTP_HOST']);
		}
		exit();
	}
	
	/**
	 * Gets the translated Records from DB, depending on the sys_language_uid.
	 * Options to set in the TS Config:
	 * 		****Shows the original Record, if no translation was found***
	 * 		bool showNonTranslatedRecords.###tableName### = 1
	 *
	 * Probably deprecated, use the typo3-language-overlay function
	 *
	 * @param array $dataArr
	 * @return array
	 */
	function handleTranslations($dataArr, $table){
	
		// added by manuel - 24.06.2010 - get correct value for language
		$sys_language_uid = $GLOBALS['TSFE']->sys_language_content;
		
		if($sys_language_uid > 0){
			$origUid = $dataArr['uid'];
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('*',$table,'sys_language_uid='.$sys_language_uid.' AND l18n_parent='.$origUid.$this->cObj->enableFields($table));
			if(count($res)>0){
				unset($dataArr);
				$dataArr = array();
				$dataArr = $res[0];
				$dataArr['uid'] = $origUid;
			}else{
				if(!$this->conf['showNonTranslatedRecords.'][$table]){
					unset($dataArr);
					$dataArr = array();	
				}
			}
		}
		return $dataArr;
	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/x4epersdb/pi1/class.tx_x4epersdb_pi1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/x4epersdb/pi1/class.tx_x4epersdb_pi1.php']);
}

?>