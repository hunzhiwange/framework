<?php
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
namespace Leevel\Throttler\Middleware;

use Closure;
use Leevel\{
    Http\Request,
    Http\Response,
    Throttler\IThrottler,
    Mvc\TooManyRequestsHttp
};

/**
 * throttler 中间件
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.08.10
 * @version 1.0
 */
class Throttler
{

    /**
     * throttler
     *
     * @var \Leevel\Throttler\IThrottler
     */
    protected $throttler;

    /**
     * HTTP Response
     *
     * @var \Leevel\Http\Response $response
     */
    protected $response;

    /**
     * 构造函数
     *
     * @param \Leevel\Throttler\IThrottler $throttler
     * @param \Leevel\Http\Response $response
     * @return void
     */
    public function __construct(IThrottler $throttler, Response $response)
    {
        $this->throttler = $throttler;
        $this->response = $response;
    }

    /**
     * 请求
     *
     * @param \Closure $next
     * @param \Leevel\Http\Request $request
     * @param int $limit
     * @param int $time
     * @return void
     */
    public function handle(Closure $next, Request $request, $limit = 60, $time = 60)
    {
        $rateLimiter = $this->throttler->create(null, ( int ) $limit, ( int ) $time);

        if ($rateLimiter->attempt()) {
            $this->header($rateLimiter);
            throw new TooManyRequestsHttp('Too many attempts.');
        } else {
            $this->header($rateLimiter);
        }

        $next($request);
    }

    /**
     * 发送 HEADER
     *
     * @param \Leevel\Throttler\RateLimiter $rateLimiter
     * @return void
     */
    protected function header($rateLimiter)
    {
        $this->response->headers($rateLimiter->toArray());
    }
}
