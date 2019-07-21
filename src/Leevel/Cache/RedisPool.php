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
 * (c) 2010-2019 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Leevel\Cache;

use Leevel\Cache\Redis\RedisPool as RedisPools;

/**
 * redis pool 缓存.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2019.07.20
 *
 * @version 1.0
 * @codeCoverageIgnore
 */
class RedisPool extends Cache implements ICache
{
    /**
     * 配置.
     *
     * @var array
     */
    protected $option = [
        'time_preset' => [],
        'expire'      => 86400,
        'serialize'   => true,
    ];

    /**
     * Redis 连接池.
     *
     * @var \Leevel\Cache\Redis\RedisPool
     */
    protected $redisPool;

    /**
     * 构造函数.
     *
     * @param \Leevel\Cache\Redis\RedisPool $redisPool
     * @param array                         $option
     */
    public function __construct(RedisPools $redisPool, array $option = [])
    {
        parent::__construct($option);

        $this->redisPool = $redisPool;
    }

    /**
     * 获取缓存.
     *
     * @param string $name
     * @param mixed  $defaults
     * @param array  $option
     *
     * @return mixed
     */
    public function get(string $name, $defaults = false, array $option = [])
    {
        $option = $this->normalizeOptions($option);
        $redis = $this->redisPool->borrowConnection();
        $data = $redis->get($name, $defaults, $option);
        $this->redisPool->returnConnection($redis);

        return $data;
    }

    /**
     * 设置缓存.
     *
     * @param string $name
     * @param mixed  $data
     * @param array  $option
     */
    public function set(string $name, $data, array $option = []): void
    {
        $option = $this->normalizeOptions($option);
        $redis = $this->redisPool->borrowConnection();
        $redis->set($name, $data, $option);
        $this->redisPool->returnConnection($redis);
    }

    /**
     * 清除缓存.
     *
     * @param string $name
     */
    public function delete(string $name): void
    {
        $redis = $this->redisPool->borrowConnection();
        $redis->delete($name);
        $this->redisPool->returnConnection($redis);
    }

    /**
     * 关闭 redis.
     */
    public function close(): void
    {
        $this->redisPool->close();
    }

    /**
     * 返回缓存句柄.
     *
     * @return mixed
     */
    public function handle()
    {
        return $this->redisPool;
    }
}
