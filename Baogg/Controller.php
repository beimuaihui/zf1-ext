<?php
/**
 * beimuaihui System
 * Copyright(c) 2011-2020 beimuaihui.
 * @license    http://www.gnu.org/licenses/gpl.html     This software Under GPL V3 License 
 * beimuaihui@gmail.com
 * http://code.google.com/p/beimuaihui/
 * $Id: Controller.php 479 2012-03-14 03:46:16Z beimuaihui@gmail.com $
 */

class Baogg_Controller {
	
	public static function getUrlParams() {		
		$request = Zend_Controller_Front::getInstance ()->getRequest ();
		$params = $request ? $request->getParams () : $_REQUEST;
		if (! isset ( $params ['module'] )) {
			$params = array_merge ( $params, self::getCustomControllerName () );
		}
		return $params;
	}
	public static function getCustomControllerName() {
		
		$sParams = substr ( $_SERVER ['REQUEST_URI'], strlen ( BAOGG_BASE_URL ) );
		$aParams = explode ( "/", $sParams );
		$ctlName ['module'] = isset($aParams[0]) && $aParams[0]? $aParams [0] : "default";
		$ctlName ['controller'] = isset($aParams[1]) && $aParams[1]? $aParams [1] : "index";
		$ctlName ['action'] = isset($aParams[2])  && $aParams[2]? $aParams [2] : "index";
		return $ctlName;
	}
	
	
	
	function buildParamList($params)
	{
		$retval = array();
		foreach($params as $key => $param) {
			if(is_string($key)) {
				$key = htmlspecialchars(var_export($key, true) . ' => ');
			} else {
				$key = '';
			}
			switch(gettype($param)) {
				case 'array':
					$retval[] = $key . 'array(' . buildParamList($param) . ')';
					break;
				case 'object':
					$retval[] = $key . '[object <em>' . get_class($param) . '</em>]';
					break;
				case 'resource':
					$retval[] = $key . '[resource <em>' . htmlspecialchars(get_resource_type($param)) . '</em>]';
					break;
				case 'string':
					$retval[] = $key . htmlspecialchars(var_export(strlen($param) > 51 ? substr_replace($param, ' 鈥� ', 25, -25) : $param, true));
					break;
				default:
					$retval[] = $key . htmlspecialchars(var_export($param, true));
			}
		}
		return implode(', ', $retval);
	}
}