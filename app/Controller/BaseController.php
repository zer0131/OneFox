<?php

namespace Controller;

use OneFox\Controller;

abstract class BaseController extends Controller {

	protected function _init(){
		$this->assign('title', '首页');
	}
    
    protected function com(){
        echo 'i am com';
    }
}
