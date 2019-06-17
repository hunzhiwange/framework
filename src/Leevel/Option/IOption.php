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

namespace Leevel\Option;

/**
 * IOption 接口.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.04.23
 *
 * @version 1.0
 *
 * @see \Leevel\Option\Proxy\IOption 请保持接口设计的一致
 */
interface IOption
{
    /**
     * 默认命名空间.
     *
     * @var string
     */
    const DEFAUTL_NAMESPACE = 'app';

    /**
     * 是否存在配置.
     *
     * @param string $name
     *
     * @return bool
     */
    public function has(string $name = 'app\\'): bool;

    /**
     * 获取配置.
     *
     * @param string     $name
     * @param null|mixed $defaults
     *
     * @return mixed
     */
    public function get(string $name = 'app\\', $defaults = null);

    /**
     * 返回所有配置.
     *
     * @return array
     */
    public function all(): array;

    /**
     * 设置配置.
     *
     * @param mixed      $name
     * @param null|mixed $value
     */
    public function set($name, $value = null): void;

    /**
     * 删除配置.
     *
     * @param string $name
     */
    public function delete(string $name): void;

    /**
     * 初始化配置参数.
     *
     * @param null|mixed $namespaces
     */
    public function reset($namespaces = null): void;
}
