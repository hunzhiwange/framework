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
namespace Queryyetsimple\Mail;

use Queryyetsimple\Manager\Manager as Managers;

/**
 * mail 入口
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.08.26
 * @version 1.0
 */
class Manager extends Managers
{

    /**
     * 取得配置命名空间
     *
     * @return string
     */
    protected function getOptionNamespace()
    {
        return 'mail';
    }

    /**
     * 创建连接对象
     *
     * @param object $connect
     * @return object
     */
    protected function createConnect($connect)
    {
        return new Mail($connect, $this->container['view'], $this->container['event'], $this->getOptionCommon());
    }

    /**
     * 创建 smtp 连接
     *
     * @param array $options
     * @return \Queryyetsimple\Mail\smtp
     */
    protected function makeConnectSmtp($options = [])
    {
        return new Smtp($this->getOption('smtp', $options));
    }

    /**
     * 创建 sendmail 连接
     *
     * @param array $options
     * @return \Queryyetsimple\Mail\sendmail
     */
    protected function makeConnectSendmail($options = [])
    {
        return new Sendmail($this->getOption('sendmail', $options));
    }

    /**
     * 过滤全局配置项
     *
     * @return array
     */
    protected function filterOptionCommonItem()
    {
        return [
            'default',
            'connect',
            'from',
            'to'
        ];
    }
}
