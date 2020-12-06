<?php

declare(strict_types=1);

namespace Tests\Router\Middlewares;

use Closure;
use Leevel\Http\Request;

/**
 * demo3 中间件.
 */
class Demo3
{
    public function __construct()
    {
    }

    public function handle(Closure $next, Request $request, int $arg1 = 1, string $arg2 = 'hello')
    {
        $GLOBALS['demo_middlewares'][] = sprintf('Demo3::handle(arg1:%s,arg2:%s)', $arg1, $arg2);
        $next($request);
    }
}
