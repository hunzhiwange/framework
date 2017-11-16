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
 * mail 默认配置文件
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.08.25
 * @version 1.0
 */
return [

    /**
     * ---------------------------------------------------------------
     * mail 驱动
     * ---------------------------------------------------------------
     *
     * 采用什么方式发送邮件数据
     */
    'default' => env('mail_driver', 'smtp'),

    /**
     * ---------------------------------------------------------------
     * mail 发送地址
     * ---------------------------------------------------------------
     *
     * 必须设置邮件发送的邮箱
     */
    'global_from' => [
        'address' => null,
        'name' => null
    ],

    /**
     * ---------------------------------------------------------------
     * mail 全局接收地址
     * ---------------------------------------------------------------
     *
     * 这个可以不用设置，如果设置所有邮件都会发送一份到这个邮箱
     */
    'global_to' => [
        'address' => null,
        'name' => null
    ],

    /**
     * ---------------------------------------------------------------
     * mail 驱动连接参数
     * ---------------------------------------------------------------
     *
     * 这里为所有的 mail 驱动的连接参数，每一种不同的驱动拥有不同的配置
     * 虽然有不同的驱动，但是在使用上却有着一致性
     */
    '+connect' => [
        '+smtp' => [
            // driver
            'driver' => 'smtp',

            // smtp 主机
            'host' => env('mail_host', 'smtp.qq.com'),

            // 端口
            'port' => env('mail_port', 587),

            // 用户名
            'username' => env('mail_username'),

            // 登录密码
            'password' => env('mail_password'),

            // 加密方式
            'encryption' => env('mail_encryption', 'ssl')
        ],

        '+sendmail' => [
            // driver
            'driver' => 'sendmail',

            // 命令路径
            'path' => '/usr/sbin/sendmail -bs'
        ]
    ]
];
