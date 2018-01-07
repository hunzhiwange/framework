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
namespace queryyetsimple\log;

use queryyetsimple\support\manager as support_manager;

/**
 * log 入口
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.02.15
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
        return 'log';
    }

    /**
     * 创建连接对象
     *
     * @param object $objConnect
     * @return object
     */
    protected function createConnect($objConnect)
    {
        return new log($objConnect, $this->getOptionCommon());
    }

    /**
     * 创建 file 日志驱动
     *
     * @param array $arrOption
     * @return \queryyetsimple\log\file
     */
    protected function makeConnectFile($arrOption = [])
    {
        return new file($this->getOption('file', $arrOption));
    }

    /**
     * 创建 monolog 日志驱动
     *
     * @param array $arrOption
     * @return \queryyetsimple\log\monolog
     */
    protected function makeConnectMonolog($arrOption = [])
    {
        return new monolog($this->getOption('monolog', $arrOption));
    }
}
