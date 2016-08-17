<?php
/**
 * @author ryan<zer0131@vip.qq.com>
 * @desc 测试脚本
 */

set_time_limit(0);
require __DIR__ . DIRECTORY_SEPARATOR . 'loader.php';

class Test {

    public function run() {
        echo \Service\Service::test(). PHP_EOL;
    }
}

$test = new Test();
$test->run();