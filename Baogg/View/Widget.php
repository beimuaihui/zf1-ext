<?php
/**
 * beimuaihui System
 * Copyright(c) 2011-2020 beimuaihui.
 * @license    http://www.gnu.org/licenses/gpl.html     This software Under GPL V3 License 
 * beimuaihui@gmail.com
 * http://code.google.com/p/beimuaihui/
 * $Id: Grid.php 208 2011-03-04 13:23:19Z beimuaihui $
 */

class Baogg_View_Widget
{
	public function __call($method, $args){
		if(method_exists($this, $method))
        {
            $this->$method();
        }
	}
}