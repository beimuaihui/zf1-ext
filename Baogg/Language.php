<?php
/**
 * beimuaihui System
 * Copyright(c) 2011-2020 beimuaihui.
 * @license    http://www.gnu.org/licenses/gpl.html     This software Under GPL V3 License 
 * beimuaihui@gmail.com
 * http://code.google.com/p/beimuaihui/
 * $Id: Language.php 339 2011-10-07 10:40:45Z beimuaihui $
 */
class Baogg_Language
{
    protected static $loaded_files=array();
    
    public static function loadFile($file){
        global $LANG;
        
         $path=BAOGG_ROOT.'config/language/'.BAOGG_LANGUAGE.'/Global.php';
        if(is_file($path) && !isset(self::$loaded_files[$path]) ){
            $_lang=include $path;
            $LANG=array_merge($LANG,$_lang);
            self::$loaded_files[$path]=1;
        }
            
        $path=BAOGG_ROOT.'config/language/'.BAOGG_LANGUAGE.'/'.BAOGG_MDB_PREFIX.$file.".php";
        if(is_file($path) &&  !isset(self::$loaded_files[$path])){
        	//var_dump($path);exit;
            $_lang=include $path;
            $LANG=array_merge($LANG,$_lang);
            self::$loaded_files[$path]=1;
        }  
    }
    
    public static function get($key){
        global $LANG;

        $key = trim($key);
        $matches = array();

        if(isset($LANG[$key])){
            return $LANG[$key];
        }else if(preg_match('/^([\-\|]+)(.+)$/', trim($key), $matches)){
                //such as ---------|-Role,tree combo
                return $matches[1].self::get($matches[2]);

        }else if(preg_match('/^(.+?)(\d+)$/', trim($key), $matches)){
              //such as role2; will change to role        	
                 return self::get($matches[1]);
        }else{
            return $key;
        }
    }
    public static function outputResult($ret)
    {
    	if(isset($ret['msg'])){
    		$ret['msg']=BAOGG_LANGUAGE::get($ret['msg']);
    	}
    	return Zend_Json::encode($ret);
    }
    //translate array("1"=>"a","2"=>"b") to array(array(1,a),array(2,b)),for form combobox store
    public static function array2store($key){
        global $LANG;
        
        $ret=array();
        
        if(is_array($key)){
             foreach((array)$key as $k=>$v){
                $ret[]=array(''.$k,$v);
            } 
        }else if( isset($LANG[$key])){
            $rs= $LANG[$key];
            foreach((array)$rs as $k=>$v){
                $ret[]=array(''.$k,$v);
            }
        }
        
        return $ret;
    }
    
   
}