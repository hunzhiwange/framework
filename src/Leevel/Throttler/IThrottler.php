<?php

declare(strict_types=1);

namespace Leevel\Throttler;

use Leevel\Http\Request;

/**
 * 节流器接口.
 */
interface IThrottler
{
    /**
     * 创建一个速率限制器.
     */
    public function create(?string $key = null, int $xRateLimitLimit = 20, int $xRateLimitTime = 20): RateLimiter;

    /**
     * 设置 HTTP 请求.
     */
    public function setRequest(Request $request): self;

    /**
     * 获取请求 key.
     */
    public function getRequestKey(?string $key = null): string;
}
