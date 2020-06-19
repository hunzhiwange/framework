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

namespace Leevel\Cache\Proxy;

use Closure;
use Leevel\Cache\ICache as IBaseCache;
use Leevel\Cache\Manager;
use Leevel\Di\Container;

/**
 * 代理 cache.
 *
 * @codeCoverageIgnore
 */
class Cache
{
    /**
     * call.
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
    public static function put($keys, $value = null, ?int $expire = null): void
    {
        self::proxy()->put($keys, $value, $expire);
    }

    /**
     * 缓存存在读取否则重新设置.
     *
     * @return mixed
     */
    public static function remember(string $name, Closure $dataGenerator, ?int $expire = null)
    {
        return self::proxy()->remember($name, $dataGenerator, $expire);
    }

    /**
     * 设置配置.
     *
     * @param mixed $value
     */
    public static function setOption(string $name, $value): IBaseCache
    {
        return self::proxy()->setOption($name, $value);
    }

    /**
     * 获取缓存.
     *
     * @param mixed $defaults
     *
     * @return mixed
     */
    public static function get(string $name, $defaults = false)
    {
        return self::proxy()->get($name, $defaults);
    }

    /**
     * 设置缓存.
     *
     * @param mixed $data
     */
    public static function set(string $name, $data, ?int $expire = null): void
    {
        self::proxy()->set($name, $data, $expire);
    }

    /**
     * 清除缓存.
     */
    public static function delete(string $name): void
    {
        self::proxy()->delete($name);
    }

    /**
     * 缓存是否存在.
     */
    public static function has(string $name): bool
    {
        return self::proxy()->has($name);
    }

    /**
     * 自增.
     *
     * @return false|int
     */
    public static function increase(string $name, int $step = 1, ?int $expire = null)
    {
        return self::proxy()->increase($name, $step, $expire);
    }

    /**
     * 自减.
     *
     * @return false|int
     */
    public static function decrease(string $name, int $step = 1, ?int $expire = null)
    {
        return self::proxy()->decrease($name, $step, $expire);
    }

    /**
     * 获取缓存剩余时间.
     *
     * - 不存在的 key:-2
     * - key 存在，但没有设置剩余生存时间:-1
     * - 有剩余生存时间的 key:剩余时间
     */
    public static function ttl(string $name): int
    {
        return self::proxy()->ttl($name);
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
     * 设置缓存键值正则.
     */
    public static function setKeyRegex(string $keyRegex): void
    {
        self::proxy()->setKeyRegex($keyRegex);
    }

    /**
     * 代理服务.
     */
    public static function proxy(): Manager
    {
        return Container::singletons()->make('caches');
    }
}
