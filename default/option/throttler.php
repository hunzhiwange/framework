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
 * 请求频率默认配置文件
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.08.07
 * @version 1.0
 */
return [

    /**
     * ---------------------------------------------------------------
     * 请求频率缓存驱动
     * ---------------------------------------------------------------
     *
     * 这里可以可以设置为 file、memcache 等
     * 这里使用的缓存组件的中的配置
     * 系统为所有缓存提供了统一的接口，在使用上拥有一致性
     */
    'driver' => env('throttler_driver', null)
];
