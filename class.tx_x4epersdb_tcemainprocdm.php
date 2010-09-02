<?
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
 * This hook generates a personal sysfolder inside the actual sysfolder
 * if users adds a fe_user.
 *
 * Additionally the "personal_page" field will be set automatically
 */

class tx_x4epersdb_tcemainprocdm {
	/**
	 * Name of the table containing the person records
	 * @var string
	 */
	var $personTable = 'tx_x4epersdb_person';

	/**
	 * Name of the table containing the fe_users
	 * @var string
	 */
	var $feUsersTable = 'fe_users';

	/**
	 * Name of the table containing the pages
	 * @var string
	 */
	var $pagesTable = 'pages';

	/**
	 * Uid of the default fe_group
	 * @var integer
	 */
	var $defaultUserGroup = 0;

	/**
	 * Array of language labels to set the correct page titles (english)
	 * @var array
	 */
	var $langEn = array (
		'cv' => "CV",
		'research' => "Research",
	);
	/**
	 * Array of language labels to set the correct page titles (german)
	 * @var array
	 */
	var $langDe = array (
		'cv' => "Lebenslauf",
		'research' => "Forschungsprojekte",
	);

	/**
	 * Hook to create various records such as fe_users, pages etc. when
	 * creating or updating a new user
	 *
	 * @param string $status
	 * @param string $table
	 * @param string $id
	 * @param array $fieldArray
	 * @param object $parent
	 *
	 * @return void
	 */
    function processDatamap_postProcessFieldArray ($status, $table, $id, &$fieldArray, &$parent) {

		if (($table == $this->personTable) || ($table == 'fe_users')) {
			$pid = $this->getPopViewId($fieldArray);

			// check if hooks are enabled
			$pageTSconf = t3lib_BEfunc::getPagesTSconfig($pid);
			$pageTSconf = $pageTSconf['plugin.']['x4epersdb.'];
			if ($pageTSconf['enableHooks'] == 1) {
				// only act if users are created in specific sysfolder:
				if ($table == $this->personTable) {
					switch($status) {
						case 'new':
							$this->createFEUser($fieldArray);
							$this->checkAlias($fieldArray,$id,$status);
							if ($pageTSconf['enableCreatePages'] == 1) {
								$this->createPages($fieldArray);
							}
							//$this->createMountoints($fieldArray);
							$this->createCalendar($fieldArray);
							$this->syncUserGroup($fieldArray,'new',$id);
						break;
						case 'update':
							$tmp = get_object_vars($this);
							$this->checkAlias($fieldArray,$id,$status);
							if (isset($tmp['datamap'][$this->personTable][$id]['personal_page'])) {
								$personalFolder = $tmp['datamap'][$this->personTable][$id]['personal_page'];
							} else {
								$t = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('personal_page',$this->personTable,'uid ='.$id);
								$personalFolder = $t[0]['personal_page'];
								unset($t);
							}
							$this->updatePersonalFolder($fieldArray,$id,$table);
							$this->updatePassword($fieldArray,$id);
							$this->syncUsernameAndEmail($fieldArray,$id,$table);
							$be_users = $tmp['datamap'][$this->personTable][$id]['beuser'];
							//$this->removeMountpoints($id,$personalFolder);
							//$this->createMountoints($fieldArray,$id,$be_users,$personalFolder);
							$this->syncUserGroup($fieldArray,'update',$id);
						break;
						default:
						break;
					}
				}
			
				if($table == 'fe_users'){
					switch($status) {
						case 'update':
							$this->updatePersonalFolder($fieldArray,$id,$table);
							$this->syncUsernameAndEmail($fieldArray,$id,$table);
							$this->syncUserGroup($fieldArray,'update',$id,$table);
						break;
						default:
						break;
					}
				}
			}
		}
    }

	/**
	 * Makes sure the e-mail of the person is equal to the username and e-mail
	 * of the fe_user
	 *
	 * @param array $fieldArray
	 * @param string $id
	 * @param string $table
	 *
	 * @return void
	 */
    function syncUsernameAndEmail($fieldArray,$id,$table){
    	switch ($table){
    	case $this->personTable://UPDATE the fe_user's username
    		if(isset($fieldArray['email'])){
    			$upd['username'] = $fieldArray['email'];
    			$res = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('feuser_id',$this->personTable,'uid='.$id);
    			$where = 'uid='.$res[0]['feuser_id'];
    			$tbl = $this->feUsersTable;
    			$exec = true;
    		}else{
    			$exec = false;
    		}
    	break;
    	case $this->feUsersTable;//UPDATE the person's email
    		if(isset($fieldArray['username'])){ 
    			$upd['email'] = $fieldArray['username'];
	    		$where = 'feuser_id='.$id;
    			$tbl = $this->personTable;
    			$exec = true;
    		}else{
    			$exec = false;
    		}
    	break;
    	default:
    	break;
    	}
    	
    	//If the fields have to be synchronsized, the query is executed
    	if($exec){
			$GLOBALS['TYPO3_DB']->exec_UPDATEquery($tbl,$where,$upd); 
    	}
    }

    /**
     * Creates and assigns a calender to a user, only necessary if cal is used
	 *
	 * @param array $fieldArray
	 * @return void
     */
    function createCalendar(&$fieldArray) {
    	global $TYPO3_CONF_VARS;

    	$pid = $this->getPopViewId($fieldArray);

    	if (isset($TYPO3_CONF_VARS['EXTCONF']['x4epersdb']['calendarPid'][$pid])) {
    		$data['tx_cal_calendar']['NEW001'] = array (
    			'pid' => $TYPO3_CONF_VARS['EXTCONF']['x4epersdb']['calendarPid'][$pid],
    			'title' => $fieldArray['email'],
    			'owner' => $fieldArray['feuser_id'],
    		);

    		$tce = t3lib_div::makeInstance('t3lib_TCEmain');
			$tce->stripslashes_values = 0;
			$tce->start($data,array());
			$tce->process_datamap();
    	}

    }

    /**
     * Updates the passwort of fe user, if there are any changes.
     *
	 * @param array $fieldArray
	 * @param string $id
	 * @return void
     */
    function updatePassword(&$fieldArray,$id) {
    	if (isset($fieldArray['password'])) {
    		$user = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('feuser_id',$this->personTable,'uid = '.$id);
    		if (isset($user[0]['feuser_id'])) {
    			$upd['password'] = $fieldArray['password'];
    			$GLOBALS['TYPO3_DB']->exec_UPDATEquery('fe_users','uid = '.$user[0]['feuser_id'],$upd);
    		}
    	}
    }

	/**
     * Updates the title of the personal folder, if there are any changes.
     *
	 * @param array $fieldArray
	 * @param string $id
	 * @param string $table
	 * return void
     */
    function updatePersonalFolder(&$fieldArray,$id,$table) {
    	switch($table){
    	case $this->personTable:
	    	if (isset($fieldArray['email'])) {
				$user = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('personal_page',$this->personTable,'uid = '.$id);
	    		if (isset($user[0]['personal_page'])) {
	    			$upd['title'] = $fieldArray['email'];
	    			$GLOBALS['TYPO3_DB']->exec_UPDATEquery($this->pagesTable,'uid = '.$user[0]['personal_page'],$upd);
	    		}
	    	}
	    break;
    	case $this->feUsersTable:
    	if (isset($fieldArray['username'])) {
				$user = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('personal_page',$this->personTable,'feuser_id = '.$id);
	    		if (isset($user[0]['personal_page'])) {
	    			$upd['title'] = $fieldArray['username'];
	    			$GLOBALS['TYPO3_DB']->exec_UPDATEquery($this->pagesTable,'uid = '.$user[0]['personal_page'],$upd);
	    		}
	    	}
    	break;
    	default:
    	break;
    	}
    }

    /**
     * Creates the fe user
	 *
	 * @param array $fieldArray
	 * @return void
     */
    function createFeUser(&$fieldArray) {
    	global $TYPO3_CONF_VARS;

			$pid = $this->getPopViewId($fieldArray);
			
			if($fieldArray['email'] != ''){
				$emailArr = explode('@', $fieldArray['email']);
				if (count($emailArr) > 1) {
					if(stripos($emailArr[1], "uhbs") !== false){
						$username = $emailArr[0];
					} else {
						$username = $fieldArray['email'];
					}
				} else {
					$username = $fieldArray['email'];
				}
			} else {
				$username = $this->generateUsername($fieldArray);
			}
			$email = 

			$data['fe_users']['NEW001'] = array (
				'username' => $username ,
				'pid' => $pid,
				'usergroup' => $this->getDefaultUserGroup()
			);
				// change the pid of the fe-users to the selected folder

			$pageTSconf = t3lib_BEfunc::getPagesTSconfig($pid);
			$pageTSconf = $pageTSconf['plugin.']['x4epersdb.'];
			if ($pageTSconf['defaultFeUserPid']) {
				$data['fe_users']['NEW001']['pid'] = intval($pageTSconf['defaultFeUserPid']);
			} else if (isset($TYPO3_CONF_VARS['EXTCONF']['x4epersdb']['feUserPid'][$pid])) {
				$data['fe_users']['NEW001']['pid'] = intval($TYPO3_CONF_VARS['EXTCONF']['x4epersdb']['feUserPid'][$pid]);
			}


			$tce = t3lib_div::makeInstance('t3lib_TCEmain');
			$tce->stripslashes_values = 0;
			$tce->start($data,array());
			$tce->process_datamap();

			$fe = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('uid,username,usergroup','fe_users','pid = '.intval($data['fe_users']['NEW001']['pid']).' AND username LIKE "'.$data['fe_users']['NEW001']['username'].'%"','','tstamp DESC',1);

			$fieldArray['username'] = $fe[0]['username'];
			$fieldArray['password'] = '';
			$fieldArray['feuser_id'] = $fe[0]['uid'];
    }

	/**
	 * Returns the current page uid (the sysfolder in which the record was
	 * created or edited
	 *
	 * @param array $fieldArray
	 * @return integer
	 */
	function getPopViewId($fieldArray){
		$pid = (isset($_POST['popViewId'])) ? intval($_POST['popViewId']) : 0 ;

		if(!$pid && isset($fieldArray['pid'])){
			$pid = intval($fieldArray['pid']);
		}

		return $pid;
	}

	/**
	 * Handels the usergroup selection, so that the corresponding fe_user
	 * will be member of the fe_groups given
	 *
	 * @param array $fieldArray
	 * @param string $type
	 * @param string $id
	 * @param string $table
	 */
	function syncUserGroup(&$fieldArray,$type,$id,$table=''){
		$id = intval($id);
		switch($type) {
			case 'new':

				if(!is_null($fieldArray['fe_groups']) && trim($fieldArray['fe_groups']) !== ""){
					$upd = array();
					$upd['usergroup'] = $fieldArray['fe_groups'];
					$GLOBALS['TYPO3_DB']->exec_UPDATEquery('fe_users','uid = '.intval($fieldArray['feuser_id']),$upd);
				}
			break;
			case 'update':
				switch($table){
					case 'fe_users':
						if(isset($fieldArray['usergroup']) && intval($fieldArray['usergroup'])!=0){
							$upd = array();
							$upd['fe_groups'] = $fieldArray['usergroup'];
							$GLOBALS['TYPO3_DB']->exec_UPDATEquery($this->personTable,'feuser_id = '.$id,$upd);
						}
					break;
					default:
						if(!is_null($fieldArray['fe_groups']) && trim($fieldArray['fe_groups']) !== ""){
							$upd = array();
							$upd['usergroup'] = $fieldArray['fe_groups'];
							//$GLOBALS['TYPO3_DB']->exec_UPDATEquery('fe_users','uid = '.$id,$upd);
							$query = $GLOBALS['TYPO3_DB']->SELECTquery('feuser_id',$this->personTable,'uid ='.$id);
							$GLOBALS['TYPO3_DB']->exec_UPDATEquery('fe_users','uid IN('.$query.')',$upd);
						}
					break;
				}
			break;
			default:
			break;
		}
	}

	/**
	 * Returns the default usergroup the created or edited person has,
	 * the correct usergroup needs to be set in the TYPO3_CONF_VARS
	 *
	 * @global array $TYPO3_CONF_VARS
	 * @return integer
	 */
   function getDefaultUserGroup() {
   		global $TYPO3_CONF_VARS;

   		$pid = $this->getPopViewId($fieldArray);

		$pageTSconf = t3lib_BEfunc::getPagesTSconfig($pid);
		$pageTSconf = $pageTSconf['plugin.']['x4epersdb.'];

		if ($pageTSconf['defaultUserGroup'] && !empty($pageTSconf['defaultUserGroup'])) {
			return $pageTSconf['defaultUserGroup'];
   		} else if (isset($TYPO3_CONF_VARS['EXTCONF']['x4epersdb']['usergroupMapping'][$pid])) {
   			return $TYPO3_CONF_VARS['EXTCONF']['x4epersdb']['usergroupMapping'][$pid];
   		} else {
   			return $this->defaultUserGroup;
   		}

   }

   /**
    * Generates a username, e.g. firstname.lastname
    *
    * @param array $fieldArray
    * @return string
    */
    function generateUsername(&$fieldArray) {
    	$username[0] = $this->safeUsername($fieldArray['firstname']);
    	$username[1] = $this->safeUsername($fieldArray['lastname']);
    	return strtolower(implode('.',$username));
    }

	/**
	 * Returns translated text
	 * @global array $TYPO3_CONF_VARS
	 * @param string $fieldArray
	 * @param string $key
	 * @return string
	 */
	function getTranslation(&$fieldArray, $key=''){
		global $TYPO3_CONF_VARS;

		$pid = $this->getPopViewId($fieldArray);

    	if (isset($TYPO3_CONF_VARS['EXTCONF']['x4epersdb']['defaultLanguage'][$pid])){
    		$lang = $TYPO3_CONF_VARS['EXTCONF']['x4epersdb']['defaultLanguage'][$pid];
    	}else {
    		$lang = "de";
    	}
		if($key != ''){
			switch($lang){
				case "en":
				return $this->langEn[$key];
				default:
				case "de":
				return $this->langDe[$key];
			}
		}

	}

	/**
	 * Creates the pages
	 *
	 * @param array $fieldArray
	 * @return void
	 */
	function createPages(&$fieldArray) {
        	$data['pages']['NEW001'] = array(
			    'title' => $fieldArray['username'],
			    'hidden' => 0,
			    'doktype' => 254,
    			'pid' => $fieldArray['pid']
			);
			$data['pages']['NEW002'] = array(
			    'title' => 'Standard-Seiten',
			    'hidden' => 0,
			    'doktype' => 254,
    			'pid' => 'NEW001'
			);
			$data['pages']['NEW012'] = array(
			    'title' => 'Eigene Seiten',
			    'hidden' => 0,
			    'doktype' => 254,
    			'pid' => '-NEW002'
			);
			$data['pages']['NEW003'] = array(
			    'hidden' => 0,
			    'doktype' => 1,
			    'perms_everybody' => 27,
				'title' => $this->getTranslation($fieldArray, 'cv'),
    			'pid' => 'NEW002'
			);
					$data['pages']['NEW005'] = array(
			    'hidden' => 1,
			    'doktype' => 1,
			    'perms_everybody' => 27,
				'title' => $this->getTranslation($fieldArray, 'research'),
    			'pid' => '-NEW003'
			);
			$data['pages']['NEW006'] = array(
			    'hidden' => 1,
			    'doktype' => 1,
			    'title' => 'Seite A',
    			'pid' => 'NEW012'
			);
			$data['pages']['NEW007'] = array(
			    'hidden' => 1,
			    'doktype' => 1,
			    'title' => 'Seite B',
    			'pid' => '-NEW006'
			);
			$data['pages']['NEW008'] = array(
			    'hidden' => 1,
			    'doktype' => 1,
			    'title' => 'Seite C',
    			'pid' => '-NEW007'
			);

				// set permission for all pages
			foreach($data['pages'] as $key => $value) {
				$data['pages'][$key]['perms_user'] = 31;
				$data['pages'][$key]['perms_group'] = 31;
				$data['pages'][$key]['perms_everybody'] = 31;
			}

        	$tce = t3lib_div::makeInstance('t3lib_TCEmain');
   			$tce->stripslashes_values = 0;
			$tce->start($data,array());
			$tce->process_datamap();

				// add new page
			$fieldArray['personal_page'] = $tce->substNEWwithIDs['NEW001'];
			$fieldArray['resume_page'] = $tce->substNEWwithIDs['NEW003'];
			$fieldArray['course_page'] = $tce->substNEWwithIDs['NEW004'];
			$fieldArray['research_page'] = $tce->substNEWwithIDs['NEW005'];

			$fieldArray['beuser'] .=','.$fieldArray['cruser_id'];

	}


	/**
	 * Removes the uncessary mountpoints for the responsible backend user
	 *
	 * @param string $id
	 * @param integer $folderUid
	 * @return void
	 */
    function removeMountpoints(&$id,$folderUid) {
    	$folderUid = str_replace('pages_','',$folderUid);
       		// select users which have the user's home-sysfolder as mountpoint
    	$be_users = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid,db_mountpoints','be_users','FIND_IN_SET('.str_replace('pages_','',$folderUid).',`be_users`.`db_mountpoints`)');
    	while($u = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($be_users)) {
    			// now add all mountpoints except the $folderUid and update the record
    		$mps = t3lib_div::trimExplode(',',$u['db_mountpoints']);
    		$newMp = array();
    		foreach($mps as $v) {
    			$v = str_replace('pages_','',$v);
    			if (($v != '') && ($v != $folderUid)) {
    				$newMp[] = $v;
    			}

    		}
    		$GLOBALS['TYPO3_DB']->exec_UPDATEquery('be_users','uid='.$u['uid'],array('db_mountpoints'=>implode(',',$newMp)));
    	}
    }

    /**
	 * Creates the necessary mountpoints for the responsible backend user
	 * @param array $fieldArray
	 * @param string $id
	 * @param string $beUsersOld
	 * @param integer $personalFolder
	 * @return void
	 */
    function createMountoints(&$fieldArray,$id=0,$beUsersOld='',$personalFolder='') {
    		// create mountpoints
    	$beUsers = t3lib_div::trimExplode(',',$fieldArray['beuser']);
    	if ($fieldArray['personal_page'] == '') {
    		$personalFolder = str_replace('pages_','',$personalFolder);
    	} else {
    		$personalFolder = $fieldArray['personal_page'];
    	}

    	if ($fieldArray['beuser'] == '') {
    		$beUsers = explode(',',str_replace('be_users_',' ',$beUsersOld));
    	} else {
    		$beUsers = t3lib_div::trimExplode(',',$fieldArray['beuser'],1);
    	}
		$query = 'UPDATE be_users SET db_mountpoints = CONCAT(db_mountpoints,",","'.$personalFolder.'") WHERE uid IN ('.implode(',',$beUsers).')';
		$upd['db_mountpoints'] = 'CONCAT(db_mountpoints,",","'.$personalFolder.'")';
		foreach($beUsers as $v) {
			$query = 'UPDATE be_users SET db_mountpoints = CONCAT(db_mountpoints,",","'.$personalFolder.'") WHERE uid = '.$v;
			$GLOBALS['TYPO3_DB']->sql_query($query);
		}
    }

    /**
	 * Removes specialchars, whitespace etc.
	 *
	 * @param string $text
	 * @return string
	 */
	function safeUsername($text) {
		$text = str_replace('ä','ae',$text);
		$text = str_replace('ö','oe',$text);
		$text = str_replace('ü','ue',$text);		
		$text = str_replace('à','ae',$text);
		$text = str_replace('Ä','Ae',$text);
		$text = str_replace('Ö','Oe',$text);
		$text = str_replace('Ü','Ue',$text);
		$text = str_replace(' ','',$text);
		return preg_replace("/[^a-zA-Z0-9\-_\.]+/", "_", $text);
	}

	/**
	 * Removes special chars in alias field
	 *
	 * @param array $fieldArray		Fieldarray (pointer-wise)
	 * @return void
	 */
	function removeSpecialChars(&$fieldArray) {
		$aliasField = 'alias';
		$fieldArray[$aliasField] = str_replace(' ','-',$fieldArray[$aliasField]);
			// Fetch character set:
		$charset = 'iso-8859-1';
		require_once(PATH_t3lib.'class.t3lib_cs.php');
		$cs = t3lib_div::makeInstance('t3lib_cs');
		$processedTitle = $cs->conv_case($charset,$title,'toLower');

			// Convert some special tokens to the space character:
		$space = $cs->conf['spaceCharacter'] ? $cs->conf['spaceCharacter'] : '-';
		$processedTitle = strtr($fieldArray[$aliasField],' -+_',$space.$space.$space.$space); // convert spaces

			// Remove specialchars
		$processedTitle = $this->safeUsername($processedTitle);
		
			// Convert extended letters to ascii equivalents:
		$fieldArray[$aliasField] = $cs->specCharsToASCII($charset,$processedTitle);
	}

	/**
	 * Generates a unique alias, trying the lastname, lastname-firstname,
	 * lastname-firstname-x etc.
	 *
	 * @param array $fieldArray
	 * @param string $id
	 * @param string $status
	 * @return	string		Alias
	 */
	function getUniqueAlias(&$fieldArray,$id,$status) {


		$pid = $this->getPopViewId($fieldArray);

		if (trim($fieldArray['alias'])=='') {
			$fieldArray['alias'] = $fieldArray['lastname'];
		}
		$this->removeSpecialChars($fieldArray);
		$alias = $fieldArray['alias'];

		if (intval($id)>0) {
			$person = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('lastname,firstname',$this->personTable,'uid ='.$id);
			$person = $person[0];
		} else {
			$person = $fieldArray;
		}

		$addWhere = ' AND deleted=0 AND pid = '.$pid;
		if ($status != 'new') {
			$addWhere .= ' AND uid != '.$id;
		}

		$record = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('uid',$this->personTable,'alias LIKE "'.$alias.'"'.$addWhere);
		if (isset($record[0]['uid'])) {
			$alias = $alias.'-'.$person['firstname'];
			$record = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('uid',$this->personTable,'alias LIKE "'.$alias.'"'.$addWhere);
			$orgAlias = $alias;
			$i=1;
			while(isset($record[0]['uid'])) {
				$alias = $orgAlias.'-'.$i;
				$record = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('uid',$this->personTable,'alias LIKE "'.$alias.'"'.$addWhere);
				$i++;
			}
		}
		return $alias;
	}

	/**
	 * Checks for empty alias and resets it to lastname
	 *
	 * @param array $fieldArray
	 * @param string $id
	 * @return void
	 */
	function checkAlias(&$fieldArray,$id,$status) {
		global $TCA;
		$pid = $this->getPopViewId($fieldArray);

		if (isset($TCA[$this->personTable]['columns']['alias'])) {
			if (isset($fieldArray['alias'])) {
				if (($status == 'update') && (trim($fieldArray['alias'])=='')) {
					$t = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('lastname',$this->personTable,'uid ='.$id.' AND deleted = 0 AND pid = '.$pid);
					$fieldArray['alias'] = $t[0]['lastname'];
					$fieldArray['alias'] = $this->getUniqueAlias($fieldArray,$id,$status);
				} else {
					$fieldArray['alias'] = $this->getUniqueAlias($fieldArray,$id,$status);
				}
			} elseif ($status == 'new') {
				$fieldArray['alias'] = $this->getUniqueAlias($fieldArray,$id,$status);
			}
		}
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/x4epersdb/class.tx_x4epersdb_tcemainprocdm.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/x4epersdb/class.tx_x4epersdb_tcemainprocdm.php']);
}
?>