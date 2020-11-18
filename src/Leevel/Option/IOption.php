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
 * (c) 2010-2020 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Leevel\Option;

/**
 * IOption 接口.
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
     */
    public function has(string $name = 'app\\'): bool;

    /**
     * 获取配置.
     */
    public function get(string $name = 'app\\', mixed $defaults = null): mixed;

    /**
     * 返回所有配置.
     */
    public function all(): array;

    /**
     * 设置配置.
     */
    public function set(mixed $name, mixed  $value = null): void;

    /**
     * 删除配置.
     */
    public function delete(string $name): void;

    /**
     * 初始化配置参数.
     *
     * @param mixed $namespaces
     */
    public function reset(mixed $namespaces = null): void;
}
