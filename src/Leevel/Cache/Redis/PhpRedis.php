<?php

declare(strict_types=1);

namespace Leevel\Cache\Redis;

use Redis;

/**
 * php redis 扩展缓存.
 */
class PhpRedis implements IRedis
{
    /**
     * redis 句柄.
     */
    protected ?\Redis $handle = null;

    /**
     * 配置.
     */
    protected array $option = [
        'host' => '127.0.0.1',
        'port' => 6379,
        'password' => '',
        'select' => 0,
        'timeout' => 0,
        'persistent' => false,
    ];

    /**
     * 构造函数.
     *
     * @throws \RuntimeException
     */
    public function __construct(array $option = [])
    {
        $this->option = array_merge($this->option, $option);
        $this->connect();
    }

    /**
     * 实现魔术方法 __call.
     */
    public function __call(string $method, array $args): mixed
    {
        $this->checkConnect();

        return $this->handle->{$method}(...$args);
    }

    /**
     * {@inheritDoc}
     */
    public function handle(): ?object
    {
        return $this->handle;
    }

    /**
     * {@inheritDoc}
     */
    public function get(string $name): mixed
    {
        $this->checkConnect();

        return $this->handle->get($name);
    }

    /**
     * {@inheritDoc}
     */
    public function set(string $name, mixed $data, int $expire = 0): void
    {
        $this->checkConnect();

        if ($expire) {
            $this->handle->setex($name, $expire, $data);
        } else {
            $this->handle->set($name, $data);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function delete(string $name): void
    {
        $this->checkConnect();
        $this->handle->del($name);
    }

    /**
     * {@inheritDoc}
     */
    public function has(string $name): bool
    {
        $this->checkConnect();

        return 1 === $this->handle->exists($name);
    }

    /**
     * {@inheritDoc}
     */
    public function increase(string $name, int $step = 1, ?int $expire = null): false|int
    {
        return $this->doIncreaseOrDecrease('incrby', $name, $step, $expire);
    }

    /**
     * {@inheritDoc}
     */
    public function decrease(string $name, int $step = 1, ?int $expire = null): false|int
    {
        return $this->doIncreaseOrDecrease('decrby', $name, $step, $expire);
    }

    /**
     * {@inheritDoc}
     */
    public function ttl(string $name): int
    {
        $this->checkConnect();

        return $this->handle->ttl($name);
    }

    /**
     * {@inheritDoc}
     */
    public function close(): void
    {
        if (!$this->handle) {
            return;
        }

        $this->handle->close();
        $this->handle = null;
    }

    /**
     * 处理自增自减.
     */
    protected function doIncreaseOrDecrease(string $type, string $name, int $step = 1, ?int $expire = null): false|int
    {
        $this->checkConnect();
        $newName = false === $this->handle->get($name);
        if ($newName && $expire) {
            $this->handle->setex($name, $expire, $result = 'incrby' === $type ? $step : -$step);
        } else {
            $result = $this->handle->{$type}($name, $step);
        }

        return $result;
    }

    /**
     * 连接 Redis.
     */
    protected function connect(): void
    {
        $this->handle = $this->createRedis();
        $this->handle->{$this->option['persistent'] ? 'pconnect' : 'connect'}(
            $this->option['host'],
            (int) $this->option['port'],
            $this->option['timeout']
        );

        if ($this->option['password']) {
            $this->handle->auth($this->option['password']);
        }

        if ($this->option['select']) {
            $this->handle->select($this->option['select']);
        }
    }

    /**
     * 校验是否连接.
     */
    protected function checkConnect(): void
    {
        if (!$this->handle) {
            $this->connect();
        }
    }

    /**
     * 返回 redis 对象.
     */
    protected function createRedis(): \Redis
    {
        return new \Redis();
    }
}
