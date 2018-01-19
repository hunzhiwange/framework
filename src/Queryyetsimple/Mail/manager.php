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
namespace queryyetsimple\mail;

use queryyetsimple\support\manager as support_manager;

/**
 * mail 入口
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.08.26
 * @version 1.0
 */
class manager extends support_manager
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
        return new mail($connect, $this->container['view'], $this->container['event'], $this->getOptionCommon());
    }

    /**
     * 创建 smtp 连接
     *
     * @param array $options
     * @return \queryyetsimple\mail\smtp
     */
    protected function makeConnectSmtp($options = [])
    {
        return new smtp(array_merge($this->getOption('smtp', $options)));
    }

    /**
     * 创建 sendmail 连接
     *
     * @param array $options
     * @return \queryyetsimple\mail\sendmail
     */
    protected function makeConnectSendmail($options = [])
    {
        return new sendmail(array_merge($this->getOption('sendmail', $options)));
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
