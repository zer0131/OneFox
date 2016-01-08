<?php

/**
 * @author: ryan<zer0131@vip.qq.com>
 * @desc: 缓存配置
 */

$common = array(
	'type' => 'file',
	'file' => array(
		'path' => APP_PATH.DS.'Cache',
		'expire' => 0,
		'prefix' => 'onefox_'
	),
	'memcache' => array(
		'expire' => 0,
		'prefix' => 'onefox_',
		'servers' => array(
			array('host'=>'127.0.0.1', 'port'=>11211, 'persistent'=>false, 'weight'=>10),
		)
	),
	'redis' => array()
);

$online = array();

$dev = array();

return DEBUG ? array_merge($common, $dev) : array_merge($common, $online);
