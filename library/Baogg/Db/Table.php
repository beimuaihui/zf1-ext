<?php
/**
 * beimuaihui System
 * Copyright(c) 2011-2020 beimuaihui.
 * @license    http://www.gnu.org/licenses/gpl.html     This software Under GPL V3 License 
 * beimuaihui@gmail.com
 * http://code.google.com/p/beimuaihui/
 * $Id: Table.php 457 2012-01-12 07:44:47Z beimuaihui@gmail.com $
 */
/**
 * @author 
 * @version
 */

Zend_Loader::loadClass('Zend_Db_Table_Abstract');

//class Permission extends Zend_Db_Table_Abstract {
class Baogg_Db_Table  extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name;
	protected $_db ;
	protected $_primary; 
	
	function __construct($db_key='master',$table_name='',$pk='') {	   
	    $this->_db=Baogg_Db::getDb($db_key);
        $this->db_prefix=Baogg_Db::getTablePrefix($db_key);
        if($table_name){
        	$this->_name=$this->db_prefix && strpos($table_name,$this->db_prefix)===0?$table_name:$this->db_prefix.$table_name;	
        }else{
        	$this->_name=$this->db_prefix && strpos($this->_name,$this->db_prefix)===0?$this->_name:$this->db_prefix.$this->_name;
        }
        /*
        $db= new Zend_Db_Adapter_Pdo_Mysql(array(
            'host'     => 'localhost',
            'username' => 'root',
            'password' => '',
            'dbname'   => 'baogg',
        	'driver_options' => array(
        			PDO::MYSQL_ATTR_INIT_COMMAND => 'set names utf8'
        		)
        ));
        */
        
        //just for debug
        //$profiler = new Zend_Db_Profiler_Firebug('All DB Queries');
        //$profiler->setEnabled(true);
        //$this->_db->setProfiler($profiler); 
        
        
        /*$writer = new Zend_Log_Writer_Firebug();
        $logger = new Zend_Log($writer);
        //global $logger;$logger->log('This is a log message!', Zend_Log::INFO);
        */
       

		//$this->_db=  Zend_Registry::get("db");

        if($pk){
        	$this->myPrimary=$this->_primary=$pk;		
        }else{
			$this->myPrimary=$this->_primary;	
		}

		// First, set up the Cache
		$frontendOptions = array(
		    'automatic_serialization' => true
		    );
		$dir_cache =  BAOGG_UPLOAD_DIR.'cache/';
		is_dir($dir_cache) or Baogg_File::mkdir($dir_cache);
		$backendOptions  = array(
		    'cache_dir'                => $dir_cache
		    );
		 
		$cache = Zend_Cache::factory('Core',
		                             'File',
		                             $frontendOptions,
		                             $backendOptions);

		parent::__construct(array('metadataCache' => $cache));
	}

	public function changeTableName($new_name){
		$this->setOptions(array(self::NAME=>$new_name));
		self::__construct();
	}
	
	/*
	 *   get  data list
	 */
	function getList($where=array(),$order=array(),$limit=array(),$cols="*",$joins=array(),$group=array(),$having=array(),$is_distinct=false) {
	    
		$select=$this->_db->select();	
		if($is_distinct){
			$select->distinct();
		}	
		$select->from(array($this->getAlias()=>$this->_name),$cols);
		/*if($this->getAlias() == 'bom_part_type'){
			echo __FILE__.__LINE__.'<pre>';print_r($cols);exit;
		}*/
		foreach((array)$joins as $v){
			//echo '<pre>';print_r($v);
			if(!is_array($v['name'])){
				$v['name']=array($v['name']=>$this->db_prefix.$v['name']);
			}else{
				foreach($v['name'] as $alias=>$model){
					$v['name'][$alias]=$this->db_prefix.$model;
				}
			}
			
			//var_dump($v['type']);var_dump(isset($v['type']));exit;
			$join_type='join'.(isset($v['type'])?ucwords($v['type']):'');
			$select->$join_type($v['name'],$v['condition'],@$v['cols'],@$v['schema']);
		}

		$cols=$this->info(self::COLS);

		$arr_meta = $this->info();
		
		
		$arr_where_or=array();
		
	    foreach((array)$where as $k=>$v){   	
	    	
	    	if(is_int($k)){
	    		//if $v is not ""
	    		if($v){
	    			$select->where($v);
	    		}
	    		continue;
	    	}
	    	
	    	$k=trim($k);	    	
	    	$arr_k=explode(" ",$k);	    	
	    	if(!in_array($arr_k[0],$cols) && $this->is_word($arr_k[0])){
	    		continue;
	    	}	   
	    	
	    	 	
	    	if(!$this->is_word($k)){
	    		$select->where($this->getAlias().'.'.$k.$v);
	    	}else if(is_int($v) || is_float($v)){
	           $select->where("{$this->getAlias()}.$k =  ?",$v ) ; 
	        }else if(is_array($v)){
	           $select->where("{$this->getAlias()}.$k  IN (?)",$v ) ; 
	        }else{
	        	/*if($k == 'user_group_id'){
	        		echo __FILE__.__LINE__.'<pre>';
	        		print_r($arr_meta);
	        		var_dump(strtolower($arr_meta['metadata'][$k]['DATA_TYPE']));
	        		exit;
	        	}*/
	           if($v==="%%"){
	           		continue;
	           }else if($this->is_query_value(trim($v,'%'))){
	           		$arr_where_or[]=$this->_db->quoteInto("{$this->getAlias()}.$k like ?",'%'.$this->get_query_value(trim($v,'%')).'%');
	           }else{
	           		if(isset($arr_meta['metadata'][$k]) && in_array(strtolower($arr_meta['metadata'][$k]['DATA_TYPE']),array('int','tinyint','bigint'))){
	           			$v = trim($v,'%');
	           		}
	           		$select->where("{$this->getAlias()}.$k like  ?",$v ) ;
	           }
	        }
	    }
	    
		if($arr_where_or){
	    	$select->where('('.implode(" or ",$arr_where_or).')');
	    }
	    
	    if($order){
	    	$_arr_sort=array();
	    	foreach((array)$order as $k=>$v){
		    	if(is_int($k)){
		    		//$select->order($order);
		    		$_arr_sort[]=$v;
		    	}else if($k=='sort'){
		    		if(($json_order=json_decode($v,true))!==null){		    			
		    			foreach((array)$json_order as $k=>$v){
		    				$v['property']=$this->filterColumn($v['property']);
		    				$_arr_sort[]="{$v['property']} {$v['direction']}";
		    			}		    			
		    		}else{
				    	$sort=$this->filterColumn($order['sort']);
				    	if($sort){
					    	$dir=isset($order['dir']) && in_array(strtolower($order['dir']),array('asc','desc'))?$order['dir']:'asc';
					        $_arr_sort[]="$sort $dir" ;
				    	}
		    		}
		    	}else if($k=='dir'){
		    		
		    	}else{
		    		//just for 'array(col=>dir)'
		    		$sort=$this->filterColumn($k);
			    	if($sort){
				    	$dir= in_array(strtolower($v),array('asc','desc'))?$v:'asc';
				        $_arr_sort[]="$sort $dir" ;
			    	}
		    	}
	    	}
	    	$select->order($_arr_sort) ;
	    }
		if($limit){			
		    $select->limit($limit['limit'],(int)@$limit['start']) ;
		}
		if($group){
			$select->group($group);
		}
		if($having){
			$select->having($having);
		}
		try{ 	  
			$rs=$this->_db->fetchAll($select);
		}catch(Exception $e){
			
			echo '<br /><pre>'.__FILE__.__LINE__;
			echo '<br />'.$select;
			echo '<br />'.$e->getMessage();
			echo '<br />';
			print_r((array)$where );
			debug_print_backtrace();
			exit;
		}
		/* if($group){
			
		} */
	  // $rs[0]['sub_table']='gg';
	   
	   return $rs;	
	}
	
	/**
	 * 
	 * get list with tree style
	 * @param array $where
	 * @param array("sort"=>"","dir"=>"") $order
	 * @param array $limit
	 * @param cols array $cols
	 * @param array $joins
	 */
	function getListTree($where=array(),$order=array(),$limit=array(),$cols="*",$joins=array()){
		//$rs=$this->getList($where,$order,$limit,$cols,$joins);

		$rs=$this->getArray($where,$order,$limit,$cols,$joins);

		/*if($this->myPrimary=="position_id"){
            echo __FILE__.__LINE__.'<pre>';print_r($rs);print_r($where);print_r($cols);exit;
        }*/

		if(!$rs){
			return array();
		}
		$columns=@array_keys((array)current($rs));
		foreach((array)$rs as $k=>$v){
			if(isset($columns[2]) && !isset($rs[$v[$columns[2]]])){
				$rs[$k][$columns[2]]=0;
			}
		}
		$rs=array_values($rs);




		//echo '<br /><pre>'.__FILE__.__LINE__;print_r($rs);

		$dbTree=new Baogg_Db_Tree();
		$tr=$dbTree->rs2GridTree($rs);
		$rs=$dbTree->tr2GridStore($tr);

		//print_r($rs);exit;

		return $rs;
	}
	function getArray($where=array(),$order=array(),$limit=array(),$cols="*") {
		$rs=$this->getList($where,$order,$limit,$cols);
		$arr=array();
		foreach((array)$rs as $k=>$v){
			//except value field is not primary key,
			$key_col=$this->myPrimary;
			if(!array_key_exists($this->myPrimary, $v)){
				$arr_cols=array_keys($v);
				$key_col=$arr_cols[0];
			}
			$arr[$v[$key_col]]=$v;
		}
		return $arr;
	}
	function getCombo($where=array(),$order=array(),$limit=array(),$cols="*",$joins=array()) {
		$rs=$this->getList($where,$order,$limit,$cols,$joins);
		$arr=array();
		foreach((array)$rs as $k=>$v){
			if(!isset($arr_key)){
				$arr_key=array_keys($v);				
			}
			$arr[]=array($v[$arr_key[0]],$v[$arr_key[1]]);
		}
		return $arr;
	}
	
	function getTree($where=array(),$order=array(),$limit=array(),$cols="*") {
		$rs=$this->getArray($where,$order,$limit,$cols);
		
		
		foreach((array)$rs as $k=>$v){
			if(!isset($pid_column)){
				$arr_key=array_keys($v);
				$pid_column=$arr_key[2];
			}			
			if(!isset($rs[$v[$pid_column]])){
				$rs[$k][$pid_column]=0;
			}
		}
		$Tree=new Baogg_Db_Tree();
		$tr=$Tree->rs2GridTree(array_values($rs));
		$tr=$Tree->tr2GridStore($tr);
		return $tr;
	}
	function getExtTree($where=array(),$order=array(),$limit=array(),$cols="*",$joins=array(),$rs_op=array(),$arr_selected=array()) {
		$rs_tmp=$this->getList($where,$order,$limit,$cols,$joins);
		//echo '<pre>';print_r($rs_op);print_r($where);print_r($order);print_r($limit);print_r($cols);print_r($joins);		print_r($arr_selected);exit;
		//echo __FILE__.__LINE__.'<pre>';print_r($rs_tmp); print_r($rs_op);exit;
		$rs=array();
	  	foreach($rs_tmp as $k=>$v){	  		
	  		!empty($v['pid']) or $v['pid']=0;
	  		if(in_array($v['id'],(array)$arr_selected)){
	  			$v['checked']=true;
	  		}
	  		if(!isset($v['checked'])){
	  			$v['checked']=false;
	  		}
	  		//echo '<pre>';print_r($v);exit;
	  		$rs[$v['id']]=$v;
	  		foreach((array)$rs_op as $row_op){
	  			if(isset($row_op['id']) &&  in_array($row_op['id'],(array)$arr_selected)){
	  				$row_op['checked']=true;
	  			}
	  			$row_op['id']= isset($row_op['id'])?$row_op['id']: $v['id'].'_'.$row_op['id2'];
	  			$row_op['pid']=isset($row_op['pid'])?$row_op['pid']:$v['id'];
	  			$rs[$row_op['id']]=$row_op;
	  		}
	  	}
	  	
		foreach((array)$rs as $k=>$v){
			if(!isset($pid_column)){				
				$arr_key=array_keys($v);
				$pid_column=count($arr_key)>=3?$arr_key[2]:'pid';
			}
			//echo $pid_column;exit;
			if(!isset($v[$pid_column]) || !isset($rs[$v[$pid_column]])){
				$rs[$k][$pid_column]=0;
			}
		}
		
		$Tree=new Baogg_Db_Tree();
		$tr=$Tree->rs2GridTree(array_values($rs));
		$tr=$Tree->tr2ExtTreeStore($tr);
		//echo '<pre>';var_dump($tr);exit;
		return $tr;
		
	}
	function getComboTree($where=array(),$order=array(),$limit=array(),$cols="*") {
		$rs=$this->getArray($where,$order,$limit,$cols);
		
		
		foreach((array)$rs as $k=>$v){
			if(!isset($pid_column)){
				$arr_key=array_keys($v);
				$pid_column=$arr_key[2];
			}			
			if(!isset($rs[$v[$pid_column]])){
				$rs[$k][$pid_column]=0;
			}
		}
		$Tree=new Baogg_Db_Tree();
		$tr=$Tree->rs2ComboTree(array_values($rs));
		$tr=$Tree->tr2ComboStore($tr);
		return $tr;
	}
	/*
	 * Get All by state
	 */	
	function getAllByState() {
		$sql = 'SELECT * FROM ' . $this->_name . ' WHERE state = 1 ';
		$rs = $this->_db->query($sql)->fetchAll();
		return $rs; 	
	}

	/*
	 * Get language list unless a language . defaut is unless english
	 */
	function getAllUnlessLang($id=1) {
		$sql = "SELECT * FROM {$this->_name} WHERE state = 1  and {$this->myPrimary}<>{$id}";
		//echo $sql;
		//exit();
		$rs = $this->_db->query($sql)->fetchAll();
		return $rs; 	
	}
	
	function _update($arr, $id='') {

		$rows_affected  = 0;

		//echo __FILE__.__LINE__.'<pre>';		print_r($arr);
		//filter col
		$arr=$this->filterForm($arr);		
    		$id= ($id===''?$arr[$this->myPrimary]:$id);
    		
		$where = is_array($id)? $this->_db->quoteInto("{$this->myPrimary} in (?)", $id): $this->_db->quoteInto("{$this->myPrimary} = ?", $id);
		$rs = $this->getList($where);
		//filter col val
		foreach((array)$rs as $v){
			//need to update data
			$arr_update  = array();
			foreach($arr as  $col=>$val){
				if($val  != $v[$col]){
					$arr_update[$col] = $val;
				}
			}
			if($arr_update){
				try{
					$rows_affected = $this->_db->update($this->_name, $arr_update,  $this->_db->quoteInto("{$this->myPrimary} = ?", $v[$this->myPrimary]));
					if($rows_affected){
						$ModelHistory = new ModelHistory();
						$ModelHistory->create($this,__METHOD__,$arr_update);
					}
				}catch(Exception $e){
					echo __FILE__.__LINE__.'<pre>'.$e->getMessage();
					debug_print_backtrace();
					var_dump( $this->myPrimary);
					var_dump( $this->_name);
					var_dump($arr);
					var_dump($where);
					exit;
				}
			}
		}


		//echo  __FILE__.__LINE__.'<pre>';var_dump($this->_name);		print_r($arr);		echo $where;print_r($id);		exit;
		/*try{
			$rows_affected = $this->_db->update($this->_name, $arr, $where);

			//echo  __FILE__.__LINE__.'<pre>';var_dump($rows_affected);exit;
			if($rows_affected){
				$ModelHistory = new ModelHistory();
				foreach((array)$id as $sub_id){
					$rs = $this->getOne($sub_id);
					if($rs){
						$ModelHistory->create($this,__METHOD__,$rs[0] );
					}
				}
			}
			
			
			
		}catch(Exception $e){
			echo __FILE__.__LINE__.'<pre>'.$e->getMessage();
			debug_print_backtrace();
			var_dump( $this->myPrimary);
			var_dump( $this->_name);
			var_dump($arr);
			var_dump($where);
			exit;
		}*/
		
		
		return $rows_affected;
	}
	
	function _insert($arr) {
		unset($arr[$this->myPrimary]);
		$arr=$this->filterForm($arr);
		
		$rows_affected = $this->_db->insert($this->_name, $arr);
		$last_insert_id = $this->_db->lastInsertId();

		$rs = $this->getOne($last_insert_id);
		if($rs){
			$ModelHistory = new ModelHistory();
			$ModelHistory->create($this,__METHOD__,$rs[0] );
		}
		
	
		return $last_insert_id;
	} 

	function _delete($id)
	{	

		$ModelHistory = new ModelHistory();
		foreach((array)$id as $sub_id){
			$rs = $this->getOne($sub_id);
			if($rs){
				$ModelHistory->create($this,__METHOD__,$rs[0] );
			}
		}

	    	$arr_id=is_array($id)?$id:explode(",",$id);
		$where = $this->_db->quoteInto("{$this->myPrimary} in (?)", $arr_id);
		//echo $where;
		$rows_affected = $this->_db->delete($this->_name, $where);
		return $rows_affected;
	}
	

	function getOne($id) {		
	    
		$sql = $this->_db->quoteInto("SELECT * FROM {$this->_name} WHERE {$this->myPrimary} = ? ", (int)$id);
		$rs = $this->_db->query($sql)->fetchAll();
		return $rs; 	
	
	}
	

	/**
	 * return primary ids
	 * @param array,which build where $arr
	 */
	function getSame($arr) {	
		$ret=array();
		
		$op=" or ";
		if(!$arr){
			$arr['0']=1;
		}
		$where = array();
		foreach((array)$arr as $k=>$v)
		{
			$where[]=$this->_db->quoteInto(" $k = ?",$v);
		}
		$where=implode($op,$where);
	    $sql = "SELECT {$this->myPrimary} FROM {$this->_name} WHERE $where ";
		//echo $sql;exit();
		$rs = $this->_db->query($sql)->fetchAll();
		foreach((array)$rs as $v)
		{
		    $ret[]=$v[$this->myPrimary];
		}
		return $ret; 	
	
	}
	function getSame2($arr,$op=" or "){
		$ret=array();
		
		if(!$arr){
			$arr['0']=1;
		}

		$where = array();
		foreach((array)$arr as $k=>$v)
		{
			$where[]=$this->_db->quoteInto(" $k = ?",$v);
		}
		$where=implode($op,$where);
	    $sql = "SELECT {$this->myPrimary} FROM {$this->_name} WHERE $where ";
		
		$rs = $this->_db->query($sql)->fetchAll();
		//echo '<pre>';print_r($sql);exit;
		foreach((array)$rs as $v)
		{
		    $ret[]=$v[$this->myPrimary];
		}
		
		return $ret; 	
	}
	
    function filterForm($form=array()){
        $arr_meta = $this->info();
        $cols = $arr_meta['cols'];



        //echo __FILE__.__LINE__.'<pre>';print_r($cols);print_r($form);
        //var_dump($cols);
        foreach((array)$form as $k=>$v){
            if(!in_array($k,$cols)){
                unset($form[$k]);
                continue;
            }
            // || is_null($v) || $v=== '' empty($v)
            if( is_null($v) || $v=== ''){
            	$form[$k] = $arr_meta['metadata'][$k]['DEFAULT'];
            }

         /*   if($v === BAOGG_FIELD_NULL){
            	$form[$k] = NULL;
            }*/
            //added larter,change array value to string
            if(is_array($v)){
            	$form[$k]=implode(',',$v);
            }

        }
        //echo __FILE__.__LINE__.'<pre>';print_r($cols);print_r($form);exit;
        return $form;
        
    }
    
    function filterColumn($k)
    {
    	return trim(trim($k),"0123456789");
    }
    
	function is_word($str){
	    return preg_match("/^[a-zA-Z0-9_]*$/" , $str);
	}
	function is_query_value($v=''){
		return strpos($v,"#")===0 && strrpos($v,"#")===strlen($v)-1;
	}
	function get_query_value($v=''){
		return substr($v,1,-1);
	}
	function getName(){
		return $this->_name;
	}
	
	function getAlias($name=null){
		if($name===null){
			return str_replace($this->db_prefix, '', $this->_name);
		}else{
			return str_replace($this->db_prefix, '', $name);
		}
		return '';
	}
	
	public function getColCombo($where=array(),$order=array(),$limit=array(),$cols="*",$joins=array(),$group=array(),$having=array(),$is_distinct=true){
		$rs=$this->getList($where,$order,$limit,$cols,$joins,$group,$having,$is_distinct);
		$arr=array();
		foreach((array)$rs as $k=>$v){
			if(!isset($arr_key)){
				$arr_key=array_keys($v);				
			}
			$id_col=$arr_key[0];
			$text_col=isset($arr_key[1])?$arr_key[1]:$arr_key[0];
			
			$arr[]=array($v[$id_col],$v[$text_col]);
		}
		return $arr;
	}
	public function addComboAll($arr){
		array_unshift($arr,array('',Baogg_Language::get('please_select')));
		return $arr;
	}

	public static function getSubTableId($content_id=0){
        return (int)($content_id/500000);
    }



    /**
     * Quote values and place them into a piece of text with placeholders
     *
     * The placeholder is a question-mark; all placeholders will be replaced
     * with the quoted value.
     *
     * Accepts unlimited number of parameters, one for every question mark.
     *
     * @param string $text Text containing replacements
     * @return string
     */
    public function quoteInto($text)
    {
        // get function arguments
        $args = func_get_args();
 
        // remove $text from the array
        array_shift($args);
 
        // check if the first parameter is an array and loop through that instead
       /* if (isset($args[0]) && is_array($args[0])) {
            $args = $args[0];
        }*/
 
        // replace each question mark with the respective value
        foreach ($args as $arg) {
            $text = preg_replace('/\?{1}/', $this->_db->quote($arg), $text, 1);
        }
 
        // return processed text
        return $text;
    }
}