<?php
/*
 * This file is part of the ************************ package.
 * _____________                           _______________
 *  ______/     \__  _____  ____  ______  / /_  _________
 *   ____/ __   / / / / _ \/ __`\/ / __ \/ __ \/ __ \___
 *    __/ / /  / /_/ /  __/ /  \  / /_/ / / / / /_/ /__
 *      \_\ \_/\____/\___/_/   / / .___/_/ /_/ .___/
 *         \_\                /_/_/         /_/
 *
 * The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
 * (c) 2010-2018 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * 日志默认配置文件
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2016.11.19
 * @version 1.0
 */
return [

    /**
     * ---------------------------------------------------------------
     * 默认日志驱动
     * ---------------------------------------------------------------
     *
     * 系统为所有日志提供了统一的接口，在使用上拥有一致性
     */
    'default' => env('log_driver', 'file'),

    /**
     * ---------------------------------------------------------------
     * 是否启用日志
     * ---------------------------------------------------------------
     *
     * 默认记录日志，记录日志会消耗服务器资源
     */
    'enabled' => true,

    /**
     * ---------------------------------------------------------------
     * 记录系统运行异常
     * ---------------------------------------------------------------
     *
     * 系统异常错误等日志是否记录
     * 系统运行过程 queryyetsimple\bootstrap\runtime
     */
    'runtime_enabled' => true,

    /**
     * ---------------------------------------------------------------
     * 允许记录的日志级别
     * ---------------------------------------------------------------
     *
     * 随意自定义,其中 debug、info、notice、warning、error、critical、alert、emergency 和 sql 为系统内部使用
     */
    'level' => [
        'debug',
        'info',
        'notice',
        'warning',
        'error',
        'critical',
        'alert',
        'emergency',
        'sql'
    ],

    /**
     * ---------------------------------------------------------------
     * 日志时间格式化
     * ---------------------------------------------------------------
     *
     * 每条日志信息开头的时间信息
     */
    'time_format' => '[Y-m-d H:i]',

    /**
     * ---------------------------------------------------------------
     * 日志连接参数
     * ---------------------------------------------------------------
     *
     * 这里为所有的日志的连接参数，每一种不同的驱动拥有不同的配置
     * 虽然有不同的驱动，但是在日志使用上却有着一致性
     */
    'connect' => [
        'file' => [
            // driver
            'driver' => 'file',

            // 日志文件名时间格式化
            'name' => 'Y-m-d H',

            // 日志文件大小限制,单位为字节 byte
            'size' => 2097152,

            // 默认的日志路径
            'path' => path_log_cache()
        ],

        'monolog' => [
            // driver
            'driver' => 'monolog',

            // 日志类型
            // support file、daily_file、syslog、error_log
            'type' => [
                'file'
            ],

            // 频道
            'channel' => 'Q',

            // 日志文件名时间格式化
            'name' => 'Y-m-d H',

            // 日志文件大小限制,单位为字节 byte
            'size' => 2097152,

            // 默认的日志路径
            'path' => path_log_cache('monolog')
        ]
    ]
];
