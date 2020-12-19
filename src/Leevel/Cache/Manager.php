<?php

declare(strict_types=1);

namespace Leevel\Cache;

use Leevel\Manager\Manager as Managers;
use RuntimeException;
use Leevel\Cache\Redis\RedisPool as RedisPools;

/**
 * 缓存管理器.
 *
 * @method static void put($keys, $value = null, ?int $expire = null)                        批量插入.
 * @method static mixed remember(string $name, \Closure $dataGenerator, ?int $expire = null) 缓存存在读取否则重新设置.
 * @method static mixed get(string $name, $defaults = false)                                 获取缓存.
 * @method static void set(string $name, $data, ?int $expire = null)                         设置缓存.
 * @method static void delete(string $name)                                                  清除缓存.
 * @method static bool has(string $name)                                                     缓存是否存在.
 * @method static mixed increase(string $name, int $step = 1, ?int $expire = null)           自增.
 * @method static mixed decrease(string $name, int $step = 1, ?int $expire = null)           自减.
 * @method static int ttl(string $name)                                                      获取缓存剩余时间.
 * @method static mixed handle()                                                             返回缓存句柄.
 * @method static void close()                                                               关闭.
 * @method static void setKeyRegex(string $keyRegex)                                         设置缓存键值正则.
 * @method static \Leevel\Di\IContainer container() 返回 IOC 容器. 
 * @method static \Leevel\Cache\ICache connect(?string $connect = null, bool $onlyNew = false) 连接并返回连接对象. 
 * @method static \Leevel\Cache\ICache reconnect(?string $connect = null) 重新连接. 
 * @method static void disconnect(?string $connect = null) 删除连接. 
 * @method static array getConnects() 取回所有连接. 
 * @method static string getDefaultConnect() 返回默认连接. 
 * @method static void setDefaultConnect(string $name) 设置默认连接. 
 * @method static mixed getContainerOption(?string $name = null) 获取容器配置值. 
 * @method static void setContainerOption(string $name, mixed $value) 设置容器配置值. 
 * @method static void extend(string $connect, \Closure $callback) 扩展自定义连接. 
 * @method static array normalizeConnectOption(string $connect) 整理连接配置. 
 */
class Manager extends Managers
{
    /**
     * {@inheritDoc}
     */
    public function connect(?string $connect = null, bool $onlyNew = false): ICache 
    {
        if (!$connect) {
            $connect = $this->getDefaultConnect();
        }
        
        // 连接中带有 Pool 表示连接池驱动
        // 连接池驱动每次需要从池子取到连接，不能够进行缓存
        if (str_contains($connect, 'Pool')) {
            $onlyNew = true;
        }

        return parent::connect($connect, $onlyNew);
    }

    /**
     * {@inheritDoc}
     */
    public function reconnect(?string $connect = null): ICache 
    {
        return parent::reconnect($connect);
    }

    /**
     * 创建 Redis 连接池连接.
     */
    public function createRedisPoolConnection(string $connect): RedisPoolConnection
    {
        return $this->makeConnectRedis($connect, RedisPoolConnection::class);
    }

    /**
     * 取得配置命名空间.
     */
    protected function getOptionNamespace(): string
    {
        return 'cache';
    }

    /**
     * 创建文件缓存.
     */
    protected function makeConnectFile(string $connect): File
    {
        return new File(
            $this->normalizeConnectOption($connect)
        );
    }

    /**
     * 创建 redis 缓存.
     */
    protected function makeConnectRedis(string $connect, ?string $driver = null): Redis
    {
        $options = $this->normalizeConnectOption($connect);
        $driver = $driver ?? Redis::class;

        return new $driver($this->container->make('redis'), $options);
    }

    /**
     * 创建 redisPool 连接.
     *
     * @throws \RuntimeException
     */
    protected function makeConnectRedisPool(): RedisPoolConnection
    {
        if (!$this->container->getCoroutine()) {
            $e = 'Redis pool can only be used in swoole scenarios.';

            throw new RuntimeException($e);
        }

        return $this->createRedisPool()->borrowConnection();
    }

    /**
     * 创建 Redis 连接池.
     */
    protected function createRedisPool(): RedisPools
    {
        return $this->container->make('redis.pool');
    }

    /**
     * 分析连接配置.
     */
    protected function getConnectOption(string $connect): array
    {
        return $this->filterNullOfOption(
            parent::getConnectOption($connect)
        );
    }
}
