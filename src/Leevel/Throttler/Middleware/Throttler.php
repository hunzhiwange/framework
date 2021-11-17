<?php

declare(strict_types=1);

namespace Leevel\Throttler\Middleware;

use Closure;
use Leevel\Http\Request;
use Leevel\Kernel\Exceptions\TooManyRequestsHttpException;
use Leevel\Throttler\IThrottler;
use Symfony\Component\HttpFoundation\Response;

/**
 * 节流器中间件.
 */
class Throttler
{
    /**
     * 构造函数.
     */
    public function __construct(protected IThrottler $throttler)
    {
    }

    /**
     * 请求.
     */
    public function handle(Closure $next, Request $request, int $limit = 60, int $time = 60): Response
    {
        $rateLimiter = $this->throttler
            ->setRequest($request)
            ->create(null, $limit, $time);

        if ($rateLimiter->attempt()) {
            $e = new class('Too many attempts.') extends TooManyRequestsHttpException {
            };
            $e->setHeaders($rateLimiter->getHeaders());

            throw $e;
        }

        return $next($request);
    }
}
