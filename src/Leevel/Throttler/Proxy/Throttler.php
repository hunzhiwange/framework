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

namespace Leevel\Throttler\Proxy;

use Leevel\Di\Container;
use Leevel\Http\Request;
use Leevel\Throttler\IRateLimiter;
use Leevel\Throttler\IThrottler as IBaseThrottler;
use Leevel\Throttler\Throttler as BaseThrottler;

/**
 * 代理 throttler.
 *
 * @codeCoverageIgnore
 */
class Throttler
{
    /**
     * call.
     *
     * @return mixed
     */
    public static function __callStatic(string $method, array $args)
    {
        return self::proxy()->{$method}(...$args);
    }

    /**
     * 创建一个节流器.
     */
    public static function create(?string $key = null, int $xRateLimitLimit = 20, int $xRateLimitTime = 20): IRateLimiter
    {
        return self::proxy()->create($key, $xRateLimitLimit, $xRateLimitTime);
    }

    /**
     * 设置 http request.
     */
    public static function setRequest(Request $request): IBaseThrottler
    {
        return self::proxy()->setRequest($request);
    }

    /**
     * 获取请求 key.
     */
    public static function getRequestKey(?string $key = null): string
    {
        return self::proxy()->getRequestKey($key);
    }

    /**
     * 代理服务.
     */
    public static function proxy(): BaseThrottler
    {
        return Container::singletons()->make('throttler');
    }
}
