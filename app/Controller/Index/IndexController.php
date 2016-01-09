<?php

/**
 * @author ryan<zer0131@vip.qq.com>
 * @desc 默认控制器
 */
namespace Controller\Index;

use Controller\BaseController;
use OneFox\Config;
use Model\Index\TestModel;
use OneFox\C;
use OneFox\Cache;

class IndexController extends BaseController {
    
    /**
     * 默认方法
     */
    public function indexAction(){
		//$this->assign(array('name'=>'ryan', 'age'=>27));
		//$this->show();
		//$test_model = new TestModel();
		//var_dump($test_model->test());exit;
		//C::log('test');
		//Cache::getInstance()->set('name', 'ryan', 10);
		//echo Cache::getInstance()->get('name');
    }
}
