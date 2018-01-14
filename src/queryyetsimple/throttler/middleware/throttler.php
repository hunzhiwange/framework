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
namespace queryyetsimple\throttler\middleware;

use Closure;
use queryyetsimple\{
    http\request,
    http\response,
    throttler\ithrottler,
    mvc\too_many_requests_http
};

/**
 * throttler 中间件
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.08.10
 * @version 1.0
 */
class throttler
{

    /**
     * throttler
     *
     * @var \queryyetsimple\throttler\ithrottler
     */
    protected $throttler;

    /**
     * HTTP Response
     *
     * @var \queryyetsimple\http\response $response
     */
    protected $response;

    /**
     * 构造函数
     *
     * @param \queryyetsimple\throttler\ithrottler $throttler
     * @param \queryyetsimple\http\response $response
     * @return void
     */
    public function __construct(ithrottler $throttler, response $response)
    {
        $this->throttler = $throttler;
        $this->response = $response;
    }

    /**
     * 请求
     *
     * @param \Closure $next
     * @param \queryyetsimple\http\request $request
     * @param int $limit
     * @param int $time
     * @return void
     */
    public function handle(Closure $next, request $request, $limit = 60, $time = 60)
    {
        $rateLimiter = $this->throttler->create(null, ( int ) $limit, ( int ) $time);

        if ($rateLimiter->attempt()) {
            $this->header($rateLimiter);
            throw new too_many_requests_http('Too many attempts.');
        } else {
            $this->header($rateLimiter);
        }

        $next($request);
    }

    /**
     * 发送 HEADER
     *
     * @param \queryyetsimple\throttler\rate_limiter $rateLimiter
     * @return void
     */
    protected function header($rateLimiter)
    {
        $this->response->headers($rateLimiter->toArray());
    }
}
