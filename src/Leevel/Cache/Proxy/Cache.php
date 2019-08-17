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

namespace Leevel\Cache\Proxy;

use Leevel\Cache\ICache as IBaseCache;
use Leevel\Cache\Manager;
use Leevel\Di\Container;

/**
 * 代理 cache.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.06.10
 *
 * @version 1.0
 * @codeCoverageIgnore
 */
class Cache implements ICache
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
     * 批量插入.
     *
     * @param array|string $keys
     * @param null|mixed   $value
     */
    public static function put($keys, $value = null): void
    {
        self::proxy()->put($keys, $value);
    }

    /**
     * 缓存存在读取否则重新设置.
     *
     * @param string $name
     * @param mixed  $data
     * @param array  $option
     *
     * @return mixed
     */
    public static function remember(string $name, $data, array $option = [])
    {
        return self::proxy()->remember($name, $data, $option);
    }

    /**
     * 设置配置.
     *
     * @param string $name
     * @param mixed  $value
     *
     * @return \Leevel\Cache\ICache
     */
    public static function setOption(string $name, $value): IBaseCache
    {
        return self::proxy()->setOption($name, $value);
    }

    /**
     * 获取缓存.
     *
     * @param string $name
     * @param mixed  $defaults
     * @param array  $option
     *
     * @return mixed
     */
    public static function get(string $name, $defaults = false, array $option = [])
    {
        return self::proxy()->get($name, $defaults, $option);
    }

    /**
     * 设置缓存.
     *
     * @param string $name
     * @param mixed  $data
     * @param array  $option
     */
    public static function set(string $name, $data, array $option = []): void
    {
        self::proxy()->set($name, $data, $option);
    }

    /**
     * 清除缓存.
     *
     * @param string $name
     */
    public static function delete(string $name): void
    {
        self::proxy()->delete($name);
    }

    /**
     * 返回缓存句柄.
     *
     * @return mixed
     */
    public static function handle()
    {
        return self::proxy()->handle();
    }

    /**
     * 关闭.
     */
    public static function close(): void
    {
        self::proxy()->close();
    }

    /**
     * 代理服务
     *
     * @return \Leevel\Cache\Manager
     */
    public static function proxy(): Manager
    {
        return Container::singletons()->make('caches');
    }
}
