<?php

declare(strict_types=1);

namespace Tests\Router\Middlewares;

use Leevel\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Demo1
{
    public function terminate(\Closure $next, Request $request, Response $response): void
    {
        $GLOBALS['demo_middlewares'][] = 'Demo1::terminate';
        $next($request, $response);
    }
}
