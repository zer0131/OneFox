<?php

/**
 * @author: ryan<zer0131@vip.qq.com>
 * @desc: 日志配置
 */

$common = array(
    'default' => array(
        'ext' => 'log',
        'date_format' => 'Y-m-d H:i:s',
        'filename' => '',
        'log_path' => '',
        'prefix' => '',
        'log_level' => 'info',
    )
);

$online = array();

$dev = array();

return DEBUG ? array_merge($common, $dev) : array_merge($common, $online);