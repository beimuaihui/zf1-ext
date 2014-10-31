<?php
/**
 * beimuaihui System
 * Copyright(c) 2011-2020 beimuaihui.
 * @license    http://www.gnu.org/licenses/gpl.html     This software Under GPL V3 License 
 * beimuaihui@gmail.com
 * http://code.google.com/p/beimuaihui/
 * $Id$
 */

//require_once 'Zend/Controller/Action.php';

class Baogg_Controller_Action extends Zend_Controller_Action {
	
	public function comboAction(){
		//echo '<pre>';print_r();
				
		$ret=array("count"=>0,"data"=>array());
		$params=$this->getRequest()->getParams();
		
		if(isset($params['model_field_id']) && $params['model_field_id']){
			$ModelField=new ModelField();
			$rs_model_field=$ModelField->getOne($params['model_field_id']);
			
			$Model=new Model();
			$arr_model_detail=$Model->getModelAndField($rs_model_field[0]['model_id']);
			$instance=new $arr_model_detail['class_name'];
			
			$post=$this->_request->getParams ();
			$where=array();$order=array();$limit=array();
			foreach((array)$post as $k=>$v){
				if($k=="start" || $k=="limit"){
					$limit[$k]=$v;
				}else if($k=="sort" || $k=="dir"){
					$order[$k]=$v;
				}else{
					if($k==$instance->myPrimary ){
						if($v && is_numeric($v)){
							$v=(int)$v;
							$where[$k]=$v;
						}
					}else{
						$v='%'.$v.'%';
						$where[$k]=$v;
					}
			
				}
			}
			
			$rs=$instance->getList($where,$order,$limit,array($rs_model_field[0]['field_name']),array(),array(),array(),true);
			$rs_cnt=$instance->getList($where,NULL,NULL,"count(*) as cnt");
			
			$ret['count']=$rs_cnt[0]['cnt'];
			$ret['data']=$rs;
		}
		
		echo Zend_Json::encode($ret);
		$this->_helper->viewRenderer->setNoRender();
	}
	/*public function __call($method, $args)
    {
        if ('Action' ==	substr($method,	-6)) {
           // $controller	= $this->getRequest()->getControllerName();
           // $url = '/' . $controller . '/index';
            return $this->_redirect(BAOGG_BASE_URL);
        }

        throw new Exception('Invalid method');
    }

	 public function errorAction()
    {
        $errors = $this->_getParam('error_handler');

        switch ($errors->type) {
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
                // 404 error -- controller or action not found
                $this->getResponse()->setRawHeader('HTTP/1.1 404 Not Found');

                $content =<<<EOH
<h1>Error!</h1>
<p>The page you requested was not found.</p>
EOH;
                break;
            default:
                // application error
                $content =<<<EOH
<h1>Error!</h1>
<p>An unexpected error occurred. Please try again later.</p>
EOH;
                break;
        }

        // Clear previous content
        $this->getResponse()->clearBody();

        $this->view->content = $content;
    }*/
	
	/**
	 * 
	 * legacy
	 * @param unknown_type $view
	 */
	function getCssFiles($view) {
		$cssFiles = &$notification->getNotificationObject ();
		
		$cssFiles [] = "themes/default/common.css";
		$cssFiles [] = "libs/jquery/themes/base/jquery-ui.css";
		$cssFiles [] = "plugins/CoreHome/templates/styles.css";
		$cssFiles [] = "plugins/CoreHome/templates/menu.css";
		$cssFiles [] = "plugins/CoreHome/templates/datatable.css";
		$cssFiles [] = "plugins/CoreHome/templates/cloud.css";
		$cssFiles [] = "plugins/CoreHome/templates/jquery.ui.autocomplete.css";
	}
	
	function getJsFiles($notification) {
		$jsFiles = &$notification->getNotificationObject ();
		
		$jsFiles [] = "libs/jquery/jquery.js";
		$jsFiles [] = "libs/jquery/jquery-ui.js";
		$jsFiles [] = "libs/jquery/jquery.bgiframe.js";
		$jsFiles [] = "libs/jquery/jquery.tooltip.js";
		$jsFiles [] = "libs/jquery/jquery.truncate.js";
		$jsFiles [] = "libs/jquery/jquery.scrollTo.js";
		$jsFiles [] = "libs/jquery/jquery.blockUI.js";
		$jsFiles [] = "libs/jquery/fdd2div-modified.js";
		$jsFiles [] = "libs/jquery/superfish_modified.js";
		$jsFiles [] = "libs/jquery/jquery.history.js";
		$jsFiles [] = "libs/swfobject/swfobject.js";
		$jsFiles [] = "libs/javascript/sprintf.js";
		$jsFiles [] = "themes/default/common.js";
		$jsFiles [] = "plugins/CoreHome/templates/datatable.js";
		$jsFiles [] = "plugins/CoreHome/templates/broadcast.js";
		$jsFiles [] = "plugins/CoreHome/templates/menu.js";
		$jsFiles [] = "plugins/CoreHome/templates/calendar.js";
		$jsFiles [] = "plugins/CoreHome/templates/date.js";
		$jsFiles [] = "plugins/CoreHome/templates/autocomplete.js";
	}
}
