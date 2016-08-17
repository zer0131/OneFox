<?php

namespace Model\Index;

use OneFox\Model;

class TestModel extends Model {

	protected $db_config = 'test';

    public function test(){
        return $this->db->query('select * from `posts`');
    }
}

