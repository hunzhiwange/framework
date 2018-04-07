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
namespace Leevel\Cookie;

/**
 * ICookie 接口
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.04.23
 * @version 1.0
 */
interface ICookie
{

    /**
     * 设置 COOKIE
     *
     * @param string $name
     * @param string $value
     * @param array $option
     * @return void
     */
    public function set($name, $value = '', array $option = []);

    /**
     * 批量插入
     *
     * @param string|array $keys
     * @param mixed $value
     * @param array $option
     * @return void
     */
    public function put($keys, $value = null, array $option = []);

    /**
     * 数组插入数据
     *
     * @param string $key
     * @param mixed $value
     * @param array $option
     * @return void
     */
    public function push($key, $value, array $option = []);

    /**
     * 合并元素
     *
     * @param string $key
     * @param array $value
     * @param array $option
     * @return void
     */
    public function merge($key, array $value, array $option = []);

    /**
     * 弹出元素
     *
     * @param string $key
     * @param mixed $value
     * @param array $option
     * @return void
     */
    public function pop($key, array $value, array $option = []);

    /**
     * 数组插入键值对数据
     *
     * @param string $key
     * @param mixed $keys
     * @param mixed $value
     * @param array $option
     * @return void
     */
    public function arrays($key, $keys, $value = null, array $option = []);

    /**
     * 数组键值删除数据
     *
     * @param string $key
     * @param mixed $keys
     * @return void
     */
    public function arraysDelete($key, $keys, array $option = []);

    /**
     * 获取 cookie
     *
     * @param string $name
     * @param mixed $defaults
     * @param array $option
     * @return mixed
     */
    public function get($name, $defaults = null, array $option = []);

    /**
     * 删除 cookie
     *
     * @param string $name
     * @param array $option
     * @return void
     */
    public function delete($name, array $option = []);

    /**
     * 清空 cookie
     *
     * @param boolean $deletePrefix
     * @param array $option
     * @return void
     */
    public function clear($deletePrefix = true, array $option = []);
}
