<?php
/**
 * beimuaihui System
 * Copyright(c) 2011-2020 beimuaihui.
 * @license    http://www.gnu.org/licenses/gpl.html     This software Under GPL V3 License 
 * beimuaihui@gmail.com
 * http://code.google.com/p/beimuaihui/
 * $Id: Db.php 438 2011-12-20 13:04:07Z beimuaihui@gmail.com $
 */
class Baogg_Db {
	/* protected static $_masterDb;
	protected static $_slaveDb;
	protected static $_qaDb; */
	protected static $arr_db=array();
	protected static $key_map=array('master'=>'MDB','slaver'=>'SDB','qa'=>'QADB');
	//this can't be construct outside,single pattern
	protected function __construct() {
	
	}
	
	/* public static function getMasterDb() {
		if (! isset ( self::$_masterDb )) {			
			Zend_Loader::loadClass ( "Zend_Db" );
            Zend_Loader::loadClass ( "Zend_Db_Table" );
            //Zend_Loader::loadClass ( "Zend_Db_Profiler_Firebug" );			
           

			self::$_masterDb = Zend_Db::factory ( BAOGG_MDB_DRIVER, 
				array('host' => BAOGG_MDB_HOST,
					'username' => BAOGG_MDB_USER,
					'password' => BAOGG_MDB_PWD,
					'dbname' => BAOGG_MDB_NAME,
					'port' => BAOGG_MDB_PORT,
					'profiler' => BAOGG_MDB_PROFILE,
				    'driver_options'=>array(BAOGG_MDB_options=>"set names ".BAOGG_MDB_CHARSET)) );  //
			Zend_Db_Table::setDefaultAdapter ( self::$_masterDb );
			
			//$profiler = new Zend_Db_Profiler_Firebug('All DB Queries');
           // $profiler->setEnabled(BAOGG_MDB_PROFILE);
			//self::$_masterDb->setProfiler($profiler);
			
			//$sql=file_get_contents(BAOGG_UPLOAD_DIR."baogg.sql");
			if(false){
				//echo $sql;exit;
				//self::$_masterDb->query("set names ".BAOGG_MDB_CHARSET);
				//self::$_masterDb->query($sql);
			}
		}
		//self::$_masterDb->query("set names ".BAOGG_MDB_CHARSET);
		
		return self::$_masterDb;
	}
	public static function getSlaveDb() {
		if (! isset ( self::$_slaveDb )) {
			Zend_Loader::loadClass ( "Zend_Db" );
			Zend_Loader::loadClass ( "Zend_Db_Table" );
			//Zend_Loader::loadClass ( "Zend_Db_Profiler_Firebug" );
			
			
			self::$_slaveDb = Zend_Db::factory ( BAOGG_SDB_DRIVER, array(
					'host' => BAOGG_SDB_HOST,
					'username' => BAOGG_SDB_USER,
					'password' => BAOGG_SDB_PWD,
					'dbname' => BAOGG_SDB_NAME,
					'port' => BAOGG_SDB_PORT,
					'profiler' => BAOGG_SDB_PROFILE,
					'driver_options'=>array(BAOGG_SDB_options=>"set names ".BAOGG_SDB_CHARSET)));  //
			Zend_Db_Table::setDefaultAdapter ( self::$_slaveDb );
			
			//$profiler = new Zend_Db_Profiler_Firebug('All DB Queries');
            //$profiler->setEnabled(BAOGG_SDB_PROFILE);
            //self::$_slaveDb->setProfiler($profiler);
            
		}
		//self::$_slaveDb->query("set names ".BAOGG_SDB_CHARSET);
		return self::$_slaveDb;
	}
	
	public static function getQaDb() {
		if (! isset ( self::$_qaDb )) {
			Zend_Loader::loadClass ( "Zend_Db" );
			Zend_Loader::loadClass ( "Zend_Db_Table" );
			//Zend_Loader::loadClass ( "Zend_Db_Profiler_Firebug" );
				
				
			self::$_slaveDb = Zend_Db::factory ( BAOGG_QADB_DRIVER, array(
						'host' => BAOGG_QADB_HOST,
						'username' => BAOGG_QADB_USER,
						'password' => BAOGG_QADB_PWD,
						'dbname' => BAOGG_QADB_NAME,
						'port' => BAOGG_QADB_PORT,
						'profiler' => BAOGG_QADB_PROFILE,
						'driver_options'=>array(BAOGG_QADB_options=>"set names ".BAOGG_QADB_CHARSET)));  //
			Zend_Db_Table::setDefaultAdapter ( self::$_qaDb);
				
			//$profiler = new Zend_Db_Profiler_Firebug('All DB Queries');
			//$profiler->setEnabled(BAOGG_QADB_PROFILE);
			//self::$_slaveDb->setProfiler($profiler);
	
		}
		//self::$_slaveDb->query("set names ".BAOGG_QADB_CHARSET);
		return self::$_qaDb;
	} */
	
	public static function getDb($key){
		$key=isset(self::$key_map[$key])?self::$key_map[$key]:strtoupper($key);
		if (! array_key_exists ( $key,self::$arr_db )) {
			Zend_Loader::loadClass ( "Zend_Db" );
			Zend_Loader::loadClass ( "Zend_Db_Table" );
			//Zend_Loader::loadClass ( "Zend_Db_Profiler_Firebug" );
		
		
			self::$arr_db[$key] = Zend_Db::factory ( constant("BAOGG_{$key}_DRIVER"), array(
								'host' => constant("BAOGG_{$key}_HOST"),
								'username' => constant("BAOGG_{$key}_USER"),
								'password' => constant("BAOGG_{$key}_PWD"),
								'dbname' => constant("BAOGG_{$key}_NAME"),
								'port' => constant("BAOGG_{$key}_PORT"),
								'profiler' => constant("BAOGG_{$key}_PROFILE"),
								'driver_options'=>array(constant("BAOGG_{$key}_options")=>"set names ".constant("BAOGG_{$key}_CHARSET"))));  //
			//Zend_Db_Table::setDefaultAdapter ( self::$arr_db[$key]);
			if(BAOGG_DEBUG){
				$profiler = new Zend_Db_Profiler_Firebug('All DB Queries');
				$profiler->setEnabled(constant("BAOGG_{$key}_PROFILE"));
				self::$arr_db[$key]->setProfiler($profiler);
			}
			
		
		}
		//self::$_slaveDb->query("set names ".BAOGG_{$key}_CHARSET);
		return self::$arr_db[$key];
		
		
	}
	public static function getTablePrefix($key){		
		$key=isset(self::$key_map[$key])?self::$key_map[$key]:strtoupper($key);
		return constant("BAOGG_{$key}_PREFIX");		
	}
	public function __clone() {
		trigger_error ( 'Clone is not allowed.', E_USER_ERROR );
	}
	/**
	 * @author :bob
	 * @abstract : get the sql ,use as echo debugDb();
	 *
	 * @return the sql string.
	 */
	public  static function debugDb( $db,$type = '') {
		if ($type == 'log') {
			$spacer = "";
		} else {
			$ret = "<pre>";
			$spacer = "<br />-- -------------------------------------------------------<br />";
		}
		if (! $db) {
			return "No Database Connect!";
		}
		
		$ret .= $spacer;

		

		foreach ( ( array ) $db->getProfiler ()->getQueryProfiles ( Zend_Db_Profiler::SELECT | Zend_Db_Profiler::INSERT | Zend_Db_Profiler::UPDATE | Zend_Db_Profiler::DELETE | Zend_Db_Profiler::QUERY, true ) as $query ) {
			if(!$query){
				continue;
			}
			

			if ($query->getQueryType () == Zend_Db_Profiler::INSERT || $query->getQueryType () == Zend_Db_Profiler::UPDATE) {
				$sql = str_replace ( "?", "'%s'", $query->getQuery () );
				
				$params = array_map ( "mysql_escape_string", $query->getQueryParams () );
				
				$ret .= $params ? (vsprintf ( $sql, $params )) : $sql;
				$ret .= ";" . $spacer;
			} else {
				$ret .= $query->getQuery () . ";" . $spacer;
			}
		}

		return $ret;
	}
	public static function filterColumn(& $tbl,$post,& $db){
		$arrColumn=$db->describeTable($tbl);
		//echo "<pre>";print_r($arrColumn);
		foreach((array)$post as $k=>$v){
			if(!array_key_exists($k,$arrColumn)){
				unset($post[$k]);
			}
		}
		return $post;
	}
	
	
	public static function syncTable($table,$master_field, $slaver_field=NULL) {
		$ret=array();
		
		if (! $master_field || ! is_array ( $master_field )) {
	
			return array();
		}
		
		if (! $slaver_field || ! is_array ( $slaver_field )) {
			//table does not exists, create
			$q = "CREATE TABLE  IF NOT EXISTS  `$table`(";
			
			foreach ( $master_field as $field ) {
				if ($field['Key'] == 'PRI')
					$primary = " PRIMARY KEY ";
				else
					$primary = '';
				
				//$primary=($field['key'] == 'PRI' ? ", ADD PRIMARY KEY (`{$field['Field']}`)" : '')	
				
			$column_def = "";
			$column_def .=($field['Null']=='NO' ? ' NOT NULL ' : ' NULL ') ;			
			$column_def .= (strlen($field['Default']) > 0 ? " default '{$field['default']}' " : '');
			$column_def .= ($field['Extra'] == 'auto_increment' ? ' auto_increment ' : '');
			$column_def .= 	$field ['Key'] == 'PRI'?' PRIMARY KEY ':'';
			$q .= "`{$field['Field']}` {$field['Type']} " .$column_def;
			
			if ($field != end ( $master_field ))
				$q .= ", ";
			}
			
			$q .= ") DEFAULT CHARSET=utf8";
			return array($q);
		}
		
		
		//table exists, check fields
		foreach ( $master_field as $f => $field ) {
			$dfield = @$slaver_field [$f];
			$ffound = isset ( $dfield );
			
			if ($ffound) {
				
				//|| $field ['Null'] != $dfield ['Null'] || $field ['Default'] != $dfield ['Default'] || $field ['Extra'] != $dfield ['Extra']
				if ($field ['Type'] != $dfield ['Type']  || $field ['Key'] != $dfield ['Key'] ) 
				{
					$column_def = "";
					if ($field ['Null'] != $dfield ['Null']) {
						$column_def .= $field ['Null'] == 'NO' ? " NOT NULL " : ' NULL ';
					}
					
					if ($field ['Default'] != $dfield ['Default'] && $field ['Default']) {
						$column_def .= " default '{$field['Default']}' ";
					}
					
					if ($field ['Extra'] != $dfield ['Extra'] && ($field['Extra']=='auto_increment' || $field['Extra']='')) {
						$column_def .= " {$field['Extra']} ";
					}
					
					if ($field ['Key'] != $dfield ['Key'] && $field ['Key'] == 'PRI') {
						$column_def .= " PRIMARY KEY ";
					}
					
					//ALTER TABLE  `mail_log` CHANGE  `to_name`  `to_name` VARCHAR( 62 ) CHARACTER SET latin1 COLLATE latin1_general_ci NULL DEFAULT NULL
					$q = "ALTER TABLE `$table` CHANGE  `{$field['Field']}` `{$field['Field']}` {$field['Type']} $column_def";
					//$db->q($q);
					$ret [] = $q;
				
				}
			} else {
				//alter table add field
				if ($field ['Key'] == 'PRI')
					$primary = " PRIMARY KEY ";
				else
					$primary = '';
				
				if ($field ['Null'] == 'NO'){
					$isNull = " NOT NULL ";
				}else{
					$isNull = ' NULL ';
				}
				
				if ($field ['Default']) {
					$default = " default '{$field['Default']}' ";
				}
				
				
					
				//ALTER TABLE  `mail_log` CHANGE  `to_name`  `to_name` VARCHAR( 62 ) CHARACTER SET latin1 COLLATE latin1_general_ci NULL DEFAULT NULL
				$q = "ALTER TABLE `$table` ADD `{$field['Field']}` {$field['Type']}  $isNull $default {$field['Extra']} $primary";//
				//$db->q($q);
				$ret [] = $q;
			}
			
		}
		return $ret;
	
	} 
	/*
	function AddTableField($table, $field, $field_before = 0) {
		$sql = "ALTER TABLE `{$table}` ADD `{$field['name']}` {$field['type']} " . ($field['null'] ? '' : 'NOT') . ' NULL' . (strlen($field['default']) > 0 ? " default '{$field['default']}'" : '') . ($field['extra'] == 'auto_increment' ? ' auto_increment' : '') . (!is_string($field_before) ? ' FIRST' : " AFTER `{$field_before}`") . ($field['key'] == 'PRI' ? ", ADD PRIMARY KEY (`{$field['name']}`)" : '');
          return mysql_query($sql, $this->dbp);
    }
   function ChangeTableField($table, $field, $new_field) {
   	$sql = "ALTER TABLE `{$table}` CHANGE `{$field}` `{$new_field['name']}` {$new_field['type']} " . ($new_field['null'] ? '' : 'NOT') . ' NULL' . (strlen($new_field['default']) > 0 ? " default '{$new_field['default']}'" : '') . ($field['extra'] == 'auto_increment' ? ' auto_increment' : '') . ($field['key'] == 'PRI' ? ", ADD PRIMARY KEY (`{$field['name']}`)" : '');
     return mysql_query($sql, $this->dbp);
  }*/
}

?>