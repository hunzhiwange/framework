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

use Leevel\Di\Container;
use Leevel\Option\Option as BaseOption;

/**
 * 代理 option.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.06.10
 *
 * @version 1.0
 * @codeCoverageIgnore
 */
class Option implements IOption
{
    /**
     * call.
     *
     * @param string $method
     * @param array  $args
     *
     * @return mixed
     */
    public static function __callStatic(string $method, array $args)
    {
        return self::proxy()->{$method}(...$args);
    }

    /**
     * 是否存在配置.
     *
     * @param string $name
     *
     * @return bool
     */
    public static function has(string $name = 'app\\'): bool
    {
        return self::proxy()->has($name);
    }

    /**
     * 获取配置.
     *
     * @param string     $name
     * @param null|mixed $defaults
     *
     * @return mixed
     */
    public static function get(string $name = 'app\\', $defaults = null)
    {
        return self::proxy()->get($name, $defaults);
    }

    /**
     * 返回所有配置.
     *
     * @return array
     */
    public static function all(): array
    {
        return self::proxy()->all();
    }

    /**
     * 设置配置.
     *
     * @param mixed      $name
     * @param null|mixed $value
     */
    public static function set($name, $value = null): void
    {
        self::proxy()->set($name, $value);
    }

    /**
     * 删除配置.
     *
     * @param string $name
     */
    public static function delete(string $name): void
    {
        self::proxy()->delete($name);
    }

    /**
     * 初始化配置参数.
     *
     * @param null|mixed $namespaces
     */
    public static function reset($namespaces = null): void
    {
        self::proxy()->reset($namespaces);
    }

    /**
     * 代理服务
     *
     * @return \Leevel\Option\Option
     */
    public static function proxy(): BaseOption
    {
        return Container::singletons()->make('option');
    }
}
