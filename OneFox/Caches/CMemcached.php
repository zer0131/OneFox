<?php

/** 
 * @author ryan<zer0131@vip.qq.com>
 * @desc memcached缓存类
 */

namespace OneFox\Caches;
use OneFox\Cache;
use OneFox\Config;

class CMemcached extends Cache {

	private $_memcached;

	public function __construct() {
		//code
	}

	private function _connect() {
		//code
	}

	public function get($name) {
		//code
	}

	public function set($name, $value, $expire=null) {
		//code
	}

	public function rm($name) {
		//code
	}

	public function clear() {
		//code
	}

	public function __call($funcName, $arguments) {
		//code
	}
}
