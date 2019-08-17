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
class RedisPool implements ICache
{
    use Proxy;

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
     */
    public function __construct(RedisPools $redisPool)
    {
        $this->redisPool = $redisPool;
    }

    /**
     * 返回代理.
     *
     * @return \Leevel\Cache\ICache
     */
    public function proxy(): ICache
    {
        /** @var \Leevel\Cache\ICache $redis */
        $redis = $this->redisPool->borrowConnection();

        return $redis;
    }
}
