<?php

declare(strict_types=1);

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
 * ICookie 接口.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.04.23
 *
 * @version 1.0
 */
interface ICookie
{
    /**
     * 设置配置.
     *
     * @param string $name
     * @param mixed  $value
     *
     * @return $this
     */
    public function setOption(string $name, $value): self;

    /**
     * 设置 COOKIE.
     *
     * @param string $name
     * @param string $value
     * @param array  $option
     */
    public function set(string $name, $value = '', array $option = []): void;

    /**
     * 批量插入.
     *
     * @param array|string $keys
     * @param mixed        $value
     * @param array        $option
     */
    public function put($keys, $value = null, array $option = []): void;

    /**
     * 数组插入数据.
     *
     * @param string $key
     * @param mixed  $value
     * @param array  $option
     */
    public function push(string $key, $value, array $option = []): void;

    /**
     * 合并元素.
     *
     * @param string $key
     * @param array  $value
     * @param array  $option
     */
    public function merge(string $key, array $value, array $option = []): void;

    /**
     * 弹出元素.
     *
     * @param string $key
     * @param mixed  $value
     * @param array  $option
     */
    public function pop(string $key, array $value, array $option = []): void;

    /**
     * 数组插入键值对数据.
     *
     * @param string $key
     * @param mixed  $keys
     * @param mixed  $value
     * @param array  $option
     */
    public function arr(string $key, $keys, $value = null, array $option = []): void;

    /**
     * 数组键值删除数据.
     *
     * @param string $key
     * @param mixed  $keys
     */
    public function arrDelete(string $key, $keys, array $option = []): void;

    /**
     * 获取 cookie.
     *
     * @param string $name
     * @param mixed  $defaults
     * @param array  $option
     *
     * @return mixed
     */
    public function get(string $name, $defaults = null, array $option = []);

    /**
     * 删除 cookie.
     *
     * @param string $name
     * @param array  $option
     */
    public function delete(string $name, array $option = []): void;

    /**
     * 清空 cookie.
     *
     * @param array $option
     */
    public function clear(array $option = []): void;

    /**
     * 返回所有 cookie.
     *
     * @return array
     */
    public function all(): array;
}
