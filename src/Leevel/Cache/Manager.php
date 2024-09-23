<?php

declare(strict_types=1);

namespace Leevel\Cache;

use Leevel\Server\Pool\Pool as BasePool;
use Leevel\Support\Manager as Managers;
use Swoole\Coroutine;

/**
 * 缓存管理器.
 */
class Manager extends Managers implements ICache
{
    /**
     * 缓存连接池.
     */
    protected array $pools = [];

    public function put(array|string $keys, mixed $value = null, ?int $expire = null): void
    {
        $this->callConnectMethod(__FUNCTION__, $keys, $value, $expire);
    }

    public function remember(string $name, \Closure $dataGenerator, ?int $expire = null): mixed
    {
        return $this->callConnectMethod(__FUNCTION__, $name, $dataGenerator, $expire);
    }

    public function get(string $name, mixed $defaults = false): mixed
    {
        return $this->callConnectMethod(__FUNCTION__, $name, $defaults);
    }

    public function set(string $name, mixed $data, ?int $expire = null): void
    {
        $this->callConnectMethod(__FUNCTION__, $name, $data, $expire);
    }

    public function delete(string $name): void
    {
        $this->callConnectMethod(__FUNCTION__, $name);
    }

    public function has(string $name): bool
    {
        // @phpstan-ignore-next-line
        return $this->callConnectMethod(__FUNCTION__, $name);
    }

    public function increase(string $name, int $step = 1, ?int $expire = null): false|int
    {
        // @phpstan-ignore-next-line
        return $this->callConnectMethod(__FUNCTION__, $name, $step, $expire);
    }

    public function decrease(string $name, int $step = 1, ?int $expire = null): false|int
    {
        // @phpstan-ignore-next-line
        return $this->callConnectMethod(__FUNCTION__, $name, $step, $expire);
    }

    public function ttl(string $name): int
    {
        // @phpstan-ignore-next-line
        return $this->callConnectMethod(__FUNCTION__, $name);
    }

    public function close(): void
    {
        $this->callConnectMethod(__FUNCTION__);
    }

    public function setKeyRegex(string $keyRegex): void
    {
        $this->callConnectMethod(__FUNCTION__, $keyRegex);
    }

    public function getHandle(): ?object
    {
        // @phpstan-ignore-next-line
        return $this->callConnectMethod(__FUNCTION__);
    }

    public function release(): void
    {
        $this->callConnectMethod(__FUNCTION__);
    }

    public function setPool(BasePool $pool): void
    {
        $this->callConnectMethod(__FUNCTION__, $pool);
    }

    public function connect(?string $connect = null, bool $newConnect = false, mixed ...$arguments): ICache
    {
        // 协程环境每次从创建驱动中获取连接
        if ($this->container->enabledCoroutine()) {
            $newConnect = true;
        }

        return parent::connect($connect, $newConnect, ...$arguments);
    }

    public function reconnect(?string $connect = null, mixed ...$arguments): ICache
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
            // @phpstan-ignore-next-line
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
        Coroutine::defer(static fn () => $connection->release());

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
