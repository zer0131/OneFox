<?php
/**
 * @author zhangenrui
 * @desc 框架抽象基类, 可直接继承使用
 */

namespace onefox;

use ReflectionClass;

abstract class Base {

    protected static $_singleton = array();

    /**
     * 用于快速实例化类, 支持传入参数
     * @return static
     */
    public static function instance() {
        $args = func_get_args();//参数
        $className = get_called_class();//运行时调用的类名
        $ref = new ReflectionClass($className);//反射实例化
        return $ref->newInstanceArgs($args);
    }

    /**
     * 获取一个类的唯一实例, 不支持传入参数
     * @return static
     */
    public static function singleton() {
        $className = get_called_class();
        if(!isset(self::$_singleton[$className])){
            //注意new self()和new static()的区别
            self::$_singleton[$className] = new static();
        }
        return self::$_singleton[$className];
    }
}