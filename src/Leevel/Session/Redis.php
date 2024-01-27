<?php

declare(strict_types=1);

namespace Leevel\Session;

use Leevel\Cache\Redis as CacheRedis;

/**
 * session.redis.
 */
class Redis extends Session implements ISession
{
    /**
     * 配置.
     */
    protected array $config = [
        'id' => null,
        'name' => null,
    ];

    /**
     * 构造函数.
     */
    public function __construct(CacheRedis $cache, array $config = [])
    {
        $this->config = array_merge($this->config, $config);
        parent::__construct($cache);
    }
}
