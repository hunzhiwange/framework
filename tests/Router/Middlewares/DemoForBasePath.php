<?php

declare(strict_types=1);

namespace Tests\Router\Middlewares;

use Closure;
use Leevel\Http\Request;

class DemoForBasePath
{
    public function __construct()
    {
    }

    public function handle(Closure $next, Request $request)
    {
        $GLOBALS['demo_middlewares'][] = sprintf('DemoForBasePath::handle');
        $next($request);
    }
}
