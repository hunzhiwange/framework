<?php
/*
 * This file is part of the ************************ package.
 * ##########################################################
 * #   ____                          ______  _   _ ______   #
 * #  /     \       ___  _ __  _   _ | ___ \| | | || ___ \  #
 * # |   (  ||(_)| / _ \| '__|| | | || |_/ /| |_| || |_/ /  #
 * #  \____/ |___||  __/| |   | |_| ||  __/ |  _  ||  __/   #
 * #       \__   | \___ |_|    \__  || |    | | | || |      #
 * #     Query Yet Simple      __/  |\_|    |_| |_|\_|      #
 * #                          |___ /  Since 2010.10.03      #
 * ##########################################################
 *
 * The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
 * (c) 2010-2017 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * session 默认配置文件
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2016.11.19
 * @version 1.0
 */
return [

    /**
     * ---------------------------------------------------------------
     * session 驱动
     * ---------------------------------------------------------------
     *
     * 采用什么源保存 session 数据，默认采用 PHP 惯性设置
     */
    'default' => env('session_driver', 'cookie'),

    /**
     * ---------------------------------------------------------------
     * id
     * ---------------------------------------------------------------
     *
     * 设置了将会调用 session_id ( id )
     */
    'id' => null,

    /**
     * ---------------------------------------------------------------
     * name
     * ---------------------------------------------------------------
     *
     * 设置了将会调用 session_name ( name )
     */
    'name' => null,

    /**
     * ---------------------------------------------------------------
     * cookie_domain
     * ---------------------------------------------------------------
     *
     * ini_set ( 'session.cookie_domain', cookie_domain )
     */
    'cookie_domain' => null,

    /**
     * ---------------------------------------------------------------
     * cache_limiter
     * ---------------------------------------------------------------
     *
     * session_cache_limiter ( cache_limiter )
     */
    'cache_limiter' => null,

    /**
     * ---------------------------------------------------------------
     * cookie_lifetime
     * ---------------------------------------------------------------
     *
     * ini_set ( 'session.cookie_lifetime', cookie_lifetime )
     */
    'cookie_lifetime' => null,

    /**
     * ---------------------------------------------------------------
     * gc_maxlifetime
     * ---------------------------------------------------------------
     *
     * ini_set ( 'session.gc_maxlifetime', gc_maxlifetime )
     */
    'gc_maxlifetime' => null,

    /**
     * ---------------------------------------------------------------
     * save_path
     * ---------------------------------------------------------------
     *
     * session_save_path ( save_path )
     */
    'save_path' => null,

    /**
     * ---------------------------------------------------------------
     * use_trans_sid
     * ---------------------------------------------------------------
     *
     * ini_set ( 'session.use_trans_sid', use_trans_sid ? 1 : 0 )
     */
    'use_trans_sid' => null,

    /**
     * ---------------------------------------------------------------
     * gc_probability
     * ---------------------------------------------------------------
     *
     * ini_set ( 'session.gc_probability', gc_probability )
     */
    'gc_probability' => null,

    /**
     * ---------------------------------------------------------------
     * session 前缀
     * ---------------------------------------------------------------
     *
     * 合理设置 session 前缀可以避免项目之间的冲突
     */
    'prefix' => 'q_',

    /**
     * ---------------------------------------------------------------
     * expire
     * ---------------------------------------------------------------
     *
     * 为了与下面的配置 expire 对应，这里没有设置为 cache_expire
     * session_cache_expire ( expire )
     */
    'expire' => 86400,

    /**
     * ---------------------------------------------------------------
     * session 驱动连接参数
     * ---------------------------------------------------------------
     *
     * 这里为所有的 session 驱动的连接参数，每一种不同的驱动拥有不同的配置
     * 虽然有不同的驱动，但是在使用上却有着一致性
     */
    '+connect' => [
        '+cookie' => [
            // driver
            'driver' => 'cookie'
        ],

        '+memcache' => [
            // driver
            'driver' => 'memcache',

            // 多台服务器
            'servers' => [],

            // 默认缓存服务器
            'host' => env('session_memcache_host', '127.0.0.1'),

            // 默认缓存服务器端口
            'port' => env('session_memcache_port', 11211),

            // 是否压缩缓存数据
            'compressed' => false,

            // 是否使用持久连接
            'persistent' => true,

            // 前缀
            'prefix' => null,

            // 默认过期时间
            'expire' => null
        ],

        '+redis' => [
            // driver
            'driver' => 'redis',

            // 默认缓存服务器
            'host' => env('session_redis_host', '127.0.0.1'),

            // 默认缓存服务器端口
            'port' => env('session_redis_port', 6379),

            // 认证密码
            'password' => env('session_redis_password', ''),

            // redis 数据库索引
            'select' => 0,

            // 超时设置
            'timeout' => 0,

            // 是否使用持久连接
            'persistent' => false,

            // 是否使用 serialize 编码
            'serialize' => true,

            // 前缀
            'prefix' => null,

            // 默认过期时间
            'expire' => null
        ]
    ]
];
