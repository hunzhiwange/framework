<?php

declare(strict_types=1);

namespace Tests\Router\Middlewares;

use Closure;
use Leevel\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DemoForAll
{
    public function handle(Closure $next, Request $request): Response
    {
        $GLOBALS['demo_middlewares'][] = 'DemoForAll::handle';

        return $next($request);
    }

    public function terminate(Closure $next, Request $request, Response $response): void
    {
        $GLOBALS['demo_middlewares'][] = 'DemoForAll::terminate';
        $next($request, $response);
    }
}
