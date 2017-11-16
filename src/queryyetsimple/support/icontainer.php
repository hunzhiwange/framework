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
namespace queryyetsimple\support;

use Closure;

/**
 * icontainer 接口
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.04.13
 * @version 1.0
 */
interface icontainer
{

    /**
     * 注册到容器
     *
     * @param mixed $mixFactoryName
     * @param mixed $mixFactory
     * @param boolean $booShare
     * @return $this
     */
    public function bind($mixFactoryName, $mixFactory = null, $booShare = false);

    /**
     * 注册为实例
     *
     * @param mixed $mixFactoryName
     * @param mixed $mixFactory
     * @return void
     */
    public function instance($mixFactoryName, $mixFactory);

    /**
     * 注册单一实例
     *
     * @param string $strFactoryName
     * @param mixed $mixFactory
     * @return void
     */
    public function singleton($mixFactoryName, $mixFactory = null);

    /**
     * 创建共享的闭包
     *
     * @param \Closure $objClosure
     * @return \Closure
     */
    public function share(Closure $objClosure);

    /**
     * 设置别名
     *
     * @param array|string $mixAlias
     * @param string|null|array $mixValue
     * @return void
     */
    public function alias($mixAlias, $mixValue = null);

    /**
     * 分组注册
     *
     * @param string $strGroupName
     * @param mixed $mixGroupData
     * @return void
     */
    public function group($strGroupName, $mixGroupData);

    /**
     * 分组制造
     *
     * @param string $strGroupName
     * @param array $arrArgs
     * @return array
     */
    public function groupMake($strGroupName, array $arrArgs = []);

    /**
     * 服务容器返回对象
     *
     * @param string $strFactoryName
     * @param array $arrArgs
     * @return object|false
     */
    public function make($strFactoryName, array $arrArgs = []);

    /**
     * 实例回调自动注入
     *
     * @param callable $calClass
     * @param array $arrArgs
     * @return mixed
     */
    public function call($calClass, array $arrArgs = []);
}
