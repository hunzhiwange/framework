<?php
// [$QueryPHP] A PHP Framework Since 2010.10.03. <Query Yet Simple>
// ©2010-2017 http://queryphp.com All rights reserved.
<<<queryphp
##########################################################
#   ____                          ______  _   _ ______   #
#  /     \       ___  _ __  _   _ | ___ \| | | || ___ \  #
# |   (  ||(_)| / _ \| '__|| | | || |_/ /| |_| || |_/ /  #
#  \____/ |___||  __/| |   | |_| ||  __/ |  _  ||  __/   #
#       \__   | \___ |_|    \__  || |    | | | || |      #
#     Query Yet Simple      __/  |\_|    |_| |_|\_|      #
#                          |___ /  Since 2010.10.03      #
##########################################################
queryphp;

/**
 * 缓存默认配置文件
 *
 * @author Xiangmin Liu<635750556@qq.com>
 * @package $$
 * @since 2016.11.19
 * @version 1.0
 */
return [ 
        
        /**
         * ---------------------------------------------------------------
         * 默认缓存驱动
         * ---------------------------------------------------------------
         *
         * 这里可以可以设置为 file、memcache 等
         * 系统为所有缓存提供了统一的接口，在使用上拥有一致性
         */
        'default' => env ( 'cache_driver', 'file' ),
        
        /**
         * ---------------------------------------------------------------
         * 缓存调试 REQUEST 参数，强制不启用缓存
         * ---------------------------------------------------------------
         *
         * 在项目开发过程中有时候我们缓存写入的数据存在错误，但是缓存还没有过去，我们可以在 url 通过 GET 或者 POST 传入一个参数
         * 来让缓存系统强制重新获取，可以用来判断缓存数据是否错误和重新刷新缓存数据
         */
        'nocache_force' => '~@nocache_force',
        
        /**
         * ---------------------------------------------------------------
         * 缓存键值前缀
         * ---------------------------------------------------------------
         *
         * 为了防止与别的应用程序在缓存键值上出现冲突，可以设置一个应用特殊的前缀
         */
        'prefix' => '~@',
        
        /**
         * ---------------------------------------------------------------
         * 程序默认缓存时间
         * ---------------------------------------------------------------
         *
         * 设置好缓存时间，超过这个时间系统缓存会重新进行获取, -1 表示永不过期
         * 缓存时间为当前时间加上以秒为单位的数量
         */
        'expire' => 86400,
        
        /**
         * ---------------------------------------------------------------
         * 缓存时间预置
         * ---------------------------------------------------------------
         *
         * 为了满足不同的需求，有部分缓存键值需要的缓存时间不一致，有些缓存可能需要频繁更新
         * 于是这里我们可以通过配置缓存预设时间来控制缓存的键值的特殊时间，其中 * 表示通配符
         * 键值 = 缓存值，键值不带前缀,例如 ['option' => 60]
         */
        'time_preset' => [ ],
        
        /**
         * ---------------------------------------------------------------
         * 缓存连接参数
         * ---------------------------------------------------------------
         *
         * 这里为所有的缓存的连接参数，每一种不同的驱动拥有不同的配置
         * 虽然有不同的驱动，但是在缓存使用上却有着一致性
         */
        '+connect' => [ 
                
                '+file' => [
                        // 文件缓存路径
                        'path' => project ( 'path_cache_file' ),
                        
                        // 是否 serialize 格式化
                        'serialize' => true,
                        
                        // 前缀
                        'prefix' => null,
                        
                        // 默认过期时间
                        'expire' => null 
                ],
                
                '+memcache' => [
                        // 多台服务器
                        'servers' => [ ],
                        
                        // 默认缓存服务器
                        'host' => env ( 'session_memcache_host', '127.0.0.1' ),
                        
                        // 默认缓存服务器端口
                        'port' => env ( 'session_memcache_port', 11211 ),
                        
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
                        // 默认缓存服务器
                        'host' => env ( 'session_redis_host', '127.0.0.1' ),
                        
                        // 默认缓存服务器端口
                        'port' => env ( 'session_redis_port', 6379 ),
                        
                        // 认证密码
                        'password' => env ( 'session_redis_password', '' ),
                        
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
