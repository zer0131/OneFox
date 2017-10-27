<?php

/**
 * @author ryan<zer0131@vip.qq.com>
 * @desc 快速实例化类
 */

namespace onefox\traits;

trait Instance {

    /**
     * 获取实例
     * @return static
     */
    public static function getInstance() {
        $args = func_get_args();//参数
        $className = get_called_class();//运行时调用的类名
        $ref = new \ReflectionClass($className);//反射实例化
        return $ref->newInstanceArgs($args);
    }
}
