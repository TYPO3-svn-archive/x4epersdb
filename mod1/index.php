<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2010 4eyes GmbH  ()
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
 * Module 'Person Import' for the 'x4epersdb' extension.
 *
 * @author 4eyes    <>
 */

ini_set('auto_detect_line_endings',true);



// DEFAULT initialization of a module [BEGIN]
unset($MCONF);
require_once ("conf.php");
require_once ($BACK_PATH."init.php");
require_once ($BACK_PATH."template.php");
//require_once ($BACK_PATH."tce_file.php");

/**
 * load classes
 */
require_once (PATH_t3lib."class.t3lib_scbase.php");
require_once (PATH_t3lib."class.t3lib_tcemain.php");
require_once (PATH_t3lib."class.t3lib_basicfilefunc.php");
require_once (PATH_t3lib."class.t3lib_extfilefunc.php");

/**
 * load module classes
 */
require_once ("class.tx_x4epersdb_csvImport.php");

/**
 * load language
 */
$LANG->includeLLFile("EXT:x4epersdb/mod1/locallang.php");
#include ("locallang.php");



$BE_USER->modAccess($MCONF,1);    // This checks permissions and exits if the users has no permission for entry.
// DEFAULT initialization of a module [END]

/**
 * Main programm class
 *
 */
class tx_x4epersdb_module1 extends t3lib_SCbase {
	var $pageinfo;

	/**
	 *
	 */
	function init()    {
		global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;

		parent::init();

		/**
		 * add default TS Config
		 */
		$this->addPageDefaultTSConfig();

		/**
		 * update TS Config from current settings
		 */
		$this->modTSconfig=t3lib_BEfunc::getModTSconfig($this->id,'mod.'.$this->MCONF['name']);

		/*
		 if (t3lib_div::_GP("clear_all_cache"))    {
		 $this->include_once[]=PATH_t3lib."class.t3lib_tcemain.php";
		 }
		 */
	}

	/**
	 * Create default TS Config.
	 *
	 */
	function addPageDefaultTSConfig(){
		t3lib_extMgm::addPageTSConfig('
mod.web_txx4epersdbM1 {

	import.pid = 0
	import.table = tx_x4epersdb_person

	import.csv {
		skipFirstRow = 1
		delimiter = ,
		enclosure = "
		updatePid = 1
    updateOnlyEqualToCurrentImportPid = 1
		sourceFormat = iso-8859-1
	}
  import.upload {
		folder = import_upload
	  newFileName = currentPersonsImport.csv
	  fileExtensions.allow = txt,csv
	}

	import.reference.fieldForUpdate = 0
	import.reference.csvToTableFields {
		 0.fieldCsvName = P_Nr
		 0.fieldCsvIndex = 0
		 0.fieldTableName = external_id

		 1.fieldCsvName = Anrede
		 1.fieldCsvIndex = 1
		 1.fieldTableName =

		 2.fieldCsvName = Titel
		 2.fieldCsvIndex = 2
		 2.fieldTableName = title

		 3.fieldCsvName = Vorname
		 3.fieldCsvIndex = 3
		 3.fieldTableName = firstname

		 4.fieldCsvName  = Name
		 4.fieldCsvIndex  = 4
		 4.fieldTableName = lastname

		 5.fieldCsvName = Strasse
		 5.fieldCsvIndex = 5
		 5.fieldTableName = address

		 6.fieldCsvName = PLZ
		 6.fieldCsvIndex = 6
		 6.fieldTableName = zip

		 7.fieldCsvName = Ort
		 7.fieldCsvIndex = 7
		 7.fieldTableName = city

		 8.fieldCsvName = Land
		 8.fieldCsvIndex = 8
		 8.fieldTableName = country

		 9.fieldCsvName = Telefon privat
		 9.fieldCsvIndex = 9
		 9.fieldTableName = phone

		 10.fieldCsvName = Telefon Institut
		 10.fieldCsvIndex = 10
		 10.fieldTableName = office_phone

		 11.fieldCsvName = Email Institut
		 11.fieldCsvIndex = 11
		 11.fieldTableName = email

		 12.fieldCsvName = Zimmer Nummer
		 12.fieldCsvIndex = 12
		 12.fieldTableName = office_roomnumber

		 13.fieldCsvName = Funktions_Nr
		 13.fieldCsvIndex = 13
		 13.fieldTableName = function

		 14.fieldCsvName = Funktion Zusatz
		 14.fieldCsvIndex = 14
		 14.fieldTableName = function_suffix

		 15.fieldCsvName = Im_Internet
		 15.fieldCsvIndex = 15
		 15.fieldTableName =
		 }

}
		');
	}

	/**
	 * Adds items to the ->MOD_MENU array. Used for the function menu selector.
	 */
	function menuConfig()    {
		global $LANG;
		$this->MOD_MENU = Array (
		"function" => Array (
			 "import_csv" => $LANG->getLL("function1")
		)
		);
		parent::menuConfig();
	}

	// If you chose "web" as main module, you will need to consider the $this->id parameter which will contain the uid-number of the page clicked in the page tree
	/**
	* Main function of the module. Write the content to $this->content
	*/
	function main()    {
		global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;

		// Access check!
		// The page will show only if there is a valid page and if this page may be viewed by the user
		$this->pageinfo = t3lib_BEfunc::readPageAccess($this->id,$this->perms_clause);
		$access = is_array($this->pageinfo) ? 1 : 0;

		if (($this->id && $access) || ($BE_USER->user["admin"] && !$this->id))    {

			// Draw the header.
			$this->doc = t3lib_div::makeInstance("mediumDoc");
			$this->doc->backPath = $BACK_PATH;
			$this->doc->form='<form action="" method="post" enctype="multipart/form-data">';

			// JavaScript
			$this->doc->JScode = '
                <script language="javascript" type="text/javascript">
                    script_ended = 0;
                    function jumpToUrl(URL)    {
                        document.location = URL;
                    }
                </script>
            ';
			$this->doc->postCode='
                <script language="javascript" type="text/javascript">
                    script_ended = 1;
                    if (top.fsMod) top.fsMod.recentIds["web"] = '.intval($this->id).';
                </script>
            ';

			$headerSection = $this->doc->getHeader("pages",$this->pageinfo,$this->pageinfo["_thePath"])."<br>".$LANG->sL("LLL:EXT:lang/locallang_core.php:labels.path").": ".t3lib_div::fixed_lgd_pre($this->pageinfo["_thePath"],50);

			$this->content.=$this->doc->startPage($LANG->getLL("title"));
			$this->content.=$this->doc->header($LANG->getLL("title"));
			$this->content.=$this->doc->spacer(5);
			$this->content.=$this->doc->section("",$this->doc->funcMenu($headerSection,t3lib_BEfunc::getFuncMenu($this->id,"SET[function]",$this->MOD_SETTINGS["function"],$this->MOD_MENU["function"])));
			$this->content.=$this->doc->divider(5);


			// Render content:
			$this->content .= $this->moduleContent();

			$this->content .= '
								<input type="hidden" name="send" value="1" />
								<input type="hidden" name="popViewId" value="' . intval(trim($this->modTSconfig['properties']['import.']['pid'])) . '" />
						</form>
			';


			// ShortCut
			if ($BE_USER->mayMakeShortcut())    {
				$this->content.=$this->doc->spacer(20).$this->doc->section("",$this->doc->makeShortcutIcon("id",implode(",",array_keys($this->MOD_MENU)),$this->MCONF["name"]));
			}

			$this->content.=$this->doc->spacer(10);
		} else {
			// If no access or if ID == zero

			$this->doc = t3lib_div::makeInstance("mediumDoc");
			$this->doc->backPath = $BACK_PATH;

			$this->content.=$this->doc->startPage($LANG->getLL("title"));
			$this->content.=$this->doc->header($LANG->getLL("title"));
			$this->content.=$this->doc->spacer(5);
			$this->content.=$this->doc->spacer(10);
		}
	}

	/**
	 * Prints out the module HTML
	 */
	function printContent()    {

		$this->content.=$this->doc->endPage();
		echo $this->content;
	}

	/**
	 * Generates the module content depend on used function ($this->menuConfig())
	 */
	function moduleContent()    {
		global $LANG;
		$content = '';

		$content .='<div align=center><strong>Import of person csv!</strong></div><BR>';
		$content .= $this->addSpecialFields();
		//        $this->addColumnSelection();

		/**
		 * Import / update
		 */
		switch ((string) $this->MOD_SETTINGS['function']) { // show function 1 or 2
			case 'import_csv':
				if (trim($_FILES['file']['name']) != '') {
					$this->import = t3lib_div::makeInstance('tx_x4epersdb_csvImport');
					// init processing class
					$this->import->init($this->modTSconfig['properties']);
					// do processing, get contents
					$content .= $this->import->main(); // Show list
				} elseif (t3lib_div::_POST("send")) {
					$content .= '<fieldset>'.$LANG->getLL("noFileUploaded").'</fieldset>';
				}
				break;
			case 'xyz':
				// do any thing
				break;
			default:
				// do any thing
		}

		return $content;
	}

	/**
	 * evt. Felder der Tabelle im Selectfeld darstellen.
	 *
	 * not used here!
	 *
	 */
	function addColumnSelection() {
		if (t3lib_div::_POST('tableselection') != '') {
			$this->content .= '<fieldset><legend>Column mapping</legend>';
			$tableName = mysql_real_escape_string(t3lib_div::_POST('tableselection'));
			$res = $GLOBALS['TYPO3_DB']->sql_query('SHOW COLUMNS FROM '.$tableName);
			$columns = array();
			while ($c = $GLOBALS['TYPO3_DB']->sql_fetch_row($res)) {
				$columns[] = $c;
			}
			$count = 1;
			foreach($columns as $c) {
				$this->content .= '<fieldset><legend>Column '.$count.'</legend>
                <select name="columnSelection[]">
                  '.$this->columnOptions($columns,$count-1).'
                </select>
            </fieldset>';
				$count++;
			}
			$this->content .= '</fieldset>';
		}
	}

	/**
	 * not used here!
	 *
	 * @param unknown_type $columns
	 * @param unknown_type $count
	 * @return unknown
	 */
	function columnOptions($columns,$count) {
		$selectedColumns = t3lib_div::_POST('columnSelection');
		if (!is_array($selectedColumns)) {
			$selectedColumns = array();
		}
		$options = '<option value=""></option>';
		foreach($columns as $c) {
			if ($c[0] == $selectedColumns[$count]) {
				$selected = 'selected="selected"';
			} else {
				$selected = '';
			}
			$options .= '<option value="'.$c[0].'" '.$selected.'>'.$c[0].'</option>';
		}
		return $options;
	}

	/**
	 * Creates fields for upload form
	 *
	 * @return string
	 */
	function addSpecialFields() {
		global $LANG;
		$content = '
		<fieldset>
			<legend>' . $LANG->getLL("chooseFileLabel") . '</legend>
      File: <input type="file" name="file" />
		  <input type="submit" value="' . $LANG->getLL("upload") . '" />
		</fieldset>';

		return $content;
	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/x4epersdb/mod1/index.php'])    {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/x4epersdb/mod1/index.php']);
}


/**
 * do main programm
 */

// Make instance:
$SOBE = t3lib_div::makeInstance('tx_x4epersdb_module1');
$SOBE->init();

// Include files?
foreach($SOBE->include_once as $INC_FILE){
	include_once($INC_FILE);
}

$SOBE->main();
$SOBE->printContent();

?>