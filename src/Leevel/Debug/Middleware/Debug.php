<?php

declare(strict_types=1);

namespace Leevel\Debug\Middleware;

use Closure;
use Leevel\Debug\Debug as Debugs;
use Leevel\Http\Request;
use Leevel\Kernel\IApp;
use Symfony\Component\HttpFoundation\Response;

/**
 * Debug 中间件.
 */
class Debug
{
    /**
     * 构造函数.
     */
    public function __construct(protected IApp $app, protected Debugs $debug)
    {
    }

    /**
     * 请求.
     */
    public function handle(Closure $next, Request $request): void
    {
        if (!$this->app->isDebug()) {
            $next($request);

            return;
        }

        $this->debug->bootstrap();
        $next($request);
    }

    /**
     * 响应.
     */
    public function terminate(Closure $next, Request $request, Response $response): void
    {
        if (!$this->app->isDebug()) {
            $next($request, $response);

            return;
        }

        $this->debug->handle($request, $response);
        $next($request, $response);
    }
}
