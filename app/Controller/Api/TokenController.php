<?php

namespace Controller\Api;

use Controller\BaseController;
use OneFox\Response;

class TokenController extends BaseController {
    
    public function indexAction(){
        Response::json(array('msg'=>'ok','code'=>0,'data'=>null));
    }
    
}

