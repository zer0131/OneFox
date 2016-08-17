<?php

/**
 * @author: ryan<zer0131@vip.qq.com>
 * @desc: 基础Model类
 */

namespace onefox;

abstract class Model {

    protected $db;
    protected $db_config = 'default';
    protected $table = '';

    public function __construct() {
        $this->db = new DB($this->db_config);
    }

}

