<?php

declare(strict_types=1);

namespace Tests\Router\Middlewares;

use Closure;
use Leevel\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Demo2
{
    public function __construct()
    {
    }

    public function handle(Closure $next, Request $request)
    {
        $GLOBALS['demo_middlewares'][] = 'Demo2::handle';
        $next($request);
    }

    public function terminate(Closure $next, Request $request, Response $response)
    {
        $GLOBALS['demo_middlewares'][] = 'Demo2::terminate';
        $next($request, $response);
    }
}
