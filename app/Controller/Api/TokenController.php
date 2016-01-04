<?php

namespace Controller\Api;

use Controller\BaseController;
use Model\Index\TestModel;

class TokenController extends BaseController {
    
    public function indexAction(){
        $obj = new TestModel();
        $obj->test();
    }
    
    public function checkBefore(){
        echo 'check before<br/>';
    }

    public function checkAction(){
        echo "ok<br>";
    }
    
    public function checkAfter(){
        echo 'check after<br/>';
    }
}

