<?php

/** 
 * @author ryan<zer0131@vip.qq.com>
 * @desc 数据库访问类，基于PDO
 */

namespace OneFox;

class DB {

    private $_pdo;//pdo对象
    private $_sQuery;//statement对象
    private $_settings;//数据库设置
    private $_bConnected = false;//是否连接数据库
    private $_parameters = array();//参数数组

    public function __construct($dbConfig=''){
        if (!$dbConfig) {
            $dbConfig = 'default';
        }
        $this->_settings = Config::get('database.'.$dbConfig);
        $this->_connect();
    }

    /**
     * 连接数据库
     */ 
    private function _connect(){
        $dsn = 'mysql:dbname='.$this->_settings["dbname"].';host='.$this->_settings["host"].';port='.$this->_settings['port'];
        try {
            $this->_pdo = new \PDO($dsn, $this->_settings["user"], $this->_settings["password"], array(\PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8'"));
            //设置属性
            $this->_pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $this->_pdo->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);
            $this->_bConnected = true;
        } catch (\PDOException $e) {
            $this->_exceptionLog($e->getMessage().'(Connect error)');
        }
    }

    /**
     * 错误处理
     */ 
    private function _exceptionLog($message ,$sql = ""){
        if (!empty($sql)) {
            $message .= "\r\nRaw SQL : "  . $sql;
        }
        C::log($message, 'error');
    }

    public function close(){
        $this->_pdo = null;
    }

    /**
     * 初始化
     */ 
    private function _init($query ,$parameters = ""){
        if (!$this->_bConnected) {
            $this->_connect();
        }
        try {
            $this->_sQuery = $this->_pdo->prepare($query);

            $this->bindMore($parameters);
            if (!empty($this->_parameters)) {
                foreach ($this->_parameters as $param) {
                    $parameters = explode("\x7F",$param);
                    $this->_sQuery->bindParam($parameters[0],$parameters[1], $this->_checkType($parameters[1]));
                }
            }
            $this->_sQuery->execute();
        }
        catch(\PDOException $e) {
            $this->_exceptionLog($e->getMessage(), $query);
        }
        //重置参数
        $this->_parameters = array();
    }

    /**
     * 绑定参数
     * 示例: $db_obj->bind('name', 'ryan')
     */ 
    public function bind($para ,$value){
        $this->_parameters[sizeof($this->_parameters)] = ":" . $para . "\x7F" . $value;
    }

    /**
     * 批量绑定参数
     * 示例: $db_obj->bindMore(array('name'=>'ryan', 'age'=>20))
     */ 
    public function bindMore($parray){
        if (empty($this->_parameters) && is_array($parray)) {
            $columns = array_keys($parray);
            foreach($columns as $i => &$column) {
                $this->bind($column, $parray[$column]);
            }
        }
    }

    /**
     * 执行sql语句，如select, insert, update
     * 示例：
     * select: $db_obj->query('select * from `test` where `id`=:id', array('id'=>1))
     * delete: $db_obj->query('delete from `test` where `id`=:id', array('id'=>1))
     * insert: $db_obj->query('insert into `test`(name,age) values(:name,:age)', array('name'=>'ryan','age'=>20))
     * update: $db_obj->query('update `test` set name=:name where `id`=:id', array('name'=>'ryan', 'id'=>7))
     */	
    public function query($query ,$params = null, $fetchmode = \PDO::FETCH_ASSOC){
        $query = trim($query);
        $this->_init($query,$params);
        $rawStatement = explode(' ', $query);

        $statement = strtolower($rawStatement[0]);

        if ($statement === 'select' || $statement === 'show') {
            return $this->_sQuery->fetchAll($fetchmode);
        } elseif ( $statement === 'insert' ||  $statement === 'update' || $statement === 'delete' ) {
            return $this->_sQuery->rowCount();
        } else {
            return null;
        }
    }

    /**
     * 返回最后插入的主键
     */ 
    public function lastInsertId($name=null){
        return $this->_pdo->lastInsertId($name);
    }

    /**
     * 返回一列
     * 示例: $db_obj->column('select name from `test`');
     */	
    public function column($query ,$params = null){
        $this->_init($query,$params);
        $Columns = $this->_sQuery->fetchAll(\PDO::FETCH_NUM);

        $column = null;
        foreach ($Columns as $cells) {
            $column[] = $cells[0];
        }
        return $column;
    }

    /**
     * 返回一行
     * 示例: $db_obj->row('select * from `test` where `id`=:id', array('id'=>7))
     */ 
    public function row($query ,$params = null,$fetchmode = \PDO::FETCH_ASSOC){
        $this->_init($query,$params);
        return $this->_sQuery->fetch($fetchmode);
    }

    /**
     * 返回字段值
     * 示例: $db_obj->single('select name from `test` where `id`=:id', array('id'=>7))
     * 结果: ryan
     */ 
    public function single($query ,$params = null){
        $this->_init($query,$params);
        return $this->_sQuery->fetchColumn();
    }

    /**
     * 类型判断
     */
    private function _checkType($value){
        if (is_int($value)) {
            return \PDO::PARAM_INT;
        } elseif (is_string($value)) {
            return \PDO::PARAM_STR;
        } elseif (is_bool($value)) {
            return \PDO::PARAM_BOOL;
        } else {
            return \PDO::PARAM_STR;
        }
    }
}
