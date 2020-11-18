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

use Leevel\Cache\Manager;
use Leevel\Protocol\Pool\IConnection;
use Leevel\Protocol\Pool\Pool;

/**
 * Redis 连接池.
 *
 * @codeCoverageIgnore
 */
class RedisPool extends Pool
{
    /**
     * 缓存管理.
     */
    protected Manager $manager;

    /**
     * Redis 连接.
    */
    protected string $redisConnect;

    /**
     * 构造函数.
     */
    public function __construct(Manager $manager, string $redisConnect, array $option = [])
    {
        parent::__construct($option);
        $this->manager = $manager;
        $this->redisConnect = $redisConnect;
    }

    /**
     * 创建连接.
     */
    protected function createConnection(): IConnection
    {
        /** @var \Leevel\Protocol\Pool\IConnection $redis */
        $redis = $this->manager->connect($this->redisConnect, true);
        $redis->setRelease(true);

        return $redis;
    }
}
