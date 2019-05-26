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
 * (c) 2010-2019 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Leevel\Option\Proxy;

/**
 * 代理 option 接口.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2019.05.26
 *
 * @version 1.0
 *
 * @see \Leevel\Option\IOption 请保持接口设计的一致
 */
interface IOption
{
    /**
     * 是否存在配置.
     *
     * @param string $name
     *
     * @return bool
     */
    public static function has(string $name = 'app\\'): bool;

    /**
     * 获取配置.
     *
     * @param string $name
     * @param mixed  $defaults
     *
     * @return mixed
     */
    public static function get(string $name = 'app\\', $defaults = null);

    /**
     * 返回所有配置.
     *
     * @return array
     */
    public static function all(): array;

    /**
     * 设置配置.
     *
     * @param mixed $name
     * @param mixed $value
     */
    public static function set($name, $value = null): void;

    /**
     * 删除配置.
     *
     * @param string $name
     */
    public static function delete(string $name): void;

    /**
     * 初始化配置参数.
     *
     * @param mixed $namespaces
     */
    public static function reset($namespaces = null): void;
}
