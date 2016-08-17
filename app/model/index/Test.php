<?php

namespace model\index;

use onefox\Model;

class Test extends Model {

	protected $db_config = 'test';

    public function test(){
        return $this->db->query('select * from `posts`');
    }
}

