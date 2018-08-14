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
 * (c) 2010-2018 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Leevel\Throttler;

use Leevel\Cache\ICache;
use Leevel\Http\IRequest;
use RuntimeException;

/**
 * throttler 入口.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.08.07
 *
 * @version 1.0
 */
class Throttler implements IThrottler
{
    /**
     * 节流器实例.
     *
     * @var \Leevel\Throttler\RateLimiter[]
     */
    protected $rateLimiter = [];

    /**
     * cache.
     *
     * @var \Leevel\Cache\ICache
     */
    protected $cache;

    /**
     * http request.
     *
     * @var \Leevel\Http\IRequest
     */
    protected $request;

    /**
     * 构造函数.
     *
     * @param \Leevel\Cache\ICache $cache
     */
    public function __construct(ICache $cache)
    {
        $this->cache = $cache;
    }

    /**
     * call.
     *
     * @param string $method
     * @param array  $args
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
     * @param null|string $key
     * @param int         $xRateLimitLimit
     * @param int         $xRateLimitTime
     *
     * @return \Leevel\Throttler\RateLimiter
     */
    public function create(?string $key = null, int $xRateLimitLimit = 60, int $xRateLimitTime = 60): RateLimiter
    {
        $key = $this->getRequestKey($key);

        if (isset($this->rateLimiter[$key])) {
            return $this->rateLimiter[$key]->
            limit($xRateLimitLimit)->

            time($xRateLimitTime);
        }

        return $this->rateLimiter[$key] = new RateLimiter(
            $this->cache,
            $key,
            $xRateLimitLimit,
            $xRateLimitTime
        );
    }

    /**
     * 设置 http request.
     *
     * @param \Leevel\Http\IRequest $request
     *
     * @return $this
     */
    public function setRequest(IRequest $request)
    {
        $this->request = $request;

        return $this;
    }

    /**
     * 获取请求 key.
     *
     * @param null|string $key
     *
     * @return string
     */
    public function getRequestKey(?string $key = null)
    {
        if (!$key && !$this->request) {
            throw new RuntimeException('Request is not set.');
        }

        return $key ?:
            sha1(
                $this->request->getClientIp().
                '@'.
                $this->request->getNode()
            );
    }
}
