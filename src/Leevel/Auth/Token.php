<?php

declare(strict_types=1);

namespace Leevel\Auth;

use Leevel\Cache\ICache;
use Leevel\Http\Request;

/**
 * Auth token.
 */
class Token extends Auth implements IAuth
{
    /**
     * 配置.
     */
    protected array $option = [
        'token'       => null,
        'input_token' => 'token',
        'expire'      => null,
    ];

    /**
     * 构造函数.
     */
    public function __construct(protected ICache $cache, protected Request $request, array $option = [])
    {
        parent::__construct($option);
        $this->option['token'] = $this->getTokenNameFromRequest();
    }

    /**
     * 数据持久化.
     */
    protected function setPersistence(string $key, string $value, ?int $expire = null): void
    {
        $this->cache->set($key, $value, $expire);
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

    /**
     * 从请求中获取 token.
     */
    protected function getTokenNameFromRequest(): string
    {
        return $this->request->get($this->option['input_token'], '');
    }
}
