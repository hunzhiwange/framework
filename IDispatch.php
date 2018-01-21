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
namespace Queryyetsimple\Event;

/**
 * IDispatch 接口
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.07.12
 * @version 1.0
 */
interface IDispatch
{

    /**
     * 执行一个事件
     *
     * @param string|object $event
     * @return void
     */
    public function run($event);

    /**
     * 注册监听器
     *
     * @param string|array $event
     * @param mixed $listener
     * @return void
     */
    public function listener($event, $listener);

    /**
     * 获取一个监听器
     *
     * @param string $event
     * @return array
     */
    public function getListener($event);

    /**
     * 判断监听器是否存在
     *
     * @param string $event
     * @return bool
     */
    public function hasListener($event);

    /**
     * 删除一个事件所有监听器
     *
     * @param string $event
     * @return void
     */
    public function deleteListener($event);
}
