<?php

declare(strict_types=1);

namespace Leevel\Cache;

use Leevel\Cache\Redis\IRedis;

/**
 * redis 扩展缓存.
 */
class Redis extends Cache implements ICache
{
    /**
     * 配置.
     */
    protected array $option = [
        'expire' => 86400,
    ];

    /**
     * 构造函数.
     */
    public function __construct(IRedis $handle, array $option = [])
    {
        parent::__construct($option);
        $this->handle = $handle;
    }

    /**
     * {@inheritDoc}
     */
    public function get(string $name, mixed $defaults = false): mixed
    {
        $data = $this->handle->get($this->getCacheName($name));
        if (false === $data) {
            return $defaults;
        }
        $data = $this->decodeData($data);

        $this->releaseConnect();

        return $data;
    }

    /**
     * {@inheritDoc}
     */
    public function set(string $name, mixed $data, ?int $expire = null): void
    {
        $expire = $this->normalizeExpire($expire);
        $this->handle->set($this->getCacheName($name), $this->encodeData($data), $expire);
        $this->releaseConnect();
    }

    /**
     * {@inheritDoc}
     */
    public function delete(string $name): void
    {
        $this->handle->delete($this->getCacheName($name));
        $this->releaseConnect();
    }

    /**
     * {@inheritDoc}
     */
    public function has(string $name): bool
    {
        $result = $this->handle->has($this->getCacheName($name));
        $this->releaseConnect();

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function increase(string $name, int $step = 1, ?int $expire = null): false|int
    {
        return $this->doIncreaseOrDecrease('increase', $name, $step, $expire);
    }

    /**
     * {@inheritDoc}
     */
    public function decrease(string $name, int $step = 1, ?int $expire = null): false|int
    {
        return $this->doIncreaseOrDecrease('decrease', $name, $step, $expire);
    }

    /**
     * {@inheritDoc}
     */
    public function ttl(string $name): int
    {
        $result = $this->handle->ttl($name);
        $this->releaseConnect();

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function close(): void
    {
        $this->handle->close();
    }

    /**
     * 归还连接到连接池.
     *
     * - 预留接口用于数据连接池
     */
    protected function releaseConnect(): void
    {
        if (!method_exists($this, 'release')) {
            return;
        }

        // Redis 连接池驱动 \Leevel\Cache\RedisPoolConnection 需要实现 \Leevel\Protocol\Pool\IConnection
        // 归还连接池方法为 \Leevel\Protocol\Pool\IConnection::release
        // Redis 非连接池驱动不支持释放
        $this->release();
    }

    /**
     * 处理自增自减.
     */
    protected function doIncreaseOrDecrease(string $type, string $name, int $step = 1, ?int $expire = null): false|int
    {
        $name = $this->getCacheName($name);
        $expire = $this->normalizeExpire($expire);
        $result = $this->handle->{$type}($name, $step, $expire);
        $this->releaseConnect();

        return $result;
    }
}
