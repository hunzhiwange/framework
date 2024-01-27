<?php

declare(strict_types=1);

namespace Leevel\Auth;

use Leevel\Cache\ICache;

/**
 * Auth token.
 */
class Token extends Auth implements IAuth
{
    /**
     * 配置.
     */
    protected array $config = [
        'token' => null,
        'expire' => null,
    ];

    /**
     * 构造函数.
     */
    public function __construct(protected ICache $cache, array $config = [])
    {
        parent::__construct($config);
    }

    /**
     * 数据持久化.
     */
    protected function setPersistence(string $key, string $value, ?int $expire = null): string
    {
        $this->cache->set($key, $value, $expire);

        return $key;
    }

    /**
     * 获取持久化数据.
     */
    protected function getPersistence(string $key): mixed
    {
        return $this->cache->get($key);
    }

    /**
     * 删除持久化数据.
     */
    protected function deletePersistence(string $key): void
    {
        $this->cache->delete($key);
    }
}
