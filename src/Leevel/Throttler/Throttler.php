<?php

declare(strict_types=1);

namespace Leevel\Throttler;

use Leevel\Cache\ICache;
use Leevel\Http\Request;
use RuntimeException;

/**
 * 节流器.
 */
class Throttler implements IThrottler
{
    /**
     * 速率限制器实例.
     */
    protected array $rateLimiter = [];

    /**
     * HTTP 请求.
     */
    protected ?Request $request = null;

    /**
     * 构造函数.
     */
    public function __construct(protected ICache $cache)
    {
    }

    /**
     * 实现魔术方法 __call.
     */
    public function __call(string $method, array $args): mixed
    {
        return $this->create(...$args)->{$method}();
    }

    /**
     * {@inheritDoc}
     */
    public function create(?string $key = null, int $limit = 60, int $time = 60): RateLimiter
    {
        $key = $this->getRequestKey($key);
        if (isset($this->rateLimiter[$key])) {
            return $this->rateLimiter[$key]
                ->setLimit($limit)
                ->setTime($time);
        }

        return $this->rateLimiter[$key] = new RateLimiter(
            $this->cache,
            $key,
            $limit,
            $time
        );
    }

    /**
     * {@inheritDoc}
     */
    public function setRequest(Request $request): IThrottler
    {
        $this->request = $request;

        return $this;
    }

    /**
     * {@inheritDoc}
     *
     * @throws \RuntimeException
     */
    public function getRequestKey(?string $key = null): string
    {
        if (!$key && !$this->request) {
            throw new RuntimeException('Request is not set.');
        }

        return $key ?: sha1(
            ($this->request->getClientIp() ?: '').
            '@'.
            $this->request->getBaseUrl()
        );
    }
}
