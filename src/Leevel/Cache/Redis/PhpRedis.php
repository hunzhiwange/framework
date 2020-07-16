<?php

declare(strict_types=1);

/*
 * This file is part of the ************************ package.
 * _____________                           _______________
 *  ______/     \__  _____  ____  ______  / /_  _________
 *   ____/ __   / / / / _ \/ __`\/ / __ \/ __ \/ __ \___
 *    __/ / /  / /_/ /  __/ /  \  / /_/ / / / / /_/ /__
 *      \_\ \_/\____/\___/_/   / / .___/_/ /_/ .___/
 *         \_\                /_/_/         /_/
 *
 * The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
 * (c) 2010-2020 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Leevel\Cache\Redis;

use Redis;
use RuntimeException;

/**
 * php redis 扩展缓存.
 */
class PhpRedis implements IRedis
{
    /**
     * redis 句柄.
     *
     * @var \Redis
     */
    protected ?Redis $handle = null;

    /**
     * 配置.
     *
     * @var array
     */
    protected array $option = [
        'host'        => '127.0.0.1',
        'port'        => 6379,
        'password'    => '',
        'select'      => 0,
        'timeout'     => 0,
        'persistent'  => false,
    ];

    /**
     * 构造函数.
     *
     * @throws \RuntimeException
     */
    public function __construct(array $option = [])
    {
        if (!extension_loaded('redis')) {
            // @codeCoverageIgnoreStart
            throw new RuntimeException('Redis extension must be loaded before use.');
            // @codeCoverageIgnoreEnd
        }

        $this->option = array_merge($this->option, $option);
        $this->connect();
    }

    /**
     * call.
     *
     * @return mixed
     */
    public function __call(string $method, array $args): mixed
    {
        $this->checkConnect();

        return $this->handle->{$method}(...$args);
    }

    /**
     * 返回缓存句柄.
     */
    public function handle(): ?object
    {
        return $this->handle;
    }

    /**
     * 获取缓存.
     *
     * @return mixed
     */
    public function get(string $name): mixed
    {
        $this->checkConnect();

        return $this->handle->get($name);
    }

    /**
     * 设置缓存.
     *
     * @param mixed $data
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
     * 清除缓存.
     */
    public function delete(string $name): void
    {
        $this->checkConnect();
        $this->handle->del($name);
    }

    /**
     * 缓存是否存在.
     */
    public function has(string $name): bool
    {
        $this->checkConnect();

        return 1 === $this->handle->exists($name);
    }

    /**
     * 自增.
     *
     * @return false|int
     */
    public function increase(string $name, int $step = 1, ?int $expire = null): false|int
    {
        return $this->doIncreaseOrDecrease('incrby', $name, $step, $expire);
    }

    /**
     * 自减.
     *
     * @return false|int
     */
    public function decrease(string $name, int $step = 1, ?int $expire = null): false|int
    {
        return $this->doIncreaseOrDecrease('decrby', $name, $step, $expire);
    }

    /**
     * 获取缓存剩余时间.
     *
     * - 不存在的 key:-2
     * - key 存在，但没有设置剩余生存时间:-1
     * - 有剩余生存时间的 key:剩余时间
     */
    public function ttl(string $name): int
    {
        $this->checkConnect();

        return $this->handle->ttl($name);
    }

    /**
     * 关闭 redis.
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
     *
     * @return false|int
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
            (int) ($this->option['port']),
            $this->option['timeout']
        );

        if ($this->option['password']) {
            // @codeCoverageIgnoreStart
            $this->handle->auth($this->option['password']);
            // @codeCoverageIgnoreEnd
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
    protected function createRedis(): Redis
    {
        return new Redis();
    }
}
