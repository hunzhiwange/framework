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
     *
     * @var array
     */
    protected array $rateLimiter = [];

    /**
     * 缓存.
     *
     * @var \Leevel\Cache\ICache
     */
    protected ICache $cache;

    /**
     * HTTP 请求.
     *
     * @var \Leevel\Http\Request
     */
    protected ?Request $request = null;

    /**
     * 构造函数.
     */
    public function __construct(ICache $cache)
    {
        $this->cache = $cache;
    }

    /**
     * call.
     *
     * @return mixed
     */
    public function __call(string $method, array $args): mixed
    {
        return $this->create(...$args)->{$method}();
    }

    /**
     * 创建一个速率限制器.
     *
     * @return \Leevel\Throttler\RateLimiter
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
            $this->cache, $key, $limit, $time
        );
    }

    /**
     * 设置 HTTP 请求.
     *
     * @return \Leevel\Throttler\IThrottler
     */
    public function setRequest(Request $request): IThrottler
    {
        $this->request = $request;

        return $this;
    }

    /**
     * 获取请求 key.
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
