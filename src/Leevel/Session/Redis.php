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
    protected array $option = [
        'id' => null,
        'name' => null,
    ];

    /**
     * 构造函数.
     */
    public function __construct(CacheRedis $cache, array $option = [])
    {
        $this->option = array_merge($this->option, $option);
        parent::__construct($cache);
    }
}
