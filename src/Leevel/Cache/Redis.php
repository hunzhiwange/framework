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
     * 缓存服务句柄.
     */
    protected IRedis $handle;

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
        /** @phpstan-ignore-next-line */
        $data = $this->handle->get($this->getCacheName($name));
        if (false === $data) {
            return $defaults;
        }

        return $this->decodeData($data);
    }

    /**
     * {@inheritDoc}
     */
    public function set(string $name, mixed $data, ?int $expire = null): void
    {
        $expire = $this->normalizeExpire($expire);
        $this->handle->set($this->getCacheName($name), $this->encodeData($data), $expire);
    }

    /**
     * {@inheritDoc}
     */
    public function delete(string $name): void
    {
        $this->handle->delete($this->getCacheName($name));
    }

    /**
     * {@inheritDoc}
     */
    public function has(string $name): bool
    {
        return $this->handle->has($this->getCacheName($name));
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
        return $this->handle->ttl($name);
    }

    /**
     * {@inheritDoc}
     */
    public function close(): void
    {
        $this->handle->close();
    }

    /**
     * 处理自增自减.
     */
    protected function doIncreaseOrDecrease(string $type, string $name, int $step = 1, ?int $expire = null): false|int
    {
        $name = $this->getCacheName($name);
        $expire = $this->normalizeExpire($expire);

        return $this->handle->{$type}($name, $step, $expire);
    }
}
