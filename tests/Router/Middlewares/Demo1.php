<?php

declare(strict_types=1);

namespace Tests\Router\Middlewares;

use Closure;
use Leevel\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Demo1
{
    public function __construct()
    {
    }

    public function terminate(Closure $next, Request $request, Response $response)
    {
        $GLOBALS['demo_middlewares'][] = 'Demo1::terminate';
        $next($request, $response);
    }
}
