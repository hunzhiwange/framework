<?php

declare(strict_types=1);

namespace Leevel\Debug\Middleware;

use Leevel\Debug\Debug as Debugs;
use Leevel\Http\Request;
use Leevel\Kernel\IApp;
use Symfony\Component\HttpFoundation\Response;

/**
 * 调试器中间件.
 */
class Debug
{
    /**
     * 构造函数.
     */
    public function __construct(
        protected IApp $app,
        protected Debugs $debug,
    ) {
    }

    /**
     * 请求.
     */
    public function handle(\Closure $next, Request $request): Response
    {
        if (!$this->app->isDebug()) {
            return $next($request);
        }

        $this->debug->bootstrap();
        $response = $next($request);
        $this->debug->handle($request, $response);

        return $response;
    }
}
