<?php

declare(strict_types=1);

namespace Leevel\Cache\Proxy;

use Leevel\Cache\Manager;
use Leevel\Di\Container;

/**
 * 代理 cache.
 *
 * @method static void                  put($keys, $value = null, ?int $expire = null)                       批量插入.
 * @method static mixed                 remember(string $name, \Closure $dataGenerator, ?int $expire = null) 缓存存在读取否则重新设置.
 * @method static mixed                 get(string $name, $defaults = false)                                 获取缓存.
 * @method static void                  set(string $name, $data, ?int $expire = null)                        设置缓存.
 * @method static void                  delete(string $name)                                                 清除缓存.
 * @method static bool                  has(string $name)                                                    缓存是否存在.
 * @method static false|int             increase(string $name, int $step = 1, ?int $expire = null)           自增.
 * @method static false|int             decrease(string $name, int $step = 1, ?int $expire = null)           自减.
 * @method static int                   ttl(string $name)                                                    获取缓存剩余时间.
 * @method static mixed                 handle()                                                             返回缓存句柄.
 * @method static void                  close()                                                              关闭.
 * @method static void                  setKeyRegex(string $keyRegex)                                        设置缓存键值正则.
 * @method static \Leevel\Di\IContainer container()                                                          返回 IOC 容器.
 * @method static \Leevel\Cache\ICache  connect(?string $connect = null, bool $newConnect = false)           连接并返回连接对象.
 * @method static \Leevel\Cache\ICache  reconnect(?string $connect = null)                                   重新连接.
 * @method static void                  disconnect(?string $connect = null)                                  删除连接.
 * @method static array                 getConnects()                                                        取回所有连接.
 * @method static string                getDefaultConnect()                                                  返回默认连接.
 * @method static void                  setDefaultConnect(string $name)                                      设置默认连接.
 * @method static mixed                 getContainerOption(?string $name = null)                             获取容器配置值.
 * @method static void                  setContainerOption(string $name, mixed $value)                       设置容器配置值.
 * @method static void                  extend(string $connect, \Closure $callback)                          扩展自定义连接.
 * @method static array                 normalizeConnectOption(string $connect)                              整理连接配置.
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
