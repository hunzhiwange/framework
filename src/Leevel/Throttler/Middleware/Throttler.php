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
 * (c) 2010-2019 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Leevel\Throttler\Middleware;

use Closure;
use Leevel\Http\IRequest;
use Leevel\Kernel\Exception\TooManyRequestsHttpException;
use Leevel\Throttler\IThrottler;

/**
 * throttler 中间件.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.08.10
 *
 * @version 1.0
 */
class Throttler
{
    /**
     * throttler.
     *
     * @var \Leevel\Throttler\IThrottler
     */
    protected IThrottler $throttler;

    /**
     * 构造函数.
     *
     * @param \Leevel\Throttler\IThrottler $throttler
     */
    public function __construct(IThrottler $throttler)
    {
        $this->throttler = $throttler;
    }

    /**
     * 请求
     *
     * @param \Closure              $next
     * @param \Leevel\Http\IRequest $request
     * @param int                   $limit
     * @param int                   $time
     */
    public function handle(Closure $next, IRequest $request, int $limit = 60, int $time = 60): void
    {
        $rateLimiter = $this->throttler
            ->setRequest($request)
            ->create(null, $limit, $time);

        if ($rateLimiter->attempt()) {
            $e = new class('Too many attempts.') extends TooManyRequestsHttpException {
            };
            $e->setHeaders($rateLimiter->header());

            throw $e;
        }

        $next($request);
    }
}
