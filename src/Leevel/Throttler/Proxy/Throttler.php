<?php

declare(strict_types=1);

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
     * 实现魔术方法 __callStatic.
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
