<?php

declare(strict_types=1);

namespace Leevel\Cache;

use Leevel\Support\Manager as Managers;
use Swoole\Coroutine;

/**
 * 缓存管理器.
 *
 * @method static void                  put($keys, $value = null, ?int $expire = null)                       批量插入.
 * @method static mixed                 remember(string $name, \Closure $dataGenerator, ?int $expire = null) 缓存存在读取否则重新设置.
 * @method static mixed                 get(string $name, $defaults = false)                                 获取缓存.
 * @method static void                  set(string $name, $data, ?int $expire = null)                        设置缓存.
 * @method static void                  delete(string $name)                                                 清除缓存.
 * @method static bool                  has(string $name)                                                    缓存是否存在.
 * @method static mixed                 increase(string $name, int $step = 1, ?int $expire = null)           自增.
 * @method static mixed                 decrease(string $name, int $step = 1, ?int $expire = null)           自减.
 * @method static int                   ttl(string $name)                                                    获取缓存剩余时间.
 * @method static mixed                 handle()                                                             返回缓存句柄.
 * @method static void                  close()                                                              关闭.
 * @method static void                  setKeyRegex(string $keyRegex)                                        设置缓存键值正则.
 * @method static \Leevel\Di\IContainer container()                                                          返回 IOC 容器.
 * @method static void                  disconnect(?string $connect = null)                                  删除连接.
 * @method static array                 getConnects()                                                        取回所有连接.
 * @method static string                getDefaultConnect()                                                  返回默认连接.
 * @method static void                  setDefaultConnect(string $name)                                      设置默认连接.
 * @method static mixed                 getContainerConfig(?string $name = null)                             获取容器配置值.
 * @method static void                  setContainerConfig(string $name, mixed $value)                       设置容器配置值.
 * @method static void                  extend(string $connect, \Closure $callback)                          扩展自定义连接.
 * @method static array                 normalizeConnectConfig(string $connect)                              整理连接配置.
 */
class Manager extends Managers
{
    /**
     * 缓存连接池.
     */
    protected array $pools = [];

    /**
     * {@inheritDoc}
     */
    public function connect(?string $connect = null, bool $newConnect = false, ...$arguments): ICache
    {
        // 协程环境每次从创建驱动中获取连接
        if ($this->container->enabledCoroutine()) {
            $newConnect = true;
        }

        return parent::connect($connect, $newConnect, ...$arguments);
    }

    /**
     * {@inheritDoc}
     */
    public function reconnect(?string $connect = null, ...$arguments): ICache
    {
        return parent::reconnect($connect, ...$arguments);
    }

    /**
     * 取得配置命名空间.
     */
    protected function getConfigNamespace(): string
    {
        return 'cache';
    }

    /**
     * 创建文件缓存.
     */
    protected function makeConnectFile(string $connect, ?string $driverClass = null): File
    {
        $driverClass = $this->getDriverClass(File::class, $driverClass);

        return new $driverClass(
            $this->normalizeConnectConfig($connect)
        );
    }

    /**
     * 创建 redis 缓存.
     */
    protected function makeConnectRedis(string $connect, ?string $driverClass = null, bool $newConnect = false): Redis
    {
        $configs = $this->normalizeConnectConfig($connect);
        $enabledCoroutine = $this->container->enabledCoroutine();
        if (!$newConnect && $enabledCoroutine) {
            return $this->getConnectionFromFool($configs, $connect);
        }

        return $this->createRedis($configs, $driverClass);
    }

    protected function createRedis(array $configs, ?string $driverClass = null): Redis
    {
        $driverClass = $this->getDriverClass(Redis::class, $driverClass);

        return new $driverClass($configs);
    }

    /**
     * 创建连接池.
     */
    protected function getPool(string $connect, array $configs): Pool
    {
        if (isset($this->pools[$connect])) {
            return $this->pools[$connect];
        }

        return $this->pools[$connect] = new Pool($this, $connect, $configs);
    }

    protected function getConnectionFromFool(array $configs, string $connect): ICache
    {
        $pool = $this->getPool($connect, $configs);
        $connection = $pool->get();

        // 协程关闭前归还当前连接到缓存连接池
        Coroutine::defer(fn () => $connection->releaseConnect());

        return $connection;
    }

    /**
     * 分析连接配置.
     */
    protected function getConnectConfig(string $connect): array
    {
        return $this->filterNullOfConfig(
            parent::getConnectConfig($connect)
        );
    }
}
