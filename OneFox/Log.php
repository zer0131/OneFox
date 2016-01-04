<?php

/** 
 * @author ryan<zer0131@vip.qq.com>
 * @desc 日志类
 */

namespace OneFox;

use DateTime;
use RuntimeException;

final class Log {
        
    private static $_instance = null;
    private $logFilePath;//日志路径
    private $options = array(
        'extension' => 'log',
        'dateFormat' => 'Y-m-d H:i:s',
        'filename' => false,
        'flushFrequency' => false,
        'prefix' => ''
    );
    private $logLevelThreshold = 'debug';//默认日志级别
    private $logLineCount = 0;//总行数
    private $logLevels = array(
        'emergency' => 0,
        'alert' => 1,
        'critical' => 2,
        'error' => 3,
        'warning' => 4,
        'notice' => 5,
        'info' => 6,
        'debug' => 7
    );
    private $fileHandle;//文件句柄
//    private $lastLine = '';
    private $defaultPermissions = 0777;//文件权限
    
    public static function instance($logLevelThreshold = 'debug', array $options = array()){
        if (!self::$_instance) {
            self::$_instance = new self(LOG_PATH, $logLevelThreshold, $options);
        }
        return self::$_instance;
    }
    
    private function __construct($logDirectory, $logLevelThreshold, array $options) {
        $this->logLevelThreshold = $logLevelThreshold;
        $this->options = array_merge($this->options, $options);
        $logDirectory = rtrim($logDirectory, DS);
        if (!file_exists($logDirectory)) {
            mkdir($logDirectory, $this->defaultPermissions, true);
        }
        if ($logDirectory === "php://stdout" || $logDirectory === "php://output") {
            $this->setLogToStdOut($logDirectory);
            $this->setFileHandle('w+');
        } else {
            $this->setLogFilePath($logDirectory);
            if (file_exists($this->logFilePath) && !is_writable($this->logFilePath)) {
                throw new RuntimeException('The file could not be written to. Check that appropriate permissions have been set.');
            }
            $this->setFileHandle('a');
        }
        if (!$this->fileHandle) {
            throw new RuntimeException('The file could not be opened. Check permissions.');
        }
    }
    
    private function __clone() {
        //code
    }

        /**
     * 记录日志，设置日志级别不可超过默认日志级别
     * @param type  $message
     * @param type  $level
     * @param array $context
     * @return type
     */
    public function save($message, $level, array $context = array()){
        if ($this->logLevels[$this->logLevelThreshold] < $this->logLevels[$level]) {
            return false;
        }
        $message = $this->formatMessage($level, $message, $context);
        return $this->write($message);
    }
    
    /**
     * 写文件
     * @param type $message
     * @throws RuntimeException
     */
    private function write($message){
        $res = false;
        if (null !== $this->fileHandle) {
            $res = fwrite($this->fileHandle, $message);
            if ($res === false) {
                throw new RuntimeException('The file could not be written to. Check that appropriate permissions have been set.');
            } else {
//                $this->lastLine = trim($message);
                $this->logLineCount++;
                if ($this->options['flushFrequency'] && $this->logLineCount % $this->options['flushFrequency'] === 0) {
                    fflush($this->fileHandle);
                }
            }
        }
        return $res;
    }
    
    /**
     * 格式化信息
     * @param type $level
     * @param string $message
     * @param type $context
     * @return type
     */
    private function formatMessage($level, $message, $context){
        $level = strtoupper($level);
        if (!empty($context)) {
            $message .= PHP_EOL . $this->indent($this->contextToString($context));
        }
        return "[{$this->getTimestamp()}] [{$level}] {$message}" . PHP_EOL;
    }
    
    /**
     * 缩进
     * @param type $string
     * @param type $indent
     * @return type
     */
    private function indent($string, $indent = ' '){
        return $indent . str_replace("\n", "\n" . $indent, $string);
    }
    
    /**
     * 上下文转换字符串
     * @param type $context
     * @return type
     */
    private function contextToString($context){
        $export = '';
        foreach ($context as $key => $value) {
            $export .= "{$key}: ";
            $export .= preg_replace(array(
                '/=>\s+([a-zA-Z])/im',
                '/array\(\s+\)/im',
                '/^  |\G  /m'
            ), array(
                '=> $1',
                'array()',
                '    '
            ), str_replace('array (', 'array(', var_export($value, true)));
            $export .= PHP_EOL;
        }
        return str_replace(array('\\\\', '\\\''), array('\\', '\''), rtrim($export));
    }
    
    /**
     * 获取时间
     * @return type
     */
    private function getTimestamp(){
        $originalTime = microtime(true);
        $micro = sprintf("%06d", ($originalTime - floor($originalTime)) * 1000000);
        $date = new DateTime(date('Y-m-d H:i:s.' . $micro, $originalTime));
        return $date->format($this->options['dateFormat']);
    }
    
    private function setLogToStdOut($stdOutPath){
        $this->logFilePath = $stdOutPath;
    }
    
    private function setFileHandle($writeMode){
        $this->fileHandle = fopen($this->logFilePath, $writeMode);
    }
    
    private function setLogFilePath($logDirectory){
        if ($this->options['filename']) {
            if (strpos($this->options['filename'], '.log') !== false || strpos($this->options['filename'], '.txt') !== false) {
                $this->logFilePath = $logDirectory . DS . $this->options['filename'];
            } else {
                $this->logFilePath = $logDirectory . DS . $this->options['filename'] . '.' . $this->options['extension'];
            }
        } else {
            $this->logFilePath = $logDirectory . DS . $this->options['prefix'] . date('Y-m-d') . '.' . $this->options['extension'];
        }
    }
    
    public function __destruct(){
        if ($this->fileHandle) {
            fclose($this->fileHandle);
        }
    }
    
}

