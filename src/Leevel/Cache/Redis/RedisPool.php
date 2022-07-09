<?php

declare(strict_types=1);

namespace Leevel\Cache\Redis;

use Leevel\Cache\Manager;
use Leevel\Level\Pool\IConnection;
use Leevel\Level\Pool\Pool;

/**
 * Redis 连接池.
 *
 * @codeCoverageIgnore
 */
class RedisPool extends Pool
{
    /**
     * 构造函数.
     */
    public function __construct(
        protected Manager $manager,
        protected string $redisConnect,
        array $option = [],
    ) {
        parent::__construct($option);
    }

    /**
     * {@inheritDoc}
     */
    protected function createConnection(): IConnection
    {
        $this->manager->extend('redisPoolConnection', function (Manager $manager): IConnection {
            return $manager->createRedisPoolConnection($this->redisConnect);
        });

        /** @var \Leevel\Cache\RedisPoolConnection $mysql */
        $redisPoolConnection = $this->manager->connect('redisPoolConnection', true);
        $redisPoolConnection->setShouldRelease(true);

        return $redisPoolConnection;
    }
}
