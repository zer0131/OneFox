<?php
/**
 * @author ryan<zer0131@vip.qq.com>
 * @desc 测试脚本
 */

use \Onefox\Lib\Encrypt\DesEncryption;

set_time_limit(0);
require __DIR__ . DIRECTORY_SEPARATOR . 'loader.php';

class Test {

    public function run() {
        $enStr = DesEncryption::encrypt('zhangenrui', 'test');
        dumper($enStr);
        $deStr = DesEncryption::decrypt($enStr, 'test');
        dumper($deStr);
    }
}

$test = new Test();
$test->run();
