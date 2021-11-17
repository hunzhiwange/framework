<?php

declare(strict_types=1);

namespace Tests\Router\Middlewares;

use Closure;
use Leevel\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DemoForBasePath
{
    public function handle(Closure $next, Request $request): Response
    {
        $GLOBALS['demo_middlewares'][] = sprintf('DemoForBasePath::handle');

        return $next($request);
    }
}
