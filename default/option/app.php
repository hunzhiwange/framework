<?php declare(strict_types=1);
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
 * 应用全局配置文件
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2016.11.19
 * @version 1.0
 */
return [

    /**
     * ---------------------------------------------------------------
     * 运行环境
     * ---------------------------------------------------------------
     *
     * 根据不同的阶段设置不同的开发环境
     * 可以为 production : 生产环境 testing : 测试环境 development : 开发环境
     */
    'environment' => env('environment', 'development'),

    /**
     * ---------------------------------------------------------------
     * 是否打开调试模式
     * ---------------------------------------------------------------
     *
     * 打开调试模式可以显示更多精确的错误信息
     */
    'debug' => env('debug', false),

    /**
     * ---------------------------------------------------------------
     * Gzip 压缩
     * ---------------------------------------------------------------
     *
     * 启用页面 gzip 压缩，需要系统支持 gz_handler 函数
     */
    'start_gzip' => true,

    /**
     * ---------------------------------------------------------------
     * 系统时区
     * ---------------------------------------------------------------
     *
     * 此配置用于 date_default_timezone_set 应用设置系统时区
     * 此功能会影响到 date.time 相关功能
     */
    'time_zone' => 'Asia/Shanghai',

    /**
     * ---------------------------------------------------------------
     * 安全 key
     * ---------------------------------------------------------------
     *
     * 请妥善保管此安全 key,防止密码被人破解
     * \Leevel\Encryption\Encryption 安全 key
     */
    'auth_key' => env('app_auth_key', '7becb888f518b20224a988906df51e05'),

    /**
     * ---------------------------------------------------------------
     * 安全过期时间
     * ---------------------------------------------------------------
     *
     * 0 表示永不过期
     * \Leevel\Encryption\Encryption 安全过期时间
     */
    'auth_expiry' => 0,

    /**
     * ---------------------------------------------------------------
     * 默认是否带上伪静态后缀
     * ---------------------------------------------------------------
     *
     * 主要用于 url 生成
     */
    'with_suffix' => false,
    
    /**
     * ---------------------------------------------------------------
     * 伪静态后缀
     * ---------------------------------------------------------------
     *
     * 系统进行路由解析时将会去掉后缀后然后继续执行 url 解析
     */
    'html_suffix' => '.html',

    /**
     * ---------------------------------------------------------------
     * 顶级域名
     * ---------------------------------------------------------------
     *
     * 例如 queryphp.com，用于路由解析以及 \Leevel\Router\Url::make 生成
     */
    'top_domain' => env('top_domain', 'foo.bar'),

    /**
     * ---------------------------------------------------------------
     * url 生成是否开启子域名
     * ---------------------------------------------------------------
     *
     * 开启 url 子域名功能，用于 \Leevel\Router\Url::make 生成
     */
    'subdomain_on' => false,

    /**
     * ---------------------------------------------------------------
     * public　资源地址
     * ---------------------------------------------------------------
     *
     * 设置公共资源 url 地址
     */
    'public' => env('url_public', 'http://public.foo.bar')
];
