<?php

declare(strict_types=1);

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
     * 构造函数.
     */
    public function __construct(protected Manager $manager, protected string $redisConnect, array $option = [])
    {
        parent::__construct($option);
    }

    /**
     * {@inheritDoc}
     */
    protected function createConnection(): IConnection
    {
        /** @var \Leevel\Protocol\Pool\IConnection $redis */
        $redis = $this->manager->connect($this->redisConnect, true);
        $redis->setShouldRelease(true);

        return $redis;
    }
}
