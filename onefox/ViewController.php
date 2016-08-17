<?php

/**
 * @author ryan<zer0131@vip.qq.com>
 * @desc 模板抽象控制器
 */

namespace onefox;

abstract class ViewController {

    protected $view;

    public function __construct() {
        //此方法可初始化控制器
        if (method_exists($this, '_init')) {
            $this->_init();
        }
        $this->view = new View();
    }

    protected function assign($name, $val = '') {
        $this->view->assign($name, $val);
    }

    protected function show($tpl = '') {
        $this->view->render($tpl);
    }

    protected function import($path, $val = array()) {
        $this->view->import($path, $val);
    }

    protected function fetch($tpl = '') {
        return $this->view->fetch($tpl);
    }
}

