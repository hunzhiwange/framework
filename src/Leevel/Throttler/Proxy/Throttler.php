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

namespace Leevel\Throttler\Proxy;

use Leevel\Di\Container;
use Leevel\Http\IRequest;
use Leevel\Throttler\IRateLimiter;
use Leevel\Throttler\IThrottler as IBaseThrottler;
use Leevel\Throttler\Throttler as BaseThrottler;

/**
 * 代理 throttler.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.08.10
 *
 * @version 1.0
 * @codeCoverageIgnore
 */
class Throttler implements IThrottler
{
    /**
     * call.
     *
     * @param string $method
     * @param array  $args
     *
     * @return mixed
     */
    public static function __callStatic(string $method, array $args)
    {
        return self::proxy()->{$method}(...$args);
    }

    /**
     * 创建一个节流器.
     *
     * @param null|string $key
     * @param int         $xRateLimitLimit
     * @param int         $xRateLimitTime
     *
     * @return \Leevel\Throttler\IRateLimiter
     */
    public static function create(?string $key = null, int $xRateLimitLimit = 20, int $xRateLimitTime = 20): IRateLimiter
    {
        return self::proxy()->create($key, $xRateLimitLimit, $xRateLimitTime);
    }

    /**
     * 设置 http request.
     *
     * @param \Leevel\Http\IRequest $request
     *
     * @return \Leevel\Throttler\IThrottler
     */
    public static function setRequest(IRequest $request): IBaseThrottler
    {
        return self::proxy()->setRequest($request);
    }

    /**
     * 获取请求 key.
     *
     * @param null|string $key
     *
     * @return string
     */
    public static function getRequestKey(?string $key = null): string
    {
        return self::proxy()->getRequestKey($key);
    }

    /**
     * 代理服务.
     *
     * @return \Leevel\Throttler\Throttler
     */
    public static function proxy(): BaseThrottler
    {
        return Container::singletons()->make('throttler');
    }
}
