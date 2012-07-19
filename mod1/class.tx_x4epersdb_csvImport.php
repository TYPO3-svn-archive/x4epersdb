<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2010 4eyes GmbH ()
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
 * @author  4eyes   <>
 */
class tx_x4epersdb_csvImport {


	/**
	 * @var object
	 */
	protected $LANG;
	/**
	 * @var string path to typo3
	 */
	protected $backpath;
	/**
	 * PAGE/USER TS Config (module)
	 *
	 * @var array
	 */
	protected $modTSconfig;
	/**
	 * @var array
	 */
	protected $fileExtAllow;
	/**
	 * @var string
	 */
	protected $content;
	/**
	 * @var t3lib_TCEmain
	 */
	protected $tce;

	/**
	 * initialise all settings
	 *
	 * @param array $modTSconfig
	 */
	function init($modTSconfig){

		global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;

		$this->LANG = $LANG;
		$this->backpath = $BACK_PATH;
		$this->modTSconfig = $modTSconfig;
		$this->fileExtAllow = explode(',',$this->modTSconfig['import.']['upload.']['fileExtensions.']['allow']);
		$this->content = '';

		/**
		 * load access (insert/update) to the database
		 */
		$this->tce = t3lib_div::makeInstance('t3lib_TCEmain');
		$this->tce->stripslashes_values = 0;

	}

	/**
	 * Main programm
	 * dos processing upload and import/update via csv file
	 * returns content for the output
	 *
	 * @return string
	 */
	function main(){
		global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;

		/**
		 * verify the uploaded file und target folder.
		 */
		if (trim($_FILES['file']['name']) != '' && 	$this->checkUploadFolder()) {

				if(is_uploaded_file($_FILES['file']['tmp_name'])) {

					/**
					 * simple extension check
					 */
					$a_fileParts = array_reverse(explode('.',$_FILES['file']['name']));
					$s_ext = $a_fileParts[0];
					if (in_array($s_ext,$this->fileExtAllow)){

						/**
						 * copy for processing
						 */
						$pathTarget = $this->getUploadFolder() . $this->modTSconfig['import.']['upload.']['newFileName'];
						move_uploaded_file($_FILES['file']['tmp_name'],$pathTarget);

						/**
						 * Process the import / update if TS Config pid defined
						 */
						if ($this->checkPidDefined()){
							$this->handleImport($pathTarget);
						} else {
							// no pid defined
							$this->content .= '<fieldset>'.$LANG->getLL("noPidDefined").'</fieldset>';
							$this->content .= '<fieldset>Sample TSconfig:<br />
mod.web_txx4epersdbM1 {<br />
&nbsp;	import.pid = [your target pid]<br />
}<br />
</fieldset>';
						}


						/**
						 * clean up
						 */
						$this->deleteImportFile($pathTarget);

					} else {
						// File-extension is not allowed
						$this->content .= '<fieldset>'.$LANG->getLL("fileNotAllowed").'</fieldset>';
					}

				} else {
					// upload security che is failed.
					$this->content .= '<fieldset>'.$LANG->getLL("uploadError").'</fieldset>';
				}

		}	elseif (t3lib_div::_POST("send")) {
			$this->content .= '<fieldset>'.$LANG->getLL("noFileUploaded").'</fieldset>';
		}

		return $this->content;
	}

	/**
	 * Checks the targer pid
	 *
	 * @return boolean
	 */
	protected function checkPidDefined(){
		return intVal($this->modTSconfig['import.']['pid']) ? true : false;
	}

	/**
	 * Target folder for import file upload
	 *
	 * @return string
	 */
	protected function getUploadFolder(){
		$upload_folder = PATH_site .'uploads/tx_x4epersdb/';
		$upload_import_folder = $upload_folder . $this->modTSconfig['import.']['upload.']['folder'] . '/';
		return 	$upload_import_folder;
	}

	/**
	 * checks an creates target folder for upload
	 *
	 * @return boolean
	 */
	protected function checkUploadFolder(){

		$retval = true;
		$upload_folder = PATH_site .'uploads/tx_x4epersdb/';
		$upload_import_folder = $upload_folder . $this->modTSconfig['import.']['upload.']['folder'] . '/';

    if (!is_dir($upload_folder)) {
    	$isFolderCreated = mkdir($upload_folder);
    	if($isFolderCreated) {
    		@chmod ($upload_folder, 0777);
    	} else {
    		$this->content .= "<fieldset>Extension upload directory does not exist and could not be created.</fieldset>\n";
    		$retval = false;
    	}
    }

    /**
     * Create / check upload destination folder.
     */
    if (!is_dir($upload_import_folder) && $retval) {

    	$isFolderCreated = mkdir($upload_import_folder);
    	if($isFolderCreated) {

    		@chmod ($upload_import_folder, 0777);

    		/**
    		 * create in not exists .htacces with deny from all
    		 */
    		if(!is_file($upload_import_folder . '.htaccess')){
    			$htaccess_handle = fopen($upload_import_folder . '.htaccess', 'w');
    			fputs( $htaccess_handle , 'deny from     all' . "\n" );
    			fclose($htaccess_handle);
    			@chmod ($upload_import_folder . '.htaccess', 0766);
    		}

    	} elseif($retval)  {
    		$this->content .="<fieldset>Upload extension for import directory does not exist and could not be created.</fieldset>\n\n";
    		$retval = false;
    	}
    }

    return $retval;

	}

	/**
	 * deletes uploaded file
	 *
	 * @param string $pathTarget
	 */
	protected function deleteImportFile($pathTarget){

		$retVal = true;
		if (is_file($pathTarget)){
			$retVal = @unlink($pathTarget);
		} else {
			$retVal = false;
		}
		return $retVal;
	}


	/**
	 * Handles import / update import file
	 *
	 * @param string $pathSource
	 * @return boolean
	 */
	protected function handleImport($pathSource) {

		/**
		 * set vars
		 */
		$retval = true;
		$content = '';
		$skipRow = true;

		// target table
		$table               = $this->modTSconfig['import.']['table'];
		$fields              = $this->modTSconfig['import.']['reference.']['csvToTableFields.'];
		$fieldReferenceIndex = $this->modTSconfig['import.']['reference.']['fieldForUpdate'];
		$fieldReference      = $fields[$fieldReferenceIndex . '.'];

		// csv
		$skipFirstRow = $this->modTSconfig['import.']['csv.']['skipFirstRow'];
		$delimiter = $this->modTSconfig['import.']['csv.']['delimiter'];
		$enclosure = $this->modTSconfig['import.']['csv.']['enclosure'];

		$counter_rows = 0;
		$counter_error_brocken_rows = 0;
		$counter_error_update = 0;
		$counter_error_insert = 0;
		$counter_insert = 0;
		$counter_update = 0;


		/**
		 * open the uploaded file
		 */
		$handle = fopen($pathSource, 'r');

		if($handle){

			/**
			 * set all Persons to hidden
			 */
			$this->setAllPersonsToHidden($table);

			/**
			 * process 	line by line
			 */
			while ( ($data = fgetcsv ($handle, 1000, $delimiter, $enclosure)) !== FALSE ){

				/**
				 * erste Zeile überspringen;
				 */
				if($skipRow && $skipFirstRow){
					$skipRow = false;
					continue;
				}

				$fields_values = array();
				/**
				 * simple validate row values
				 */
		  	if (sizeof($data) >= sizeof($fields)){

		  		$ref_value = $data[$fieldReference['fieldCsvIndex']];

		  		/**
		  		 * build table row
		  		 */
		  		$values = array();
		  		foreach($fields as $field){
		  			// only if fieldTableName defined
		  			if(trim($field['fieldTableName']) != '' ){
		  				$values[$field['fieldTableName']] = trim($this->prepareValue($data[intVal($field['fieldCsvIndex'])]));
		  			}
		  		}
					//t3lib_div::debug($data);


					/**
					 * update import table row
					 */
					$uid_person = $this->rowExists($table,$fieldReference['fieldTableName'], $ref_value);

					//echo $ref_value . '<br />';
				
				if($values['external_id'] > 103){
					if($_SERVER['REMOTE_ADDR'] == '109.164.219.204'){
						t3lib_div::debug(array($uid_person,$values),$values['external_id']);
					}
				}

		  		if($uid_person){
		  			// do update
						if($this->updateRow($table,$uid_person ,$values)){
							$counter_update++;
						} else {
							$counter_error_update++;
						}
		  		} else {
		  			// do insert
						if($this->insertRow($table,$values)){
							$counter_insert++;
						} else {
							$counter_error_update++;
						}
		  		}



		  	} else {
		  		//if broken row (empty or incorrect number of columns)
		  		$counter_error_brocken_rows++;
		  	}

				$counter_rows++;
			} // while

			//t3lib_div::debug($data);

			/**
			 * close the file
			 */
			fclose($handle);

			$content .= '
<fieldset>
	<strong>' . $this->LANG->getLL("importDone") . '</strong><br />
	' . $this->LANG->getLL("rowsUpdated") . '<br />
	' . $this->LANG->getLL("rowsInserted") . '<br />
	' . $this->LANG->getLL("rowsErrorBrockenRow") . '<br />
	' . $this->LANG->getLL("rowsErrorUpdate") . '<br />
	' . $this->LANG->getLL("rowsErrorInsert") . '<br />
	' . $this->LANG->getLL("rowsProcessed") . '<br />
</fieldset>
												';


			$this->content .= sprintf($content
																	,$counter_update
																	,$counter_insert
																	,$counter_error_brocken_rows
																	,$counter_error_update
																	,$counter_error_insert
																	,$counter_rows );

			t3lib_BEfunc::getSetUpdateSignal('updatePageTree');

		} else {
			// can not open the file
			$this->content .= '<fieldset>Import file could not be opened.</fieldset>';
			$retVal = false;
		}

		return $retVal;

	}

	protected function prepareValue($value){
		$sourceFormat  = $this->modTSconfig['import.']['csv.']['sourceFormat'];

		switch(strtolower(trim($sourceFormat))){

			case 'utf8':

				break;

			case 'iso-8859-1':
				$value = iconv( 'iso-8859-1', 'utf-8//TRANSLIT//IGNORE', $value);
				break;

			case 'windows-1252':
				$value = iconv( 'Windows-1252', 'utf-8//TRANSLIT//IGNORE', $value);
				break;

			case "iso-8859-15":
				$value = iconv("ISO-8859-15", 'utf-8//TRANSLIT//IGNORE', $value);
				break;

			case "iso-8859-6":
				$value = iconv("ISO-8859-6", 'utf-8//TRANSLIT//IGNORE', $value);
				break;

			case "cp1256":
				$value = iconv("CP1256", 'utf-8//TRANSLIT//IGNORE', $value);
				break;

			default:
				$value = utf8_encode($value);
		}


		$value = str_replace("\v",",",$value);


		$value = $this->sqlEscape($value);

		return $value;
	}

	protected function sqlEscape($value){

		return mysql_escape_string($value);
	}

	function setAllPersonsToHidden($table){

		// only in sysfolder set to hidden
		$where = 'deleted != 1 AND pid = ' . $this->modTSconfig['import.']['pid'];

		$res = $GLOBALS['TYPO3_DB']->exec_UPDATEquery(
																			$table,
																			$where,
																			array('hidden' => 1)
																		);

		return 	$res;


	}


	/**
	 * Check csv line, if there exits already
	 *
	 * @param string $table
	 * @param string $fieldReference
	 * @param string $ref_value
	 * @return boolean
	 */
	protected function rowExists($table,$fieldReference, $ref_value){

		global $GLOBALS;
		$retVal = false;

		$updateOnlyEqualToCurrentPid = intval($this->modTSconfig['import.']['csv.']['updateOnlyEqualToCurrentImportPid']);
		$currentImportPid = $this->modTSconfig['import.']['pid'];

		if($updateOnlyEqualToCurrentPid){
			$wherePid = 'AND pid = ' . $currentImportPid;
		}

		$where = '' . $fieldReference . ' LIKE "' . trim($ref_value) . '"
							 AND  ' . $fieldReference . ' NOT LIKE ""
							 AND  ' . $fieldReference . ' IS NOT NULL
							 AND  deleted != 1
							 ' . $wherePid . '
						';


		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
																			'*',
																			$table,
																			$where
																		);

		if($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))	{
			//t3lib_div::debug($row);
			$retVal = $row['uid'];
		}
		return $retVal;
	}

	/**
	 * Update an existing table row
	 *
	 * @param string $table
	 * @param array $values
	 * @param string $fieldReference
	 * @return boolean
	 */
	protected function updateRow($table,$uid,$values){
		$retVal = true;
		$pid = $this->modTSconfig['import.']['pid'];
		$updatePid = intval($this->modTSconfig['import.']['csv.']['updatePid']);

		$data = array();
		$cmd = array();

		foreach ($values as $field => $value){
			$data[$table][$uid][$field] = trim($value);
		}

		$data[$table][$uid]['hidden'] = 0;

   	$this->tce->start($data,array());
   	$this->tce->process_datamap();


		if($updatePid) {
			// set the pid
			$cmd[$table][$uid]['move'] = $pid;
			$this->tce->start(array(),$cmd);
			$this->tce->process_cmdmap();
		}

		return $retVal;
	}

	/**
	 * Enter description here...
	 *
	 * @param string $table
	 * @param array $values
	 * @return boolean
	 */
	protected function insertRow($table,$values){
		$retVal = true;
		$uid = 'NEW' . $this->randStr();
		$pid = $this->modTSconfig['import.']['pid'];
		$data = array();

		foreach ($values as $field => $value){
			$data[$table][$uid][$field] = trim($value);
		}
		// set the pid
		$data[$table][$uid]['pid'] = $pid;

		//t3lib_div::debug($data);

   	$this->tce->start($data,array());
   	$this->tce->process_datamap();

		return $retVal;
	}

	/**
	 * Generate a random character string
	 *
	 */
	protected function randStr()
	{
		$length = 12;
		$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890';

		// Length of character list
		$chars_length = (strlen($chars) - 1);

		// Start our string
		$string = $chars{rand(0, $chars_length)};

		// Generate random string
		for ($i = 1; $i < $length; $i = strlen($string))
		{
			// Grab a random character from our list
			$r = $chars{rand(0, $chars_length)};

			// Make sure the same two characters don't appear next to each other
			if ($r != $string{$i - 1}) $string .=  $r;
		}

		// Return the string
		return $string;
	}

}
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/x4epersdb/mod1/class.tx_x4epersdb_csvImport.php'])    {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/x4epersdb/mod1/class.tx_x4epersdb_csvImport.php']);
}
?>