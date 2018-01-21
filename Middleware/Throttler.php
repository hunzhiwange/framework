<?php
/*
 * This file is part of the ************************ package.
 * ##########################################################
 * #   ____                          ______  _   _ ______   #
 * #  /     \       ___  _ __  _   _ | ___ \| | | || ___ \  #
 * # |   (  ||(_)| / _ \| '__|| | | || |_/ /| |_| || |_/ /  #
 * #  \____/ |___||  __/| |   | |_| ||  __/ |  _  ||  __/   #
 * #       \__   | \___ |_|    \__  || |    | | | || |      #
 * #     Query Yet Simple      __/  |\_|    |_| |_|\_|      #
 * #                          |___ /  Since 2010.10.03      #
 * ##########################################################
 *
 * The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
 * (c) 2010-2018 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Queryyetsimple\Throttler\Middleware;

use Closure;
use Queryyetsimple\{
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
     * @var \Queryyetsimple\Throttler\IThrottler
     */
    protected $throttler;

    /**
     * HTTP Response
     *
     * @var \Queryyetsimple\Http\Response $response
     */
    protected $response;

    /**
     * 构造函数
     *
     * @param \Queryyetsimple\Throttler\IThrottler $throttler
     * @param \Queryyetsimple\Http\Response $response
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
     * @param \Queryyetsimple\Http\Request $request
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
     * @param \Queryyetsimple\Throttler\RateLimiter $rateLimiter
     * @return void
     */
    protected function header($rateLimiter)
    {
        $this->response->headers($rateLimiter->toArray());
    }
}
