<?php

/** 
 * @author: ryan<zer0131@vip.qq.com>
 * @desc: Api接口控制器
 */

namespace OneFox;

abstract class ApiController {
    public function __construct() {
        $this->view = new View(); 
        //此方法可初始化控制器
        if (method_exists($this, '_init')){
            $this->_init();
        }
    }
}

