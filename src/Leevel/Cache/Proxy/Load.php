<?php

declare(strict_types=1);

namespace Leevel\Cache\Proxy;

use Leevel\Cache\Load as BaseLoad;
use Leevel\Di\Container;

/**
 * 代理 cache.load.
 *
 * @method static array data(array $names, ?int $expire = null, bool $force = false) 载入缓存数据.
 * @method static void refresh(array $names)                                         刷新缓存数据.
 */
class Load
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
    public static function proxy(): BaseLoad
    {
        return Container::singletons()->make('cache.load');
    }
}
