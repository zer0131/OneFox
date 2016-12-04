<?php

/**
 * @author ryan<zer0131@vip.qq.com>
 * @desc 通用工具类
 */

namespace onefox;

class C {

    private static $_classObj = array();

    /**
     * @param $str
     * @param bool $onlyCharacterBase
     * @return string
     */
    public static function filterChars($str, $onlyCharacterBase = false) {
        $base = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ/_-0123456789';
        if ($onlyCharacterBase) {
            $base = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        }
        $left = trim($str, $base);
        if ( '' === $left) {
            return $str;
        } else {
            return '';
        }
    }

    /**
     * 输出日志
     * @param string|array $msg
     * @param string $level
     * @param string $config
     * @return object
     */
    public static function log($msg, $level = 'info', $config = 'default') {
        return Log::instance($config)->save($msg, $level);
    }
    
    public static function logInfo($msg) {
        self::log($msg, Log::INFO);
    }

    public static function logError($msg) {
        self::log($msg, Log::ERROR);
    }

    public static function logWarning($msg) {
        self::log($msg, Log::WARNING);
    }

    public static function logNotice($msg) {
        self::log($msg, Log::NOTICE);
    }

    /**
     * 迭代创建目录
     */
    public static function mkDirs($path, $mode = 0777) {
        if (!is_dir($path)) {
            if (!self::mkDirs(dirname($path), $mode)) {
                return false;
            }
            if (!mkdir($path, $mode)) {
                return false;
            }
        }
        return true;
    }

    /**
     * 生成随机字符串
     * @param int $length
     * @param boolean $haschar
     * @return string
     */
    public static function genRandomKey($length = 10, $haschar = true) {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        if ($haschar) {
            $chars .= "!@#$%^&*()-_[]{}<>~`+=,.;:/?";//包含特殊字符
        }
        $randomKey = '';
        for ($i = 0; $i < $length; $i++) {
            $randomKey .= $chars[mt_rand(1, strlen($chars) - 1)];
        }
        return $randomKey;
    }

    /**
     * 加载文件
     * @param string $filePath
     * @return mixed
     */
    public static function loadFile($filePath) {
        if (file_exists($filePath)) {
            return include $filePath;
        }
        return null;
    }

    /**
     * 生成模板页面输出用的tree
     * @param array $list 二维数组
     * @param int $pid 父级编号
     * @parma int $level 层级
     * @param string $html html输出前缀
     * @return array
     */
    public static function htmlToTree($list, $pid = 0, $level = 1, $html = ' -- ') {
        $tree = array();
        foreach ($list as $v) {
            if ($v['parent_id'] == $pid) {
                $v['sort'] = $level;
                $v['html'] = '|' . str_repeat($html, $level);
                $tree[] = $v;
                $tree = array_merge($tree, self::htmlToTree($list, $v['id'], $level + 1, $html));
            }
        }
        return $tree;
    }

    /**
     * 二维数组转化为树形列表
     * @param array $data
     * @return array
     */
    public static function dataToTree($data) {
        $items = array();
        foreach ($data as $val) {
            $items[$val['id']] = $val;
        }
        unset($data);
        $tree = array();
        foreach ($items as $item) {
            if (isset($items[$item['parent_id']])) {
                $items[$item['parent_id']]['son'][] = &$items[$item['id']];
            } else {
                $tree[] = &$items[$item['id']];
            }
        }
        return $tree;
    }

    /**
     * 计算两个时间戳的时间差
     * @param int $begin 开始时间戳
     * @param int $end 结束时间戳
     * @param boolean $returnStr 是否返回字符串
     * @return array|string
     */
    public static function timeDiff($begin, $end, $returnStr = true) {
        if ($begin < $end) {
            $starttime = $begin;
            $endtime = $end;
        } else {
            $starttime = $end;
            $endtime = $begin;
        }
        $timediff = $endtime - $starttime;
        $days = intval($timediff / 86400);
        $daysStr = $days ? $days . '天' : '';
        $remain = $timediff % 86400;
        $hours = intval($remain / 3600);
        $hoursStr = $hours ? $hours . '小时' : '';
        $remain = $remain % 3600;
        $mins = intval($remain / 60);
        $minsStr = $mins ? $mins . '分钟' : '';
        $secs = $remain % 60;
        $secsStr = $secs ? $secs . '秒' : '';
        if ($returnStr) {
            return $daysStr . $hoursStr . $minsStr . $secsStr;
        }
        return array(
            "day" => $days,
            "hour" => $hours,
            "min" => $mins,
            "sec" => $secs
        );
    }


    /**
     * 签名算法
     * @param $p
     * @param string $signKey
     * @return string
     */
    public static function sign($p, $signKey = '2#!&70op#e') {
        $signStr = '';
        if (empty($p) || !is_array($p)) {
            return $signStr;
        }
        unset($p['sign']);
        unset($p['signType']);
        foreach ($p as $k => $v) {
            if ($v !== '') {
                $signStr .= "{$k}={$v}&";
            }
        }
        return md5($signStr . $signKey);
    }

    /**
     * 创建类
     * @param $className
     * @return mixed|null
     */
    public static function newClass($className) {
        if (!$className) {
            return null;
        }
        if (!isset(self::$_classObj[$className]) || !self::$_classObj[$className]) {
            if (!class_exists($className)) {
                return null;
            }
            self::$_classObj[$className] = new $className;
        }
        return self::$_classObj[$className];
    }

    /**
     * XML编码
     * @param mixed $data 数据
     * @param string $root 根节点名
     * @param string $item 数字索引的子节点名
     * @param string $attr 根节点属性
     * @param string $id   数字索引子节点key转换的属性名
     * @param string $encoding 数据编码
     * @return string
     */
    public static function xmlEncode($data, $root = 'onefox', $item = 'item', $attr = '', $id = 'id', $encoding = 'utf-8') {
        if (is_array($attr)) {
            $_attr = array();
            foreach ($attr as $key => $value) {
                $_attr[] = "{$key}=\"{$value}\"";
            }
            $attr = implode(' ', $_attr);
        }
        $attr = trim($attr);
        $attr = empty($attr) ? '' : " {$attr}";
        $xml = "<?xml version=\"1.0\" encoding=\"{$encoding}\"?>";
        $xml .= "<{$root}{$attr}>";
        $xml .= self::data2Xml($data, $item, $id);
        $xml .= "</{$root}>";
        return $xml;
    }

    /**
     * 数据XML编码
     * @param mixed  $data 数据
     * @param string $item 数字索引时的节点名称
     * @param string $id   数字索引key转换为的属性名
     * @return string
     */
    public static function data2Xml($data, $item='item', $id='id') {
        $xml = $attr = '';
        foreach ($data as $key => $val) {
            if (is_numeric($key)) {
                $id && $attr = " {$id}=\"{$key}\"";
                $key = $item;
            }
            $xml .= "<{$key}{$attr}>";
            $xml .= (is_array($val) || is_object($val)) ? self::data2Xml($val, $item, $id) : $val;
            $xml .= "</{$key}>";
        }
        return $xml;
    }

    /**
     * 数据库查询结果排序
     * 使用方法: C::sortDbRet($data, array('column_name'=>SORT_ASC));
     * @param $data
     * @param $columns
     * @return mixed
     */
    public static function sortDbRet($data, $columns) {
        $args = array();
        foreach ($columns as $k => $v) {
            $args[] = array_column($data, $k);
            $args[] = $v;
        }
        $args[] = &$data;
        call_user_func_array('array_multisort', $args);
        return array_pop($args);
    }

    /**
     * 导出csv文件
     * @param array $header
     * @param array $data
     * @param string $fileName
     * @param bool $isWin
     */
    public static function exportToCSV($header, $data, $fileName, $isWin = true) {
        set_time_limit(0);
        ini_set('memory_limit', '256M');
        $fileName = $fileName . '-' . date('YmdHis') . '.csv';
        if ($isWin) {
            header("Content-Type: application/vnd.ms-excel; charset=GB2312");
        } else {
            header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
        }
        header('Content-Disposition: attachment;filename=' . $fileName);
        header('Cache-Control: max-age=0');
        //打开PHP文件句柄，php://output 表示直接输出到浏览器
        $fp = fopen('php://output', 'a');
        //输出头处理
        foreach ($header as $k => $v) {
            $header[$k] = $isWin ? iconv('utf-8', 'gb2312', $v) : $v;
        }
        fputcsv($fp, $header);
        //计数器
        $cnt = 0;
        //buffer刷新行数
        $limit = 500;
        foreach ($data as $key => $val) {
            $cnt++;
            if ($cnt == $limit) {
                ob_flush();
                flush();
                $cnt = 0;
            }
            $row = array();
            foreach ($val as $i => $v) {
                $row[] = $isWin ? iconv('utf-8', 'gb2312', $v) : $v;
            }
            fputcsv($fp, $row);
        }
        fclose($fp);
        exit;
    }
}

