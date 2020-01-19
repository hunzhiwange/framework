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
 * throttler 入口.
 */
class Throttler implements IThrottler
{
    /**
     * 节流器实例.
     *
     * @var array
     */
    protected array $rateLimiter = [];

    /**
     * cache.
     *
     * @var \Leevel\Cache\ICache
     */
    protected ICache $cache;

    /**
     * HTTP Request.
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
    public function __call(string $method, array $args)
    {
        return $this->create(...$args)->{$method}();
    }

    /**
     * 创建一个节流器.
     *
     * @return \Leevel\Throttler\IRateLimiter
     */
    public function create(?string $key = null, int $limit = 60, int $time = 60): IRateLimiter
    {
        $key = $this->getRequestKey($key);
        if (isset($this->rateLimiter[$key])) {
            return $this->rateLimiter[$key]
                ->limit($limit)
                ->time($time);
        }

        return $this->rateLimiter[$key] = new RateLimiter(
            $this->cache, $key, $limit, $time
        );
    }

    /**
     * 设置 http request.
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
            $this->request->getClientIp().
            '@'.
            $this->request->getRoot()
        );
    }
}
