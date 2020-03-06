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
     *
     * @return mixed
     */
    public function __call(string $method, array $args)
    {
        return $this->proxy()->{$method}(...$args);
    }

    /**
     * 批量设置缓存.
     *
     * @param array|string $keys
     * @param null|mixed   $value
     */
    public function put($keys, $value = null, ?int $expire = null): void
    {
        $this->proxy()->put($keys, $value, $expire);
    }

    /**
     * 缓存存在读取否则重新设置.
     *
     * @param mixed $data
     *
     * @return mixed
     */
    public function remember(string $name, $data, ?int $expire = null)
    {
        return $this->proxy()->remember($name, $data, $expire);
    }

    /**
     * 设置配置.
     *
     * @param mixed $value
     *
     * @return \Leevel\Cache\ICache
     */
    public function setOption(string $name, $value): ICache
    {
        return $this->proxy()->setOption($name, $value);
    }

    /**
     * 获取缓存.
     *
     * @param mixed $defaults
     *
     * @return mixed
     */
    public function get(string $name, $defaults = false)
    {
        return $this->proxy()->get($name, $defaults);
    }

    /**
     * 设置缓存.
     *
     * @param mixed $data
     */
    public function set(string $name, $data, ?int $expire = null): void
    {
        $this->proxy()->set($name, $data, $expire);
    }

    /**
     * 清除缓存.
     */
    public function delete(string $name): void
    {
        $this->proxy()->delete($name);
    }

    /**
     * 缓存是否存在.
     */
    public function has(string $name): bool
    {
        return $this->proxy()->has($name);
    }

    /**
     * 自增.
     *
     * @return false|int
     */
    public function increase(string $name, int $step = 1, ?int $expire = null)
    {
        return $this->proxy()->increase($name, $step, $expire);
    }

    /**
     * 自减.
     *
     * @return false|int
     */
    public function decrease(string $name, int $step = 1, ?int $expire = null)
    {
        return $this->proxy()->decrease($name, $step, $expire);
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
        return $this->proxy()->ttl($name);
    }

    /**
     * 返回缓存句柄.
     *
     * @return mixed
     */
    public function handle()
    {
        return $this->proxy()->handle();
    }

    /**
     * 关闭.
     */
    public function close(): void
    {
        $this->proxy()->close();
    }

    /**
     * 代理.
     *
     * @return \Leevel\Cache\ICache
     */
    protected function proxy(): ICache
    {
        /** @var \Leevel\Cache\ICache $redis */
        $redis = $this->redisPool->borrowConnection();

        return $redis;
    }
}
