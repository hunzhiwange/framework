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

namespace Leevel\Cache;

use Closure;
use Leevel\Cache\Redis\RedisPool as RedisPools;

/**
 * redis pool 缓存.
 *
 * @codeCoverageIgnore
 */
class RedisPool implements ICache
{
    /**
     * Redis 连接池.
     *
     * @var \Leevel\Cache\Redis\RedisPool
     */
    protected RedisPools $redisPool;

    /**
     * 构造函数.
     */
    public function __construct(RedisPools $redisPool)
    {
        $this->redisPool = $redisPool;
    }

    /**
     * call.
     */
    public function __call(string $method, array $args): mixed
    {
        return $this->proxy()->{$method}(...$args);
    }

    /**
     * {@inheritdoc}
     */
    public function put($keys, mixed $value = null, ?int $expire = null): void
    {
        $this->proxy()->put($keys, $value, $expire);
    }

    /**
     * {@inheritdoc}
     */
    public function remember(string $name, Closure $dataGenerator, ?int $expire = null): mixed
    {
        return $this->proxy()->remember($name, $dataGenerator, $expire);
    }

    /**
     * {@inheritdoc}
     */
    public function get(string $name, mixed $defaults = false): mixed
    {
        return $this->proxy()->get($name, $defaults);
    }

    /**
     * {@inheritdoc}
     */
    public function set(string $name, mixed $data, ?int $expire = null): void
    {
        $this->proxy()->set($name, $data, $expire);
    }

    /**
     * {@inheritdoc}
     */
    public function delete(string $name): void
    {
        $this->proxy()->delete($name);
    }

    /**
     * {@inheritdoc}
     */
    public function has(string $name): bool
    {
        return $this->proxy()->has($name);
    }

    /**
     * {@inheritdoc}
     */
    public function increase(string $name, int $step = 1, ?int $expire = null): false|int
    {
        return $this->proxy()->increase($name, $step, $expire);
    }

    /**
     * {@inheritdoc}
     */
    public function decrease(string $name, int $step = 1, ?int $expire = null): false|int
    {
        return $this->proxy()->decrease($name, $step, $expire);
    }

    /**
     * {@inheritdoc}
     */
    public function ttl(string $name): int
    {
        return $this->proxy()->ttl($name);
    }

    /**
     * {@inheritdoc}
     */
    public function handle(): mixed
    {
        return $this->proxy()->handle();
    }

    /**
     * {@inheritdoc}
     */
    public function close(): void
    {
        $this->proxy()->close();
    }

    /**
     * {@inheritdoc}
     */
    public function setKeyRegex(string $keyRegex): void
    {
        $this->proxy()->setKeyRegex($keyRegex);
    }

    /**
     * 代理.
     */
    protected function proxy(): ICache
    {
        /** @var \Leevel\Cache\ICache $redis */
        $redis = $this->redisPool->borrowConnection();

        return $redis;
    }
}
