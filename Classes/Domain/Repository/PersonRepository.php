<?php

/***************************************************************
*  Copyright notice
*
*  (c) 2011 Michel Georgy <michel@4eyes.ch>, 4eyes GmbH
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
 * A repository for contacts
 */
class Tx_X4epersdb_Domain_Repository_PersonRepository extends Tx_Extbase_Persistence_Repository {

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
     * Overwritten add method to create some additional data
     * @param object $person
     * @return void
     */
    public function add($person){
        parent::add($person);

        $this->createFEUser($person);
        $this->checkAlias($person);
        $this->createPages($person);
        //$this->createMountpoints($fieldArray);
        $this->createCalendar($person);
        $this->syncUserGroup($person,'new');
    }

    /**
     * Update related data
     * @param object $object
     * @return void
     */
    public function update($person){
        parent::update($person);

        $this->checkAlias($person);
        $this->updatePersonalFolder($person);
        //$this->updatePassword($person);
        $this->syncUsernameAndEmail($person);
        $be_users = $tmp['datamap'][$this->personTable][$id]['beuser'];
        //$this->removeMountpoints($id,$personalFolder);
        //$this->createMountoints($fieldArray,$id,$be_users,$personalFolder);
        $this->syncUserGroup($person,'update');
    }



    /**
	 * Makes sure the e-mail of the person is equal to the username and e-mail
	 * of the fe_user
	 *
	 * @param object $person
	 * @param string $id
	 * @param string $table
	 *
	 * @return void
	 */
    function syncUsernameAndEmail($person){
        if($person->getEmail() != ''){
            $upd['username'] = $person->getEmail();
        } else {
            $upd['username'] = $this->generateUsername($person);
        }
        $where = 'uid='.$person->getFeuserId();
        $tbl = $this->feUsersTable;
        $GLOBALS['TYPO3_DB']->exec_UPDATEquery($tbl,$where,$upd);
    }

    /**
     * Creates and assigns a calender to a user, only necessary if cal is used
	 *
	 * @param array $fieldArray
	 * @return void
     */
    function createCalendar(&$person) {
    	global $TYPO3_CONF_VARS;

    	$pid = $this->getPopViewId($person);

    	if (isset($TYPO3_CONF_VARS['EXTCONF']['x4epersdb']['calendarPid'][$pid])) {
    		$data['tx_cal_calendar']['NEW001'] = array (
    			'pid' => $TYPO3_CONF_VARS['EXTCONF']['x4epersdb']['calendarPid'][$pid],
    			'title' => $person->getEmail(),
    			'owner' => $person->getFeuserId(),
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
     * @todo check if username changed instead of change title everytime
     * @param array $person
     * return void
     */
    function updatePersonalFolder(&$person) {
        $personalPage = $person->getPersonalPage();
        if ($personalPage != 0 && $personalPage != '') {
            $upd['title'] = $person->getUsername();
            $GLOBALS['TYPO3_DB']->exec_UPDATEquery($this->pagesTable,'uid = '.$personalPage,$upd);
        }
    }

    /**
     * Creates the fe user
	 *
	 * @param object $person
	 * @return void
     */
    function createFeUser(&$person) {
    	global $TYPO3_CONF_VARS;

        $pid = $this->getPopViewId($person);

        if($person->getEmail() != ''){
                $emailArr = explode('@', $person->getEmail());
                if (count($emailArr) > 1) {
                        if(stripos($emailArr[1], "uhbs") !== false){
                                $username = $emailArr[0];
                        } else {
                                $username = $person->getEmail();
                        }
                } else {
                        $username = $person->getEmail();
                }
        } else {
                $username = $this->generateUsername($person);
        }

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

        $person->setUsername($fe[0]['username']);
        $person->setPassword('');
        $person->setFeuserId($fe[0]['uid']);
    }

	/**
	 * Returns the current page uid (the sysfolder in which the record was
	 * created or edited
	 *
	 * @param object $person
	 * @return integer
	 */
	function getPopViewId(&$person){
		$pid = (isset($_POST['popViewId'])) ? intval($_POST['popViewId']) : 0 ;

		if(!$pid && is_object($person)){
                    $pid = $person->getPid();
		}

		return $pid;
	}

	/**
	 * Handels the usergroup selection, so that the corresponding fe_user
	 * will be member of the fe_groups given
	 *
	 * @param object $person
	 * @param string $type
	 * @param string $id
	 * @param string $table
	 */
	function syncUserGroup(&$person,$type){
            $id = $person->getUid();
            switch($type) {
                case 'new':
                    if(trim($person->getFeGroups()) !== ""){
                        $upd = array();
                        $upd['usergroup'] = $person->getFeGroups();
                        $GLOBALS['TYPO3_DB']->exec_UPDATEquery('fe_users','uid = '.intval($person->getFeuserId()),$upd);
                    }
                break;
                case 'update':
                    if(trim($person->getFeGroups()) !== ""){
                        $upd = array();
                        $upd['usergroup'] = $person->getFeGroups();
                        //$GLOBALS['TYPO3_DB']->exec_UPDATEquery('fe_users','uid = '.$id,$upd);
                        $query = $GLOBALS['TYPO3_DB']->SELECTquery('feuser_id',$this->personTable,'uid ='.$id);
                        $GLOBALS['TYPO3_DB']->exec_UPDATEquery('fe_users','uid IN('.$query.')',$upd);
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
    * @param object $person
    * @return string
    */
    function generateUsername(&$person) {
    	$username[0] = $this->safeUsername($person->getFirstname());
    	$username[1] = $this->safeUsername($person->getLastname());
    	return strtolower(implode('.',$username));
    }

	/**
	 * Returns translated text
	 * @global array $TYPO3_CONF_VARS
	 * @param object $person
	 * @param string $key
	 * @return string
	 */
	function getTranslation(&$person, $key=''){
            global $TYPO3_CONF_VARS;

            // load sysFolder pageTS
            $pid = $this->getPopViewId($person);
            $pageTSconf = t3lib_BEfunc::getPagesTSconfig($pid);
            $pageTSconf = $pageTSconf['plugin.']['x4epersdb.'];

            // new language check by manuel [1: pageTS sysfolder / 2: T3CV-extconf  / 3: de] - begin
            if(isset($pageTSconf['defaultLanguage'])){
                    $lang = $pageTSconf['defaultLanguage'];
            } else if (isset($TYPO3_CONF_VARS['EXTCONF']['x4epersdb']['defaultLanguage'][$pid])){
                    $lang = $TYPO3_CONF_VARS['EXTCONF']['x4epersdb']['defaultLanguage'][$pid];
            } else {
                    $lang = "de";
            }
            // - end



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
	 * @param object $person
	 * @return void
	 */
	function createPages(&$person) {
            $data['pages']['NEW001'] = array(
                'title' => $person->getUsername(),
                'hidden' => 0,
                'doktype' => 254,
                'pid' => $this->getPopViewId($person)
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
                'title' => $this->getTranslation($person, 'cv'),
                'pid' => 'NEW002'
            );
            $data['pages']['NEW005'] = array(
                'hidden' => 1,
                'doktype' => 1,
                'perms_everybody' => 27,
                'title' => $this->getTranslation($person, 'research'),
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
            $person->setPersonalPage($tce->substNEWwithIDs['NEW001']);
            $person->setResumePage($tce->substNEWwithIDs['NEW003']);
            $person->setCoursePage($tce->substNEWwithIDs['NEW004']);
            $person->setResearchPage($tce->substNEWwithIDs['NEW005']);

            $beuser = $person->getBeuser() . ',' . $person->getCruserId();
            $person->setBeuser($beuser);
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
    function createMountpoints(&$fieldArray,$id=0,$beUsersOld='',$personalFolder='') {
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
	 * @param string $str		Fieldarray (pointer-wise)
	 * @return string
	 */
	function removeSpecialChars($str) {
		$str = str_replace(' ','-',$str);

                // Fetch character set:
		$charset = 'iso-8859-1';
		require_once(PATH_t3lib.'class.t3lib_cs.php');
		$cs = t3lib_div::makeInstance('t3lib_cs');
		$processedTitle = $cs->conv_case($charset,$title,'toLower');

		// Convert some special tokens to the space character:
		$space = $cs->conf['spaceCharacter'] ? $cs->conf['spaceCharacter'] : '-';
		$processedTitle = strtr($str,' -+_',$space.$space.$space.$space); // convert spaces

		// Remove specialchars
		$processedTitle = $this->safeUsername($processedTitle);

		// Convert extended letters to ascii equivalents:
		$str = $cs->specCharsToASCII($charset,$processedTitle);
                return $str;
	}

	/**
	 * Generates a unique alias, trying the lastname, lastname-firstname,
	 * lastname-firstname-x etc.
	 *
	 * @param object $person
	 * @return	string		Alias
	 */
	function getUniqueAlias(&$person) {
            $pid = $this->getPopViewId($person);
            $alias == $person->getAlias();
            if (trim($alias) == '') {
                $alias = $person->getLastname();
            }
            $alias = $this->removeSpecialChars($alias);
            //$alias = $fieldArray['alias'];

            $addWhere = ' AND deleted=0 AND pid = '.$pid;
            //if ($status != 'new') {
            //	$addWhere .= ' AND uid != '.$id;
            //}

            $record = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('uid',$this->personTable,'alias LIKE "'.$alias.'"'.$addWhere);
            if (isset($record[0]['uid'])) {
                $alias = $alias.'-'.$person->getFirstname();
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
	 * @param object $person
	 * @return void
	 */
	function checkAlias(&$person) {
            $person->setAlias($this->getUniqueAlias($person));
	}
}
?>