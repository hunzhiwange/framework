<?php

declare(strict_types=1);

namespace Leevel\Session;

use Leevel\Support\Manager as Managers;

/**
 * Session 管理器.
 */
class Manager extends Managers implements ISession
{
    public function start(?string $sessionId = null): void
    {
        $this->__call(__FUNCTION__, [$sessionId]);
    }

    public function save(): void
    {
        $this->__call(__FUNCTION__, []);
    }

    public function setExpire(?int $expire = null): void
    {
        $this->__call(__FUNCTION__, [$expire]);
    }

    public function all(): array
    {
        // @phpstan-ignore-next-line
        return $this->__call(__FUNCTION__, []);
    }

    public function set(string $name, mixed $value): void
    {
        $this->__call(__FUNCTION__, [$name, $value]);
    }

    public function put(array|string $keys, mixed $value = null): void
    {
        $this->__call(__FUNCTION__, [$keys, $value]);
    }

    public function get(string $name, mixed $defaults = null): mixed
    {
        return $this->__call(__FUNCTION__, [$name, $defaults]);
    }

    public function delete(string $name): void
    {
        $this->__call(__FUNCTION__, [$name]);
    }

    public function has(string $name): bool
    {
        // @phpstan-ignore-next-line
        return $this->__call(__FUNCTION__, [$name]);
    }

    public function clear(): void
    {
        $this->__call(__FUNCTION__, []);
    }

    public function flash(string $key, mixed $value): void
    {
        $this->__call(__FUNCTION__, [$key, $value]);
    }

    public function flashs(array $flash): void
    {
        $this->__call(__FUNCTION__, [$flash]);
    }

    public function nowFlash(string $key, mixed $value): void
    {
        $this->__call(__FUNCTION__, [$key, $value]);
    }

    public function rebuildFlash(): void
    {
        $this->__call(__FUNCTION__, []);
    }

    public function keepFlash(array $keys): void
    {
        $this->__call(__FUNCTION__, [$keys]);
    }

    public function getFlash(string $key, mixed $defaults = null): mixed
    {
        return $this->__call(__FUNCTION__, [$key, $defaults]);
    }

    public function deleteFlash(array $keys): void
    {
        $this->__call(__FUNCTION__, [$keys]);
    }

    public function clearFlash(): void
    {
        $this->__call(__FUNCTION__, []);
    }

    public function unregisterFlash(): void
    {
        $this->__call(__FUNCTION__, []);
    }

    public function prevUrl(): ?string
    {
        // @phpstan-ignore-next-line
        return $this->__call(__FUNCTION__, []);
    }

    public function setPrevUrl(string $url): void
    {
        $this->__call(__FUNCTION__, [$url]);
    }

    public function destroySession(): void
    {
        $this->__call(__FUNCTION__, []);
    }

    public function isStart(): bool
    {
        // @phpstan-ignore-next-line
        return $this->__call(__FUNCTION__, []);
    }

    public function setName(string $name): void
    {
        $this->__call(__FUNCTION__, [$name]);
    }

    public function getName(): ?string
    {
        // @phpstan-ignore-next-line
        return $this->__call(__FUNCTION__, []);
    }

    public function setId(?string $id = null): void
    {
        $this->__call(__FUNCTION__, [$id]);
    }

    public function getId(): string
    {
        // @phpstan-ignore-next-line
        return $this->__call(__FUNCTION__, []);
    }

    public function regenerateId(): string
    {
        // @phpstan-ignore-next-line
        return $this->__call(__FUNCTION__, []);
    }

    public function open(string $savePath, string $sessionName): bool
    {
        // @phpstan-ignore-next-line
        return $this->__call(__FUNCTION__, [$savePath, $sessionName]);
    }

    public function close(): bool
    {
        // @phpstan-ignore-next-line
        return $this->__call(__FUNCTION__, []);
    }

    public function read(string $sessionId): string
    {
        // @phpstan-ignore-next-line
        return $this->__call(__FUNCTION__, [$sessionId]);
    }

    public function write(string $sessionId, string $sessionData): bool
    {
        // @phpstan-ignore-next-line
        return $this->__call(__FUNCTION__, [$sessionId, $sessionData]);
    }

    public function destroy(string $sessionId): bool
    {
        // @phpstan-ignore-next-line
        return $this->__call(__FUNCTION__, [$sessionId]);
    }

    public function gc(int $maxLifetime): int
    {
        // @phpstan-ignore-next-line
        return $this->__call(__FUNCTION__, [$maxLifetime]);
    }

    public function connect(?string $connect = null, bool $newConnect = false, ...$arguments): ISession
    {
        return parent::connect($connect, $newConnect, ...$arguments);
    }

    public function reconnect(?string $connect = null, ...$arguments): ISession
    {
        return parent::reconnect($connect, ...$arguments);
    }

    /**
     * 返回 session 配置.
     */
    public function getSessionConfig(): array
    {
        return $this->normalizeConnectConfig($this->getDefaultConnect());
    }

    /**
     * 取得配置命名空间.
     */
    protected function getConfigNamespace(): string
    {
        return 'session';
    }

    /**
     * 创建 test 缓存.
     */
    protected function makeConnectTest(string $connect, ?string $driverClass = null): Test
    {
        $driverClass = $this->getDriverClass(Test::class, $driverClass);

        return new $driverClass(
            $this->normalizeConnectConfig($connect)
        );
    }

    /**
     * 创建 file 缓存.
     */
    protected function makeConnectFile(string $connect, ?string $driverClass = null): File
    {
        $driverClass = $this->getDriverClass(File::class, $driverClass);
        $configs = $this->normalizeConnectConfig($connect);

        /** @var \Leevel\Cache\File $file */
        $file = $this->container['caches']->connect($configs['file_driver']);

        return new $driverClass($file, $configs);
    }

    /**
     * 创建 redis 缓存.
     */
    protected function makeConnectRedis(string $connect, ?string $driverClass = null): Redis
    {
        $driverClass = $this->getDriverClass(Redis::class, $driverClass);
        $configs = $this->normalizeConnectConfig($connect);

        /** @var \Leevel\Cache\Redis $redis */
        $redis = $this->container['caches']->connect($configs['redis_driver']);

        return new $driverClass($redis, $configs);
    }

    /**
     * 分析连接配置.
     */
    protected function getConnectConfig(string $connect): array
    {
        return $this->filterNullOfConfig(parent::getConnectConfig($connect));
    }
}
