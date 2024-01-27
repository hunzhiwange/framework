<?php

declare(strict_types=1);

namespace Leevel\Cache;

/**
 * redis 扩展缓存.
 */
class Redis extends Cache implements ICache
{
    /**
     * 缓存服务句柄.
     */
    protected ?\Redis $handle = null;

    /**
     * 配置.
     */
    protected array $config = [
        'host' => '127.0.0.1',
        'port' => 6379,
        'password' => '',
        'select' => 0,
        'timeout' => 0,
        'persistent' => false,
        'expire' => 86400,
    ];

    /**
     * 构造函数.
     */
    public function __construct(array $config = [])
    {
        parent::__construct($config);
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
    public function get(string $name, mixed $defaults = false): mixed
    {
        $this->checkConnect();

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
        $this->checkConnect();
        $expire = $this->normalizeExpire($expire);
        $name = $this->getCacheName($name);
        $data = $this->encodeData($data);

        if ($expire) {
            // @phpstan-ignore-next-line
            $this->handle->setex($name, $expire, $data);
        } else {
            // @phpstan-ignore-next-line
            $this->handle->set($name, $data);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function delete(string $name): void
    {
        $this->checkConnect();

        $this->handle->del($this->getCacheName($name));
    }

    /**
     * {@inheritDoc}
     */
    public function has(string $name): bool
    {
        $this->checkConnect();

        return 1 === $this->handle->exists($this->getCacheName($name));
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

        return (int) $this->handle->ttl($name);
    }

    /**
     * {@inheritDoc}
     */
    public function close(): void
    {
        if (!$this->handle) {
            return;
        }

        // @phpstan-ignore-next-line
        $this->handle->close();
        $this->handle = null;
    }

    /**
     * {@inheritDoc}
     *
     * @todo 是否返回 handle
     */
    public function getHandle(): ?\Redis
    {
        return $this->handle;
    }

    /**
     * 处理自增自减.
     */
    protected function doIncreaseOrDecrease(string $type, string $name, int $step = 1, ?int $expire = null): false|int
    {
        $this->checkConnect();

        $name = $this->getCacheName($name);
        $expire = $this->normalizeExpire($expire);

        /** @phpstan-ignore-next-line */
        $newName = false === $this->handle->get($name);
        if ($newName && $expire) {
            // @phpstan-ignore-next-line
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
        $this->handle->{$this->config['persistent'] ? 'pconnect' : 'connect'}(
            $this->config['host'],
            (int) $this->config['port'],
            $this->config['timeout']
        );

        if ($this->config['password']) {
            $this->handle->auth($this->config['password']);
        }

        if ($this->config['select']) {
            $this->handle->select($this->config['select']);
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
