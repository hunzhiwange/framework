<?php

declare(strict_types=1);

namespace Tests\Router\Middlewares;

use Leevel\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Demo2
{
    public function handle(\Closure $next, Request $request): Response
    {
        $GLOBALS['demo_middlewares'][] = 'Demo2::handle';

        return $next($request);
    }

    public function terminate(\Closure $next, Request $request, Response $response): void
    {
        $GLOBALS['demo_middlewares'][] = 'Demo2::terminate';
        $next($request, $response);
    }
}
