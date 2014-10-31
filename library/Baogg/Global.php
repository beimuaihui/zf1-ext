<?php
/**
 * beimuaihui System
 * Copyright(c) 2011-2020 beimuaihui.
 * @license    http://www.gnu.org/licenses/gpl.html     This software Under GPL V3 License 
 * beimuaihui@gmail.com
 * http://code.google.com/p/beimuaihui/
 * $Id: Controller.php 197 2011-02-18 12:45:33Z beimuaihui $
 */
class Baogg_Global
{
    public static function get ($key)
    {
        $ret = "";
        if (! $key || ! is_string($key)) {
            return null;
        }
        $arr = explode(".", $key);
        if (count($arr) < 2) {
            return $ret;
        }
        $type = $arr[0];
        unset($arr[0]);
        switch ($type) {
            case 'l': //multi language                
                $ret = BAOGG_LANGUAGE::get($arr[1]);
                break;
            case 's': //session
                $ret = $_SESSION;
                foreach ($arr as $v) {
                    if (isset($ret[$v])) {
                        $ret = $ret[$v];
                    } else {
                        $ret = "";
                        break;
                    }
                }
                break;
            case 'c': //cookie
                $ret = $_COOKIE;
                foreach ($arr as $v) {
                    if (isset($ret[$v])) {
                        $ret = $ret[$v];
                    } else {
                        $ret = "";
                        break;
                    }
                }
                break;
           case 'm': //cookie
                global $MY;
                $ret = $MY;
                foreach ($arr as $v) {
                    if (isset($ret[$v])) {
                        $ret = $ret[$v];
                    } else {
                        $ret = "";
                        break;
                    }
                }
                break;
        }
        return $ret;
    }
    public static function set ($key, $value)
    {
        $res = false;
        if (! $key || ! is_string($key)) {
            return $res;
        }
        $arr = explode(".", $key);
        if (count($arr) < 2) {
            return $res;
        }
        $type = $arr[0];
        unset($arr[0]);
        switch ($type) {
            case 's': //session                
                $ret = & $_SESSION;
                foreach ($arr as $k => $v) {                    
                    //not array ,then init array,else if not exist data add key=$v 
                    if(! is_array($ret)){
                         $ret = array($v => array());
                    }else if(!isset($ret[$v])){
                       $ret[$v] = array(); 
                    } 
                    $ret = & $ret[$v];
                }
                $ret = $value;
                $res = true;
                //echo __FILE__.__LINE__.'<pre>';print_r($_SESSION);echo '<br />';
                break;
            case 'c': //cookie                
                $ret = & $_COOKIE;
                foreach ($arr as $k => $v) {
                    //echo '<pre>';print_r($arr);print_r($ret);debug_zval_dump($_SESSION);echo '<br />';
                    //not array ,then init array,else if not exist data add key=$v 
                    if(! is_array($ret)){
                         $ret = array($v => array());
                    }else if(!isset($ret[$v])){
                       $ret[$v] = array(); 
                    } 
                    $ret = & $ret[$v];
                }
                $ret = $value;
                $res = true;
                break;
             case 'm': //cookie 
                global $MY;               
                $ret = & $MY;
                foreach ($arr as $k => $v) {
                    //echo '<pre>';print_r($arr);print_r($ret);debug_zval_dump($_SESSION);echo '<br />';
                    //not array ,then init array,else if not exist data add key=$v 
                    if(! is_array($ret)){
                         $ret = array($v => array());
                    }else if(!isset($ret[$v])){
                       $ret[$v] = array(); 
                    } 
                    $ret = & $ret[$v];
                }
                $ret = $value;
                $res = true;
                break;
        }
        return $res;
    }
}