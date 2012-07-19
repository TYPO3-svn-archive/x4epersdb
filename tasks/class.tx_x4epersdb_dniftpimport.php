<?php
class tx_x4epersdb_dniftpimport extends tx_scheduler_Task {

	private $config;
	private $debug = true;

	public function execute() {

			$this->init();

			/**
			 * Programm
			 */

			/**
			 * Get the import file via ftp
			 *
			 * Auskommentiert, weil kein FTP-Upload mehr stattfindet.
			 */
			//if(!$this->loadImportFile())
			//{
			//	// stop wenn failed.
			//	exit;
			//}


			/**
			 * create a temp table in the database
			 */
			$this->createTempTableForUpdate();

			/**
			 * Load export file in the temp table
			 */
			//$import->LoadDataInTempTable();
			//oder
			$this->importDataInTempTable();

			/**
			 * update person infomations
			 */
			$this->updatePersons();

			/**
			 * clear temp table
			 */
			$this->deleteTempTableForUpdate();

			/**
			 * clear import file
			 */
			$this->deleteImportFile();

			return true;
	}

	public function getAdditionalInformation() {

		$this->init();

		$cfg = $this->config;

			// import file
		$destination_file = $cfg["import_file"]["filename"];
		$upload_ftp_folder = $cfg["import_file"]["upload_ftp_folder"];
		$file_name = $upload_ftp_folder . $destination_file;

		return 'Importpfad: '.$file_name;
	}

	function init(){

		/*
		 * Configs / init
		 */
		$config = array(
			'table_temp' => 'tx_x4epersdb_temp',
			'ftp' => array(
				'server' => '',
                      'user' => '',
                      'password' => '',
                      'directory' => '',
						'source_file' => ''
                      )
			  ,'import_file' => array(
					'filename' => 'pers_for_weboffice.txt'
								,'upload_folder' => PATH_site .'uploads/tx_x4epersdb/'
								,'upload_ftp_folder' => PATH_site .'uploads/tx_x4epersdb/import_upload/'
							  ,'fields' => 'datum	uzeit	perid	nachn	vorna	email'
							  ,'field_types' => 'VARCHAR( 255 ) NOT NULL	VARCHAR( 255 ) NOT NULL	INT NOT NULL	VARCHAR( 255 ) NOT NULL	VARCHAR( 255 ) NOT NULL	VARCHAR( 255 ) NOT NULL'
							  ,'delimiter' => '	' // Achtung! 'fields' und 'field_types' muessen durch delimeter getrennt werden.
							  ,'enclosure' => '"'
							  )
			  ,'debug' => false

			);

		$this->config = $config;
		$this->debug = $config["debug"];

	}


	  /**
	   * Creates a temporary table for comparison dni
	   *
	   * @return boolean
	   */
	  public function createTempTableForUpdate() {
			global $GLOBALS;
			$retval = true;
			$cfg    = $this->getConfig();

			/**
			 * init vars
			 */
			$temp_table = $cfg["table_temp"];
			$delimiter = $cfg["import_file"]["delimiter"];
			$a_temp_table_fields = explode($delimiter,$cfg["import_file"]["fields"]);
			$a_temp_table_field_types = explode($delimiter,$cfg["import_file"]["field_types"]);

			/**
			 * build fields sql
			 */
			$a_sql_fields = array();
			for($i=0; $i < sizeof($a_temp_table_fields); $i++){
				$a_sql_fields[] = $a_temp_table_fields[$i] . ' ' . $a_temp_table_field_types[$i];
			}
			$sql_fields = implode("\n,",$a_sql_fields);

			$GLOBALS['TYPO3_DB']->debugOutput = true;


			/**
			 * delete temp table wenn exists
			 */
			$sql = "DROP TABLE IF EXISTS " . $temp_table . " ";
			$res = $GLOBALS['TYPO3_DB']->admin_query($sql);

			/**
			 * create temp table
			 * CREATE TEMPORARY TABLE
			 */
			/*$sql = "CREATE TEMPORARY TABLE " . $temp_table . " (
								" . $sql_fields . "
								) ENGINE = MYISAM ;
				  ";
			*/
		    $sql = "CREATE TABLE " . $temp_table . " (
								" . $sql_fields . "
								) ENGINE = MYISAM ;
		         ";

			$res = $GLOBALS['TYPO3_DB']->admin_query($sql);

			if($res){
			  $this->debug("Temptabelle $temp_table wurde erstellt.");
			} else {
			  $this->debug("Temptabelle $temp_table konnte nicht erstellt werden.");
			  $this->debug($GLOBALS['TYPO3_DB']->sql_error());
			  $this->debug($sql);
			  $retval = false;
			}


			return $retval;
	  }

	/**
	* Import data from csv file into the temporary table. Faster method
	* using LOAD DATA INFILE, but not always available.
	*
	* @return boolean
	*/
	public function loadDataInTempTable() {
	global $GLOBALS;
	$retval = true;
	$cfg    = $this->getConfig();
	$temp_table = $cfg["table_temp"];
	$delimiter = $cfg["import_file"]["delimiter"];
	$enclosure = $cfg["import_file"]["enclosure"];

	// local
	$destination_file = $cfg["import_file"]["filename"];
	$upload_ftp_folder = $cfg["import_file"]["upload_ftp_folder"];

	$file_name = $upload_ftp_folder . $destination_file;

	$sql = "
			LOAD DATA  INFILE '$file_name'
				INTO TABLE $temp_table
				FIELDS
					TERMINATED BY '$delimiter'
					ENCLOSED BY '$enclosure'
	";

	  $this->debug($sql,'LOAD IN FILE SQL:');

	$GLOBALS['TYPO3_DB']->debugOutput = true;
	$res = $GLOBALS['TYPO3_DB']->admin_query($sql);

	if($res) {
	  $this->debug("Temptabelle $temp_table wurde gefuellt.");
	} else {
	  $this->debug($res,'LOAD IN FILE FEHLGESCHALEN:');
	  $this->debug($GLOBALS['TYPO3_DB']->sql_error());
	  $retval = false;
	}

	return $retval;

	}

  /**
   * Import data from csv file line by line into the temporary table.
   *
   * @return boolean
   */
  public function importDataInTempTable()
  {
    global $GLOBALS;
  	$retval = true;
    $cfg    = $this->getConfig();
    // db table
    $temp_table = $cfg["table_temp"];

    // csv
    $delimiter = $cfg["import_file"]["delimiter"];
    $enclosure = $cfg["import_file"]["enclosure"];
    $a_table_fields = explode($delimiter,$cfg["import_file"]["fields"]);

    // import file
    $destination_file = $cfg["import_file"]["filename"];
    $upload_ftp_folder = $cfg["import_file"]["upload_ftp_folder"];
    $file_name = $upload_ftp_folder . $destination_file;

    if(is_file($file_name)){

	    /**
	     * open import file
	     */
	    if($handle = fopen($file_name, 'r')){
		    $GLOBALS['TYPO3_DB']->debugOutput = true;

				while ( ($data = fgetcsv ($handle, 1000, $delimiter, $enclosure)) !== FALSE ){
				    $fields_values = array();
				  	if (sizeof($data) == sizeof($a_table_fields)){
				    	for($i = 0; $i < sizeof($a_table_fields); $i++){
				    		$fields_values[$a_table_fields[$i]] = $data[$i];
				    	}



					    //$this->debug($fields_values);

					    // insert data
					    $res = $GLOBALS['TYPO3_DB']->exec_INSERTquery(
				                                   $temp_table,
				                                   $fields_values
				                                  );
				  	}

			   }

		    fclose($handle);

	    } else {
	    	$this->debug("Konnte die Datei '$file_name' nicht oeffnen!\n");
	    }

    } else {
    	$this->debug("Datei '$file_name' existiert nicht!\n");
    }

    return $retval;

  }

  /**
   * Deletes temporary table
   *
   * @return db result
   */
  public function deleteTempTableForUpdate() {
  	global $GLOBALS;
    /**
     * init vars
     */
  	$cfg    = $this->getConfig();
    $temp_table = $cfg["table_temp"];

    /*
     * delete temp table
     */

  	$sql = "DROP TABLE IF EXISTS " . $temp_table . " ";
  	$res = $GLOBALS['TYPO3_DB']->admin_query($sql);

  	if($res){
  		$this->debug("Temptabelle $temp_table wurde gelöscht.");
  	}

  	return $res;
  }

  /**
   * Get the export file with staff numbers by FTP upload and store files in
   * upload folder from the extension.
   *
   * @return boolean
   */
  public function loadImportFile() {
	$retval = true;
	$cfg    = $this->getConfig();

	$server = $cfg["ftp"]["server"];

	$user   = $cfg["ftp"]["user"];
	$pw     = $cfg["ftp"]["password"];
	// ftp
	$directory        = $cfg["ftp"]["directory"];
	$source_file      = $cfg["ftp"]["source_file"];
	// local
	$destination_file = $cfg["import_file"]["filename"];
	$upload_folder = $cfg["import_file"]["upload_folder"];
	$upload_ftp_folder = $cfg["import_file"]["upload_ftp_folder"];



	// Verbindung aufbauen
	$conn_id = ftp_connect($server);

	// Login mit Benutzername und Passwort
	$login_result = ftp_login($conn_id, $user, $pw);

	// Verbindung überprüfen
	if ((!$conn_id) || (!$login_result)){
	    $this->debug("FTP-Verbindung ist fehlgeschlagen!\n");
	    $retval = false;
	    return $retval;
	} else {
	 $this->debug("Verbunden zu $server mit Benutzername $user\n");
	}

	/**
	* select directory
	*/
	//$this->debug(ftp_rawlist($conn_id, $directory));
	if(!ftp_chdir  ($conn_id , $directory)) {
	    $this->debug("FTP-Verzeichnis '$directory' existiert nicht!\n");
	    $retval = false;
	    return $retval;
    } else {
    	//echo "Aktuelles Verzeichnis: " . ftp_pwd($conn_id) . "\n";
    	$this->debug("FTP-Verzeichnis '$directory' ausgewaehlt\n");
    }

    // check Upload folder
    $this->debug($upload_folder,'uploads:');
    if (!is_dir($upload_folder)) {

    	$isFolderCreated = mkdir($upload_folder);
    	if($isFolderCreated) {
    		@chmod ($upload_folder, 0777);
    	} else {
    		$this->debug("$upload_folder - verzeichnis konnte nicht erstellt werden.\n");
    		$retval = false;
        	return $retval;
    	}
    }

    /**
     * Create / check ftp destination folder.
     */
    if (!is_dir($upload_ftp_folder)) {

    	$isFolderCreated = mkdir($upload_ftp_folder);
    	if($isFolderCreated) {
    		@chmod ($upload_ftp_folder, 0777);
    	} else {
    		$this->debug("$upload_ftp_folder - verzeichnis konnte nicht erstellt werden.\n");
    		$retval = false;
        	return $retval;
    	}
    }



    /**
     * create in not exists .htacces with deny from all
     */
	if(!is_file($upload_ftp_folder . '.htaccess')){
		$htaccess_handle = fopen($upload_ftp_folder . '.htaccess', 'w');
	    fputs( $htaccess_handle , 'deny from     all' . "\n" );
	    fclose($htaccess_handle);
	    @chmod ($upload_ftp_folder . '.htaccess', 0766);
	}



		// Öffne eine Datei zum Schreiben
		$this->debug($upload_ftp_folder . $destination_file,'dest_file:');

	if($destination_file != '' && ($dest_file_handle = fopen($upload_ftp_folder . $destination_file, 'w'))) {
        $this->debug($upload_ftp_folder . $destination_file ." - angelegt\n");
    } else {
        $this->debug($upload_ftp_folder . $destination_file ." -  konnte nicht geschrieben werden!\n");
        $retval = false;
        return $retval;
    }

	// Versuche $remote_file zu laden und in $handle zu speichern
	if (ftp_fget($conn_id, $dest_file_handle, $source_file, FTP_ASCII, 0)){
	  $this->debug("Download erfolgreich\n");
	} else 	{
	  $this->debug("Download von $source_file war nicht möglich\n");
	}


	// Verbindung und Verbindungshandler schließen
	fclose($dest_file_handle);

	// Verbindung schließen
	ftp_close($conn_id);

	return $retval;

  }

  /**
   * Cleans up. Deletes import file
   *
   * @return unknown
   */
  public function deleteImportFile() {
    $retval = true;
    $cfg    = $this->getConfig();

    // local
    $destination_file = $cfg["import_file"]["filename"];
    $upload_ftp_folder = $cfg["import_file"]["upload_ftp_folder"];

    if(!is_file($upload_ftp_folder . $destination_file)) {
    	$this->debug($upload_ftp_folder . $destination_file . ' existiert nicht.');
    	$retval = false;
    	return $retval;
    }

    if(unlink($upload_ftp_folder . $destination_file)){
    	$this->debug($upload_ftp_folder . $destination_file . ' wurde geloscht.');
    } else {
    	$this->debug($upload_ftp_folder . $destination_file . ' konnte nicht geloescht werden.');
    	$retval = false;
    }

    return $retval;

  }

  /**
   * Returns array config
   *
   * @return array
   */
  protected function getConfig() {
  	return $this->config;
  }

  /**
   * Updated personnel number in individuals without this number.
   *
   */
  public function updatePersons() {
  	global $GLOBALS;
  	$cfg = $this->getConfig();

  	$GLOBALS['TYPO3_DB']->debugOutput = true;

  	$userwithoutdni = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
          "uid, email"
          ,"tx_x4epersdb_person"
          ,"dni = 0 AND deleted = 0 AND hidden = 0 AND email != '' AND email is NOT NULL"
          );

    //$this->debug($userwithoutdni);

    $count = 0;
    foreach($userwithoutdni as $user){

         $dni = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
												         'perid,email'
												         ,$cfg["table_temp"]
												         ,' email = "' . $user['email'].'"'
											         );

         //$this->debug($dni);

         if (sizeof($dni) > 0){
         	 $GLOBALS['TYPO3_DB']->exec_UPDATEquery(
         	                        'tx_x4epersdb_person'
         	                       ,'uid = '. $user['uid'] . ' AND deleted = 0 AND hidden = 0'
         	                       ,array ('dni' => $dni[0]['perid'])
         	                       );
         	 $count++;
         }
    } // foreach

    $this->debug("$count Datensaetze wurde(n) aktualisiert.");
  }

  /**
   * makes print for comments and objects. Output can be switched off via
   * configuration
   *
   * @param void $d
   * @param string $m
   */
  private function debug($d, $m = ''){

  	if($this->debug){
  		echo '<pre>';
  		if($m != ''){
  			echo $m . "\n";
  	  	}
  		print_r($d);
  		echo '</pre>';
  	}

  }
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/x4epersdb/tasks/class.tx_x4epersdb_dniftpimport.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/x4epersdb/tasks/class.tx_x4epersdb_dniftpimport.php']);
}
?>