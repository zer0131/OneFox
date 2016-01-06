<?php

/** 
 * @author ryan<zer0131@vip.qq.com>
 * @desc memcache缓存类
 */

namespace OneFox\Caches;
use OneFox\Cache;
use OneFox\Config;

class CMemcache extends Cache {

	private $_memcache;

	public function __construct() {
		if (!extension_loaded('memcache')) {
			throw new \RuntimeException('memcache扩展未加载');
		}
		$this->options = Config::get('cache.memcache');
		if (!$this->options) {
			$this->options = array(
				'expire' => 0,
				'prefix' => 'onefox_',
				'servers' => array(
					array('host'=>'127.0.0.1', 'port'=>11217, 'persistent'=>false, 'weight'=>100),
				)
			);
		}
		$this->_connect();
	}

	private function _connect() {
		$this->_memcache = new \Memcache();
		foreach ($this->options['servers'] as $val) {
			$this->_memcache->addServer($val['host'], $val['port'], $val['persistent'], $val['weight']);
		}
	}

	public function get($name) {
		if (!$this->_memcache) {
			$this->_connect();
		}
		return $this->_memcache->get($this->options['prefix'].$name);
	}

	public function set($name, $value, $expire=null) {
		if (!$this->_memcache) {
			$this->_connect();
		}
		if (is_null($expire)) {
			$expire = $this->options['expire'];
		}
		return $this->_memcache->set($this->options['prefix'].$name, $value, 0, $expire);
	}

	public function rm($name) {
		if (!$this->_memcache) {
			$this->_connect();
		}
		return $this->_memcache->delete($this->options['prefix'].$name);
	}

	public function clear() {
		if (!$this->_memcache) {
			$this->_connect();
		}
		return $this->_memcache->flush();	
	}

	public function __call($funcName, $arguments) {
		if (!$this->_memcache) {
			$this->_connect();
		}
		$res = call_user_func_array(array($this->_memcache, $funcName), $arguments);	
		return $res;
	}
}
