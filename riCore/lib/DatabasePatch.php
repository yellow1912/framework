<?php
/**
 * Our main class for parsing sql files and execute
 *
 */
namespace plugins\riCore;

/* We want to use ZenCart sqlpatch.php,but for now we have to do it the ugly way.......*/
define('ERROR_NOTHING_TO_DO','Error: Nothing to do - no query or query-file specified.');
if (!defined('DB_PREFIX')) define('DB_PREFIX','');
if (!defined('TABLE_UPGRADE_EXCEPTIONS')) define('TABLE_UPGRADE_EXCEPTIONS', DB_PREFIX . 'upgrade_exceptions');
define('REASON_TABLE_ALREADY_EXISTS','Cannot create table %s because it already exists');
define('REASON_TABLE_DOESNT_EXIST','Cannot drop table %s because it does not exist.');
define('REASON_TABLE_NOT_FOUND','Cannot execute because table %s does not exist.');
define('REASON_CONFIG_KEY_ALREADY_EXISTS','Cannot insert configuration_key "%s" because it already exists');
define('REASON_COLUMN_ALREADY_EXISTS','Cannot ADD column %s because it already exists.');
define('REASON_COLUMN_DOESNT_EXIST_TO_DROP','Cannot DROP column %s because it does not exist.');
define('REASON_COLUMN_DOESNT_EXIST_TO_CHANGE','Cannot CHANGE column %s because it does not exist.');
define('REASON_PRODUCT_TYPE_LAYOUT_KEY_ALREADY_EXISTS','Cannot insert prod-type-layout configuration_key "%s" because it already exists');
define('REASON_INDEX_DOESNT_EXIST_TO_DROP','Cannot drop index %s on table %s because it does not exist.');
define('REASON_PRIMARY_KEY_DOESNT_EXIST_TO_DROP','Cannot drop primary key on table %s because it does not exist.');
define('REASON_INDEX_ALREADY_EXISTS','Cannot add index %s to table %s because it already exists.');
define('REASON_PRIMARY_KEY_ALREADY_EXISTS','Cannot add primary key to table %s because a primary key already exists.');
define('REASON_NO_PRIVILEGES','User '.DB_SERVER_USERNAME.'@'.DB_SERVER.' does not have %s privileges to database '.DB_DATABASE.'.');
// can be set later
if (isset($_GET['debug']) && $_GET['debug']=='ON') $debug=true;
if (!isset($_GET['debug'])  && !zen_not_null($_POST['debug']) && $debug!=true)  define('ZC_UPG_DEBUG',false);
if (!isset($_GET['debug2']) && !zen_not_null($_POST['debug2']) && $debug!=true) define('ZC_UPG_DEBUG2',false);
if (!isset($_GET['debug3']) && !zen_not_null($_POST['debug3']) && $debug!=true) define('ZC_UPG_DEBUG3',false);
$keepslashes = (isset($_GET['keepslashes']) && ($_GET['keepslashes']=='1' || $_GET['keepslashes']=='true')) ? true : false;
//NOTE: THIS IS INTENTIONALLY ON 2 LINES:
$linebreak = '
	';
// NOTE: this line break is intentional!!!!
//////////////////////// End DEF //////////////////////////////////////

/**
 * reuse the original database patcher of Zencart with some minor changes
 */
class DatabasePatch{

    /**
     * executes sql file
     *
     * @param $upload_query
     * @return bool
     */
	function executeSqlFile($upload_query){
		global $messageStack;
		$query_string  = $upload_query;
		if (@get_magic_quotes_runtime() > 0) $query_string  = zen_db_prepare_input($upload_query);
		$success = false;
		if ($query_string !='') {
			$query_results = $this->executeSql($query_string, DB_DATABASE, DB_PREFIX);
			if ($query_results['queries'] > 0 && $query_results['queries'] != $query_results['ignored']) {
				$messageStack->add($query_results['queries']. ' statements processed.', 'success');
				$success = true;
			} else {
				$messageStack->add('Failed: '.$query_results['queries'], 'error');
			}
			if (zen_not_null($query_results['errors'])) {
				foreach ($query_results['errors'] as $value) {
					$messageStack->add('ERROR: '.$value, 'error');
				}
			}
			if ($query_results['ignored'] != 0) {
				$messageStack->add('Note: '.$query_results['ignored'].' statements ignored. See "upgrade_exceptions" table for additional details.', 'caution');
			}
			if (zen_not_null($query_results['output'])) {
				foreach ($query_results['output'] as $value) {
					if (zen_not_null($value)) $messageStack->add('ERROR: '.$value, 'error');
				}
			}
		} else {
			$messageStack->add(ERROR_NOTHING_TO_DO, 'error');
		}
		return $success;
	}

    /**
     * executes sql lines
     *
     * @param $lines
     * @param $database
     * @param string $table_prefix
     * @return array
     */
	function executeSql($lines, $database, $table_prefix = '') {
		global $db, $debug, $keepslashes;
		if (!get_cfg_var('safe_mode')) {
			@set_time_limit(1200);
		}

		$sql_file='SQLPATCH';
		$newline = '';
		$saveline = '';
		$ignored_count=0;
		$return_output=array();
		$errors = array();

		foreach ($lines as $line) {
			if ($_GET['debug']=='ON') echo $line . '<br />';

			$line = trim($line);
			$line = str_replace('`','',$line); //remove backquotes
			$line = $saveline . $line;
			$keep_together = 1; // count of number of lines to treat as a single command

			// split the line into words ... starts at $param[0] and so on.  Also remove the ';' from end of last param if exists
			$param=explode(" ",(substr($line,-1)==';') ? substr($line,0,strlen($line)-1) : $line);

			// The following command checks to see if we're asking for a block of commands to be run at once.
			// Syntax: #NEXT_X_ROWS_AS_ONE_COMMAND:6     for running the next 6 commands together (commands denoted by a ;)
			if (substr($line,0,28) == '#NEXT_X_ROWS_AS_ONE_COMMAND:') $keep_together = substr($line,28);
			if (substr($line,0,1) != '#' && substr($line,0,1) != '-' && $line != '') {
				//        if ($table_prefix != -1) {
				//echo '*}'.$line.'<br>';

				$line_upper=strtoupper($line);
				switch (true) {
					case (substr($line_upper, 0, 21) == 'DROP TABLE IF EXISTS '):
						//            if (!$checkprivs = $this->zen_check_database_privs('DROP')) return sprintf(REASON_NO_PRIVILEGES,'DROP');
						$line = 'DROP TABLE IF EXISTS ' . $table_prefix . substr($line, 21);
						break;
					case (substr($line_upper, 0, 11) == 'DROP TABLE ' && $param[2] != 'IF'):
						if (!$checkprivs = $this->zen_check_database_privs('DROP')) $result=sprintf(REASON_NO_PRIVILEGES,'DROP');
						if (!$this->zen_table_exists($param[2]) || zen_not_null($result)) {
							$this->zen_write_to_upgrade_exceptions_table($line, (zen_not_null($result) ? $result : sprintf(REASON_TABLE_DOESNT_EXIST,$param[2])), $sql_file);
							$ignore_line=true;
							$result=(zen_not_null($result) ? $result : sprintf(REASON_TABLE_DOESNT_EXIST,$param[2])); //duplicated here for on-screen error-reporting
							break;
						} else {
							$line = 'DROP TABLE ' . $table_prefix . substr($line, 11);
						}
						break;
					case (substr($line_upper, 0, 13) == 'CREATE TABLE '):
						// check to see if table exists
						$table = (strtoupper($param[2].' '.$param[3].' '.$param[4]) == 'IF NOT EXISTS') ? $param[5] : $param[2];
						$result = $this->zen_table_exists($table);
						if ($result==true) {
							$this->zen_write_to_upgrade_exceptions_table($line, sprintf(REASON_TABLE_ALREADY_EXISTS,$table), $sql_file);
							//$ignore_line=true;
							$result=sprintf(REASON_TABLE_ALREADY_EXISTS,$table); //duplicated here for on-screen error-reporting
							break;
						} else {
							$line = (strtoupper($param[2].' '.$param[3].' '.$param[4]) == 'IF NOT EXISTS') ? 'CREATE TABLE IF NOT EXISTS ' . $table_prefix . substr($line, 27) : 'CREATE TABLE ' . $table_prefix . substr($line, 13);
						}
						break;
					case (substr($line_upper, 0, 15) == 'TRUNCATE TABLE '):
						// check to see if TRUNCATE command may be safely executed
						if (!$tbl_exists = $this->zen_table_exists($param[2])) {
							$result=sprintf(REASON_TABLE_NOT_FOUND,$param[2]).' CHECK PREFIXES!' . $param[2];
							$this->zen_write_to_upgrade_exceptions_table($line, $result, $sql_file);
							$ignore_line=true;
							break;
						} else {
							$line = 'TRUNCATE TABLE ' . $table_prefix . substr($line, 15);
						}
						break;
					case (substr($line_upper, 0, 13) == 'REPLACE INTO '):
						//check to see if table prefix is going to match
						if (!$tbl_exists = $this->zen_table_exists($param[2])) $result=sprintf(REASON_TABLE_NOT_FOUND,$param[2]).' CHECK PREFIXES!';
						// check to see if INSERT command may be safely executed for "configuration" or "product_type_layout" tables
						if (($param[2]=='configuration'       && ($result=$this->zen_check_config_key($line))) or
						($param[2]=='product_type_layout' && ($result=$this->zen_check_product_type_layout_key($line))) or
						(!$tbl_exists)    ) {
							$this->zen_write_to_upgrade_exceptions_table($line, $result, $sql_file);
							$ignore_line=true;
							break;
						} else {
							$line = 'REPLACE INTO ' . $table_prefix . substr($line, 13);
						}
						break;
					case (substr($line_upper, 0, 12) == 'INSERT INTO '):
						//check to see if table prefix is going to match
						if (!$tbl_exists = $this->zen_table_exists($param[2])) $result=sprintf(REASON_TABLE_NOT_FOUND,$param[2]).' CHECK PREFIXES!';
						// check to see if INSERT command may be safely executed for "configuration" or "product_type_layout" tables
						if (($param[2]=='configuration'       && ($result=$this->zen_check_config_key($line))) or
						($param[2]=='product_type_layout' && ($result=$this->zen_check_product_type_layout_key($line))) or
						(!$tbl_exists)    ) {
							$this->zen_write_to_upgrade_exceptions_table($line, $result, $sql_file);
							$ignore_line=true;
							break;
						} else {
							$line = 'INSERT INTO ' . $table_prefix . substr($line, 12);
						}
						break;
					case (substr($line_upper, 0, 19) == 'INSERT IGNORE INTO '):
						//check to see if table prefix is going to match
						if (!$tbl_exists = $this->zen_table_exists($param[3])) {
							$result=sprintf(REASON_TABLE_NOT_FOUND,$param[3]).' CHECK PREFIXES!';
							$this->zen_write_to_upgrade_exceptions_table($line, $result, $sql_file);
							$ignore_line=true;
							break;
						} else {
							$line = 'INSERT IGNORE INTO ' . $table_prefix . substr($line, 19);
						}
						break;
					case (substr($line_upper, 0, 12) == 'ALTER TABLE '):
						// check to see if ALTER command may be safely executed
						if ($result=$this->zen_check_alter_command($param)) {
							$this->zen_write_to_upgrade_exceptions_table($line, $result, $sql_file);
							$ignore_line=true;
							break;
						} else {
							$line = 'ALTER TABLE ' . $table_prefix . substr($line, 12);
						}
						break;
					case (substr($line_upper, 0, 13) == 'RENAME TABLE '):
						//
							$temp = preg_replace('/(RENAME TABLE)|(\sTO\s)/',' ', $line_upper);
							$temp = strtolower(trim(preg_replace('/\s\s+/','', $temp)));
							$temp = explode(' ',$temp);
							$line = 'RENAME TABLE ' . $table_prefix . $temp[0].' TO '. $table_prefix . $temp[1];
						break;
					case (substr($line_upper, 0, 7) == 'UPDATE '):
						//check to see if table prefix is going to match
						if (!$tbl_exists = $this->zen_table_exists($param[1])) {
							$this->zen_write_to_upgrade_exceptions_table($line, sprintf(REASON_TABLE_NOT_FOUND,$param[1]).' CHECK PREFIXES!', $sql_file);
							$result=sprintf(REASON_TABLE_NOT_FOUND,$param[1]).' CHECK PREFIXES!';
							$ignore_line=true;
							break;
						} else {
							$line = 'UPDATE ' . $table_prefix . substr($line, 7);
						}
						break;
					case (substr($line_upper, 0, 14) == 'UPDATE IGNORE '):
						//check to see if table prefix is going to match
						if (!$tbl_exists = $this->zen_table_exists($param[2])) {
							$this->zen_write_to_upgrade_exceptions_table($line, sprintf(REASON_TABLE_NOT_FOUND,$param[2]).' CHECK PREFIXES!', $sql_file);
							$result=sprintf(REASON_TABLE_NOT_FOUND,$param[2]).' CHECK PREFIXES!';
							$ignore_line=true;
							break;
						} else {
							$line = 'UPDATE IGNORE ' . $table_prefix . substr($line, 14);
						}
						break;
					case (substr($line_upper, 0, 12) == 'DELETE FROM '):
						$line = 'DELETE FROM ' . $table_prefix . substr($line, 12);
						break;
					case (substr($line_upper, 0, 11) == 'DROP INDEX '):
						// check to see if DROP INDEX command may be safely executed
						if ($result=$this->zen_drop_index_command($param)) {
							$this->zen_write_to_upgrade_exceptions_table($line, $result, $sql_file);
							$ignore_line=true;
							break;
						} else {
							$line = 'DROP INDEX ' . $param[2] . ' ON ' . $table_prefix . $param[4];
						}
						break;
					case (substr($line_upper, 0, 13) == 'CREATE INDEX ' || (strtoupper($param[0])=='CREATE' && strtoupper($param[2])=='INDEX')):
						// check to see if CREATE INDEX command may be safely executed
						if ($result=$this->zen_create_index_command($param)) {
							$this->zen_write_to_upgrade_exceptions_table($line, $result, $sql_file);
							$ignore_line=true;
							break;
						} else {
							if (strtoupper($param[1])=='INDEX') {
								$line = trim('CREATE INDEX ' . $param[2] .' ON '. $table_prefix . implode(' ',array($param[4],$param[5],$param[6],$param[7],$param[8],$param[9],$param[10],$param[11],$param[12],$param[13])) ).';'; // add the ';' back since it was removed from $param at start
							} else {
								$line = trim('CREATE '. $param[1] .' INDEX ' .$param[3]. ' ON '. $table_prefix . implode(' ',array($param[5],$param[6],$param[7],$param[8],$param[9],$param[10],$param[11],$param[12],$param[13])) ); // add the ';' back since it was removed from $param at start
							}
						}
						break;
					case (substr($line_upper, 0, 8) == 'SELECT (' && substr_count($line,'FROM ')>0):
						$line = str_replace('FROM ','FROM '. $table_prefix, $line);
						break;
					case (substr($line_upper, 0, 10) == 'LEFT JOIN '):
						$line = 'LEFT JOIN ' . $table_prefix . substr($line, 10);
						break;
					case (substr($line_upper, 0, 5) == 'FROM '):
						if (substr_count($line,',')>0) { // contains FROM and a comma, thus must parse for multiple tablenames
							$tbl_list = explode(',',substr($line,5));
							$line = 'FROM ';
							foreach($tbl_list as $val) {
								$line .= $table_prefix . trim($val) . ','; // add prefix and comma
							} //end foreach
							if (substr($line,-1)==',') $line = substr($line,0,(strlen($line)-1)); // remove trailing ','
						} else { //didn't have a comma, but starts with "FROM ", so insert table prefix
							$line = str_replace('FROM ', 'FROM '.$table_prefix, $line);
						}//endif substr_count(,)
						break;
                    case (strpos($line_upper, 'REFERENCES') !== false):
                        $line = str_replace('REFERENCES ', 'REFERENCES '.$table_prefix, $line);
                        break;
					default:
						break;
				} //end switch
				//        } // endif $table_prefix
				$newline .= $line . ' ';

				if ( substr($line,-1) ==  ';') {
					//found a semicolon, so treat it as a full command, incrementing counter of rows to process at once
					if (substr($newline,-1)==' ') $newline = substr($newline,0,(strlen($newline)-1));
					$lines_to_keep_together_counter++;
					if ($lines_to_keep_together_counter == $keep_together) { // if all grouped rows have been loaded, go to execute.
						$complete_line = true;
						$lines_to_keep_together_counter=0;
					} else {
						$complete_line = false;
					}
				} //endif found ';'

				if ($complete_line) {
					if ($debug==true) echo ((!$ignore_line) ? '<br />About to execute.': 'Ignoring statement. This command WILL NOT be executed.').'<br />Debug info:<br>$ line='.$line.'<br>$ complete_line='.$complete_line.'<br>$ keep_together='.$keep_together.'<br>SQL='.$newline.'<br><br>';
					if (get_magic_quotes_runtime() > 0  && $keepslashes != true ) $newline=stripslashes($newline);
					if (trim(str_replace(';','',$newline)) != '' && !$ignore_line) $output=$db->Execute($newline);
					$results++;
					$string .= $newline.'<br />';
					$return_output[]=$output;
					if (zen_not_null($result)) $errors[]=$result;
					// reset var's
					$newline = '';
					$keep_together=1;
					$complete_line = false;
					if ($ignore_line) $ignored_count++;
					$ignore_line=false;

					// show progress bar
					global $zc_show_progress;
					if ($zc_show_progress=='yes') {
						$counter++;
						if ($counter/5 == (int)($counter/5)) echo '~ ';
						if ($counter>200) {
							echo '<br /><br />';
							$counter=0;
						}
						@ob_flush();
						@flush();
					}

				} //endif $complete_line

			} //endif ! # or -
		} // end foreach $lines
		return array('queries'=> $results, 'string'=>$string, 'output'=>$return_output, 'ignored'=>($ignored_count), 'errors'=>$errors);
	} //end function

    /**
     * checks to see if the table exists
     *
     * @param $tablename
     * @param bool $append_prefix
     * @return bool
     */
	function zen_table_exists($tablename, $append_prefix = true) {
		global $db;
        if($append_prefix) $tablename = DB_PREFIX . $tablename;

		$tables = $db->Execute("SHOW TABLES like '" . $tablename . "'");
		if (ZC_UPG_DEBUG3==true) echo 'Table check ('.$tablename.') = '. $tables->RecordCount() .'<br>';
		if ($tables->RecordCount() > 0) {
			return true;
		} else {
			return false;
		}
	}

    /**
     * checks database permission
     *
     * @param string $priv
     * @param string $table
     * @param bool $show_privs
     * @return bool|string
     */
	function zen_check_database_privs($priv='',$table='',$show_privs=false) {
		// bypass until future version
		return true;
		// end bypass
		global $zdb_server, $zdb_user, $zdb_name;
		if (isset($_GET['nogrants'])) return true; // bypass if flag set
		if (isset($_POST['nogrants'])) return true; // bypass if flag set
		//Display permissions, or check for suitable permissions to carry out a particular task
		//possible outputs:
		//GRANT ALL PRIVILEGES ON *.* TO 'xyz'@'localhost' WITH GRANT OPTION
		//GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, DROP, FILE, INDEX, ALTER ON *.* TO 'xyz'@'localhost' IDENTIFIED BY PASSWORD '2344'
		//GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, DROP, INDEX, ALTER ON `db1`.* TO 'xyz'@'localhost'
		//GRANT SELECT (id) ON db1.tablename TO 'xyz'@'localhost
		global $db;
		global $db_test;
		$granted_privs_list='';
		if (ZC_UPG_DEBUG3==true) echo '<br />Checking for priv: ['.(zen_not_null($priv) ? $priv : 'none specified').']<br />';
		if (!defined('DB_SERVER'))          define('DB_SERVER',$zdb_server);
		if (!defined('DB_SERVER_USERNAME')) define('DB_SERVER_USERNAME',$zdb_user);
		if (!defined('DB_DATABASE'))        define('DB_DATABASE',$zdb_name);
		$user = DB_SERVER_USERNAME."@".DB_SERVER;
		if ($user == 'DB_SERVER_USERNAME@DB_SERVER' || DB_DATABASE=='DB_DATABASE') return true; // bypass if constants not set properly
		$sql = "show grants for ".$user;
		if (ZC_UPG_DEBUG3==true) echo $sql.'<br />';
		$result = $db->Execute($sql);
		while (!$result->EOF) {
			if (ZC_UPG_DEBUG3==true) echo $result->fields['Grants for '.$user].'<br />';
			$grant_syntax = $result->fields['Grants for '.$user] . ' ';
			$granted_privs = str_replace('GRANT ','',$grant_syntax); // remove "GRANT" keyword
			$granted_privs = substr($granted_privs,0,strpos($granted_privs,' TO ')); //remove anything after the "TO" keyword
			$granted_db = str_replace(array('`','\\'),'',substr($granted_privs,strpos($granted_privs,' ON ')+4) ); //remove backquote and find "ON" string
			if (ZC_UPG_DEBUG3==true) echo 'privs_list = '.$granted_privs.'<br />';
			if (ZC_UPG_DEBUG3==true) echo 'granted_db = '.$granted_db.'<br />';
			$db_priv_ok += ($granted_db == '*.*' || $granted_db==DB_DATABASE.'.*' || $granted_db==DB_DATABASE.'.'.$table) ? true : false;
			if (ZC_UPG_DEBUG3==true) echo 'db-priv-ok='.$db_priv_ok.'<br />';

			if ($db_priv_ok) {  // if the privs list pertains to the current database, or is *.*, carry on
				$granted_privs = substr($granted_privs,0,strpos($granted_privs,' ON ')); //remove anything after the "ON" keyword
				$granted_privs_list .= ($granted_privs_list=='') ? $granted_privs : ', '.$granted_privs;

				$specific_priv_found = (zen_not_null($priv) && substr_count($granted_privs,$priv)==1);
				if (ZC_UPG_DEBUG3==true) echo 'specific priv['.$priv.'] found ='.$specific_priv_found.'<br />';

				if (ZC_UPG_DEBUG3==true) echo 'spec+db='.($specific_priv_found && $db_priv_ok == true).' ||| ';
				if (ZC_UPG_DEBUG3==true) echo 'all+db='.($granted_privs == 'ALL PRIVILEGES' && $db_priv_ok==true).'<br /><br />';

				if (($specific_priv_found && $db_priv_ok == true) || ($granted_privs == 'ALL PRIVILEGES' && $db_priv_ok==true)) {
					return true; // privs found
				}
			} // endif $db_priv_ok
			$result->MoveNext();
		}
		if ($show_privs) {
			if (ZC_UPG_DEBUG3==true) echo 'LIST OF PRIVS='.$granted_privs_list.'<br />';
			return $db_priv_ok . '|||'. $granted_privs_list;
		} else {
			return false; // if not found, return false
		}
	}

    /**
     * drops index
     *
     * @param $param
     * @return string
     */
	function zen_drop_index_command($param) {
		if (!$checkprivs = $this->zen_check_database_privs('INDEX')) return sprintf(REASON_NO_PRIVILEGES,'INDEX');
		//this is only slightly different from the ALTER TABLE DROP INDEX command
		global $db;
		if (!zen_not_null($param)) return "Empty SQL Statement";
		$index = $param[2];
		$sql = "show index from " . DB_PREFIX . $param[4];
		$result = $db->Execute($sql);
		while (!$result->EOF) {
			if (ZC_UPG_DEBUG3==true) echo $result->fields['Key_name'].'<br />';
			if  ($result->fields['Key_name'] == $index) {
				//        if (!$checkprivs = $this->zen_check_database_privs('INDEX')) return sprintf(REASON_NO_PRIVILEGES,'INDEX');
				return; // if we get here, the index exists, and we have index privileges, so return with no error
			}
			$result->MoveNext();
		}
		// if we get here, then the index didn't exist
		return sprintf(REASON_INDEX_DOESNT_EXIST_TO_DROP,$index,$param[4]);
	}

    /**
     * creates index
     *
     * @param $param
     * @return string
     */
	function zen_create_index_command($param) {
		//this is only slightly different from the ALTER TABLE CREATE INDEX command
		if (!$checkprivs = $this->zen_check_database_privs('INDEX')) return sprintf(REASON_NO_PRIVILEGES,'INDEX');
		global $db;
		if (!zen_not_null($param)) return "Empty SQL Statement";
		$index = (strtoupper($param[1])=='INDEX') ? $param[2] : $param[3];
		if (in_array('USING',$param)) return 'USING parameter found. Cannot validate syntax. Please run manually in phpMyAdmin.';
		$table = (strtoupper($param[2])=='INDEX' && strtoupper($param[4])=='ON') ? $param[5] : $param[4];
		$sql = "show index from " . DB_PREFIX . $table;
		$result = $db->Execute($sql);
		while (!$result->EOF) {
			if (ZC_UPG_DEBUG3==true) echo $result->fields['Key_name'].'<br />';
			if (strtoupper($result->fields['Key_name']) == strtoupper($index)) {
				return sprintf(REASON_INDEX_ALREADY_EXISTS,$index,$table);
			}
			$result->MoveNext();
		}
		/*
		* @TODO: verify that individual columns exist, by parsing the index_col_name parameters list
		*        Structure is (colname(len)),
		*                  or (colname),
		*/
	}

    /**
     * checks alter command
     *
     * @param $param
     * @return string
     */
	function zen_check_alter_command($param) {
		global $db;
		if (!zen_not_null($param)) return "Empty SQL Statement";
		if (!$checkprivs = $this->zen_check_database_privs('ALTER')) return sprintf(REASON_NO_PRIVILEGES,'ALTER');
		switch (strtoupper($param[3])) {
			case ("ADD"):
				if (strtoupper($param[4]) == 'INDEX') {
					// check that the index to be added doesn't already exist
					$index = $param[5];
					$sql = "show index from " . DB_PREFIX . $param[2];
					$result = $db->Execute($sql);
					while (!$result->EOF) {
						if (ZC_UPG_DEBUG3==true) echo 'KEY: '.$result->fields['Key_name'].'<br />';
						if  ($result->fields['Key_name'] == $index) {
							return sprintf(REASON_INDEX_ALREADY_EXISTS,$index,$param[2]);
						}
						$result->MoveNext();
					}
				} elseif (strtoupper($param[4])=='PRIMARY') {
					// check that the primary key to be added doesn't exist
					if ($param[5] != 'KEY') return;
					$sql = "show index from " . DB_PREFIX . $param[2];
					$result = $db->Execute($sql);
					while (!$result->EOF) {
						if (ZC_UPG_DEBUG3==true) echo $result->fields['Key_name'].'<br />';
						if  ($result->fields['Key_name'] == 'PRIMARY') {
							return sprintf(REASON_PRIMARY_KEY_ALREADY_EXISTS,$param[2]);
						}
						$result->MoveNext();
					}

				} elseif (!in_array(strtoupper($param[4]),array('CONSTRAINT','UNIQUE','PRIMARY','FULLTEXT','FOREIGN','SPATIAL') ) ) {
					// check that the column to be added does not exist
					$colname = ($param[4]=='COLUMN') ? $param[5] : $param[4];
					$sql = "show fields from " . DB_PREFIX . $param[2];
					$result = $db->Execute($sql);
					while (!$result->EOF) {
						if (ZC_UPG_DEBUG3==true) echo $result->fields['Field'].'<br />';
						if  ($result->fields['Field'] == $colname) {
							return sprintf(REASON_COLUMN_ALREADY_EXISTS,$colname);
						}
						$result->MoveNext();
					}

				} elseif (strtoupper($param[5])=='AFTER') {
					// check that the requested "after" field actually exists
					$colname = ($param[6]=='COLUMN') ? $param[7] : $param[6];
					$sql = "show fields from " . DB_PREFIX . $param[2];
					$result = $db->Execute($sql);
					while (!$result->EOF) {
						if (ZC_UPG_DEBUG3==true) echo $result->fields['Field'].'<br />';
						if  ($result->fields['Field'] == $colname) {
							return; // exists, so return with no error
						}
						$result->MoveNext();
					}

				} elseif (strtoupper($param[6])=='AFTER') {
					// check that the requested "after" field actually exists
					$colname = ($param[7]=='COLUMN') ? $param[8] : $param[7];
					$sql = "show fields from " . DB_PREFIX . $param[2];
					$result = $db->Execute($sql);
					while (!$result->EOF) {
						if (ZC_UPG_DEBUG3==true) echo $result->fields['Field'].'<br />';
						if  ($result->fields['Field'] == $colname) {
							return; // exists, so return with no error
						}
						$result->MoveNext();
					}
					/*
					* @TODO -- add check for FIRST parameter, to check that the FIRST colname specified actually exists
					*/
				}
				break;
			case ("DROP"):
				if (strtoupper($param[4]) == 'INDEX') {
					// check that the index to be dropped exists
					$index = $param[5];
					$sql = "show index from " . DB_PREFIX . $param[2];
					$result = $db->Execute($sql);
					while (!$result->EOF) {
						if (ZC_UPG_DEBUG3==true) echo $result->fields['Key_name'].'<br />';
						if  ($result->fields['Key_name'] == $index) {
							return; // exists, so return with no error
						}
						$result->MoveNext();
					}
					// if we get here, then the index didn't exist
					return sprintf(REASON_INDEX_DOESNT_EXIST_TO_DROP,$index,$param[2]);

				} elseif (strtoupper($param[4])=='PRIMARY') {
					// check that the primary key to be dropped exists
					if ($param[5] != 'KEY') return;
					$sql = "show index from " . DB_PREFIX . $param[2];
					$result = $db->Execute($sql);
					while (!$result->EOF) {
						if (ZC_UPG_DEBUG3==true) echo $result->fields['Key_name'].'<br />';
						if  ($result->fields['Key_name'] == 'PRIMARY') {
							return; // exists, so return with no error
						}
						$result->MoveNext();
					}
					// if we get here, then the primary key didn't exist
					return sprintf(REASON_PRIMARY_KEY_DOESNT_EXIST_TO_DROP,$param[2]);

				} elseif (!in_array(strtoupper($param[4]),array('CONSTRAINT','UNIQUE','PRIMARY','FULLTEXT','FOREIGN','SPATIAL'))) {
					// check that the column to be dropped exists
					$colname = ($param[4]=='COLUMN') ? $param[5] : $param[4];
					$sql = "show fields from " . DB_PREFIX . $param[2];
					$result = $db->Execute($sql);
					while (!$result->EOF) {
						if (ZC_UPG_DEBUG3==true) echo $result->fields['Field'].'<br />';
						if  ($result->fields['Field'] == $colname) {
							return; // exists, so return with no error
						}
						$result->MoveNext();
					}
					// if we get here, then the column didn't exist
					return sprintf(REASON_COLUMN_DOESNT_EXIST_TO_DROP,$colname);
				}//endif 'DROP'
				break;
			case ("ALTER"):
			case ("MODIFY"):
			case ("CHANGE"):
				// just check that the column to be changed 'exists'
				$colname = ($param[4]=='COLUMN') ? $param[5] : $param[4];
				$sql = "show fields from " . DB_PREFIX . $param[2];
				$result = $db->Execute($sql);
				while (!$result->EOF) {
					if (ZC_UPG_DEBUG3==true) echo $result->fields['Field'].'<br />';
					if  ($result->fields['Field'] == $colname) {
						return; // exists, so return with no error
					}
					$result->MoveNext();
				}
				// if we get here, then the column didn't exist
				return sprintf(REASON_COLUMN_DOESNT_EXIST_TO_CHANGE,$colname);
				break;
			default:
				// if we get here, then we're processing an ALTER command other than what we're checking for, so let it be processed.
				return;
				break;
		} //end switch
	}

    /**
     * checks config key
     *
     * @param $line
     * @return string
     */
	function zen_check_config_key($line) {
		global $db;
		$values=array();
		$values=explode("'",$line);
		//INSERT INTO configuration blah blah blah VALUES ('title','key', blah blah blah);
		//[0]=INSERT INTO.....
		//[1]=title
		//[2]=,
		//[3]=key
		//[4]=blah blah
		$title = $values[1];
		$key  =  $values[3];
		$sql = "select configuration_title from " . DB_PREFIX . "configuration where configuration_key='".$key."'";
		$result = $db->Execute($sql);
		if ($result->RecordCount() >0 ) return sprintf(REASON_CONFIG_KEY_ALREADY_EXISTS,$key);
	}

    /**
     * checks product type layout key
     *
     * @param $line
     * @return string
     */
	function zen_check_product_type_layout_key($line) {
		global $db;
		$values=array();
		$values=explode("'",$line);
		$title = $values[1];
		$key  =  $values[3];
		$sql = "select configuration_title from " . DB_PREFIX . "product_type_layout where configuration_key='".$key."'";
		$result = $db->Execute($sql);
		if ($result->RecordCount() >0 ) return sprintf(REASON_PRODUCT_TYPE_LAYOUT_KEY_ALREADY_EXISTS,$key);
	}

    /**
     * writes to upgrade excetions table
     *
     * @param $line
     * @param $reason
     * @param $sql_file
     * @return mixed
     */
	function zen_write_to_upgrade_exceptions_table($line, $reason, $sql_file) {
		global $db;
		$this->zen_create_exceptions_table();
		$sql="INSERT INTO " . TABLE_UPGRADE_EXCEPTIONS . " VALUES (0,'". $sql_file."','".$reason."', now(), '".addslashes($line)."')";
		if (ZC_UPG_DEBUG3==true) echo '<br />sql='.$sql.'<br />';
		$result = $db->Execute($sql);
		return $result;
	}

    /**
     * empties exceptions table
     *
     * @return mixed
     */
	function zen_purge_exceptions_table() {
		global $db;
		$this->zen_create_exceptions_table();
		$result = $db->Execute("TRUNCATE TABLE " . TABLE_UPGRADE_EXCEPTIONS );
		return $result;
	}

    /**
     * creates exceptions table
     *
     * @return mixed
     */
	function zen_create_exceptions_table() {
		global $db;
		if (!$this->zen_table_exists(TABLE_UPGRADE_EXCEPTIONS, false)) {
			$result = $db->Execute("CREATE TABLE " . TABLE_UPGRADE_EXCEPTIONS ." (
            upgrade_exception_id smallint(5) NOT NULL auto_increment,
            sql_file varchar(50) default NULL,
            reason varchar(200) default NULL,
            errordate datetime default '0001-01-01 00:00:00',
            sqlstatement text, PRIMARY KEY  (upgrade_exception_id)
          ) ENGINE=MyISAM   ");
			return $result;
		}
	}

    /**
     * gets the column list
     *
     * @param $table
     * @return array
     */
    public function getColumns($table){
        global $db;
        $fields = $db->Execute("SHOW fields FROM " . TABLE_PRODUCTS_DESCRIPTION);
        $columns = array();

        while(!$fields->EOF){
            $columns[] = $fields->fields['Field'];
            $fields->MoveNext();
        }

        return $columns;
    }
}