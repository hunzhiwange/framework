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
namespace Queryyetsimple\Option;

/**
 * IOption 接口
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.04.23
 * @version 1.0
 */
interface IOption
{

    /**
     * 是否存在配置
     *
     * @param string $name 配置键值
     * @return string
     */
    public function has($name = 'app\\');

    /**
     * 获取配置
     *
     * @param string $name 配置键值
     * @param mixed $defaults 配置默认值
     * @return string
     */
    public function get($name = 'app\\', $defaults = null);

    /**
     * 返回所有配置
     *
     * @return array
     */
    public function all();

    /**
     * 设置配置
     *
     * @param mixed $name 配置键值
     * @param mixed $value 配置值
     * @return array
     */
    public function set($name, $value = null);

    /**
     * 删除配置
     *
     * @param string $name 配置键值
     * @return string
     */
    public function delete($name);

    /**
     * 初始化配置参数
     *
     * @param mixed $namespaces
     * @return boolean
     */
    public function reset($namespaces = null);
}
