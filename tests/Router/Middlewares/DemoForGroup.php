<?php

declare(strict_types=1);

namespace Tests\Router\Middlewares;

use Closure;
use Leevel\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * demoForGroup 中间件.
 */
class DemoForGroup
{
    public function __construct()
    {
    }

    public function handle(Closure $next, Request $request)
    {
        $GLOBALS['demo_middlewares'][] = 'DemoForGroup::handle';
        $next($request);
    }

    public function terminate(Closure $next, Request $request, Response $response)
    {
        $GLOBALS['demo_middlewares'][] = 'DemoForGroup::terminate';
        $next($request, $response);
    }
}
