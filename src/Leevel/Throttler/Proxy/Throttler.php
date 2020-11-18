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
use Leevel\Throttler\Throttler as BaseThrottler;

/**
 * 代理 throttler.
 *
 * @method static \Leevel\Throttler\RateLimiter create(?string $key = null, int $xRateLimitLimit = 20, int $xRateLimitTime = 20) 创建一个节流器.
 * @method static \Leevel\Throttler\IThrottler setRequest(\Leevel\Http\Request $request)                                         设置 http request.
 * @method static string getRequestKey(?string $key = null)                                                                      获取请求 key.
 */
class Throttler
{
    /**
     * call.
     */
    public static function __callStatic(string $method, array $args): mixed
    {
        return self::proxy()->{$method}(...$args);
    }

    /**
     * 代理服务.
     */
    public static function proxy(): BaseThrottler
    {
        return Container::singletons()->make('throttler');
    }
}
