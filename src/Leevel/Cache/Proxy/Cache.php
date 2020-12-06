<?php

declare(strict_types=1);

namespace Leevel\Cache\Proxy;

use Leevel\Cache\Manager;
use Leevel\Di\Container;

/**
 * 代理 cache.
 *
 * @method static void put($keys, $value = null, ?int $expire = null)                        批量插入.
 * @method static mixed remember(string $name, \Closure $dataGenerator, ?int $expire = null) 缓存存在读取否则重新设置.
 * @method static mixed get(string $name, $defaults = false)                                 获取缓存.
 * @method static void set(string $name, $data, ?int $expire = null)                         设置缓存.
 * @method static void delete(string $name)                                                  清除缓存.
 * @method static bool has(string $name)                                                     缓存是否存在.
 * @method static false|int increase(string $name, int $step = 1, ?int $expire = null)           自增.
 * @method static false|int decrease(string $name, int $step = 1, ?int $expire = null)           自减.
 * @method static int ttl(string $name)                                                      获取缓存剩余时间.
 * @method static mixed handle()                                                             返回缓存句柄.
 * @method static void close()                                                               关闭.
 * @method static void setKeyRegex(string $keyRegex)                                         设置缓存键值正则.
 */
class Cache
{
    /**
     * 实现魔术方法 __callStatic.
     */
    public static function __callStatic(string $method, array $args): mixed
    {
        return self::proxy()->{$method}(...$args);
    }

    /**
     * 代理服务.
     */
    public static function proxy(): Manager
    {
        return Container::singletons()->make('caches');
    }
}
