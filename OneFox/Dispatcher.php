<?php

/** 
 * @author ryan<zer0131@vip.qq.com>
 * @desc 调度操作类
 */

namespace OneFox;

class Dispatcher {
    
    private static $_uri = '';
    private static $_defaultModule = DEFAULT_MODULE;
    private static $_defaultController = DEFAULT_CONTROLLER;
    private static $_defaultAction = DEFAULT_ACTION;
    private static $_currentModule = null;
    private static $_currentController = null;
    private static $_currentAction = null;
    
    const PATH_DEEP_3 = 3;
    const PATH_DEEP_2 = 2;
    
    public static function dipatcher(){
        //处理url
        if (!IS_CLI) {
            if (isset($_SERVER['PATH_INFO'])) {
                self::$_uri = $_SERVER['PATH_INFO'];
            } else {
                $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
                if (0 === strpos($requestUri, $_SERVER['SCRIPT_NAME'])) {
                    self::$_uri = substr($requestUri, strlen($_SERVER['SCRIPT_NAME']));
                } elseif (0 === strpos($requestUri, dirname($_SERVER['SCRIPT_NAME']))) {
                    self::$_uri = substr($requestUri, strlen(dirname($_SERVER['SCRIPT_NAME'])));
                } else {
                    self::$_uri = $requestUri;
                }
            }
        } else {
            self::$_uri = isset($_SERVER['argv'][1]) ? $_SERVER['argv'][1] : '';
        }
        
        self::$_uri = trim(self::$_uri, '/');//去除'/'
        
        self::_httpRout();
    }
    
    /**
     * 处理uri
     */
    private static function _httpRout(){
        $uri = self::$_uri;
        $moduleName = null;
        if ($uri == '') {
            if (PATH_DEEP == self::PATH_DEEP_3) {
                $moduleName = self::$_defaultModule;
            }
            $controllerName = self::$_defaultController;
            $actionName = self::$_defaultAction;
        } else {
            $uriArr = explode('/', $uri);
            
            if (PATH_DEEP == self::PATH_DEEP_3) {
                $moduleName = array_shift($uriArr);
                if(count($uriArr)>0){
                    $controllerName = array_shift($uriArr);
                    if(count($uriArr)>0){
                        $actionName = array_shift($uriArr);
                    }else{
                        $actionName = self::$_defaultAction;
                    }
                }else{
                    $controllerName = self::$_defaultController;
                    $actionName = self::$_defaultAction;
                }
            } else {
                $controllerName = array_shift($uriArr);
                $actionName = array_shift($uriArr);
                $actionName = $actionName !== null ? $actionName : self::$_defaultAction;
            }

            //参数处理
            if (count($uriArr) > 0) {
                $data = array();
                $total = count($uriArr);
                for ($i = 0; $i < $total; $i += 2) {
                    $k = $uriArr[$i];
                    $v = $uriArr[$i + 1];
                    $data[$k] = $v;
                }
                Request::setParams($data, 'get');
            }
        }
        
        //过滤并赋值
        $moduleName= C::filterChars($moduleName);
        $controllerName = C::filterChars($controllerName);
        $actionName = C::filterChars($actionName);

        self::$_currentModule = $moduleName;
        self::$_currentController = $controllerName;
        self::$_currentAction = $actionName;
    }
    
    public static function getModuleName() {
        if (is_null(self::$_currentModule)) {
            self::$_currentModule = '';
            return self::$_currentModule;
        }
        return ucfirst(self::$_currentModule);
    }
    
    public static function getControllerName() {
        return ucfirst(self::$_currentController);
    }
    
    public static function getActionName() {
        return self::$_currentAction;
    }
}


