<?php
/**
 * @author ryan<zer0131@vip.qq.com>
 * @desc 基于redis锁实现
 */

namespace onefox;

class Lock extends Base {

    const LOCK_PREFIX = 'lock:';

    private $_redis;

    public function __construct() {
        $this->_redis = Cache::getInstance('redis');
    }

    public function acquire($lockName, $acquireTime = 10, $lockTimeout = 10) {
        $value = uniqid();
        $key = self::LOCK_PREFIX . $lockName;
        $lockTimeout = intval($lockTimeout);
        $end = time() + $acquireTime;
        while (time() < $end) {
            if ($this->_redis->setnx($key, $value)) {
                $this->_redis->expire($key, $lockTimeout);
                return $value;
            } elseif (!$this->_redis->ttl($key)) {
                $this->_redis->expire($key, $lockTimeout);
            }
            usleep(1000);
        }
        return false;
    }

    public function release($lockName, $value) {
        $key = self::LOCK_PREFIX . $lockName;
        if ($this->_redis->get($key) == $value) {
            $this->_redis->del($key);
            return true;
        }
        return false;
    }
}