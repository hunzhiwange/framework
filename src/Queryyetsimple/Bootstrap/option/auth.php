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
 * (c) 2010-2018 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * auth 默认配置文件
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.09.07
 * @version 1.0
 */
return [

    /**
     * ---------------------------------------------------------------
     * 默认认证类型
     * ---------------------------------------------------------------
     *
     * 这里可以是 web 或者 api
     */
    'default' => 'web',

    /**
     * ---------------------------------------------------------------
     * 默认 WEB 驱动
     * ---------------------------------------------------------------
     *
     * WEB 认证驱动连接
     */
    'web_default' => 'session',

    /**
     * ---------------------------------------------------------------
     * 默认 API 驱动
     * ---------------------------------------------------------------
     *
     * API 认证驱动连接
     */
    'api_default' => 'token',

    /**
     * ---------------------------------------------------------------
     * auth 连接参数
     * ---------------------------------------------------------------
     *
     * 这里为所有的 auth 的连接参数，每一种不同的驱动拥有不同的配置
     * 虽然有不同的驱动，但是在日志使用上却有着一致性
     */
    '+connect' => [
        '+session' => [
            // driver
            'driver' => 'session',

            // 模型
            'model' => common\domain\entity\user::class,

            // 用户信息持久化名字
            'user_persistence' => 'user_persistence',

            // Token 持久化名字
            'token_persistence' => 'token_persistence',

            // 查询字段
            'field' => 'id,name,nikename,email,mobile'
        ],

        '+token' => [
            // driver
            'driver' => 'token',

            // 模型
            'model' => common\domain\entity\user::class,

            // 用户信息持久化名字
            'user_persistence' => 'user_persistence',

            // Token 持久化名字
            'token_persistence' => 'token_persistence',

            // 查询字段
            'field' => 'id,name,nikename,email,mobile'
        ]
    ]
];
