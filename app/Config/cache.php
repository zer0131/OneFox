<?php

/**
 * @author: ryan<zer0131@vip.qq.com>
 * @desc: 缓存配置
 */

$common = array(
	'file' => array(
		//code
	),
);

$online = array();

$dev = array();

return DEBUG ? array_merge($common, $dev) : array_merge($common, $online);
