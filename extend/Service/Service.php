<?php

/**
 * @author zhangenrui
 * @desc 测试扩展类库
 */

namespace Service;

use model\index\Test;

class Service {
    public static function test() {
        dumper('service test');
    }

    public static function composerTest() {
        // 请使用composer引入"symfony/var-dumper"后使用
        //$test = new Test();
        dumper('ok');
    }
}
