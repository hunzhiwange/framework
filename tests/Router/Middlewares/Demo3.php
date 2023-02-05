<?php

declare(strict_types=1);

namespace Tests\Router\Middlewares;

use Leevel\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Demo3
{
    public function handle(\Closure $next, Request $request, int $arg1 = 1, string $arg2 = 'hello'): Response
    {
        $GLOBALS['demo_middlewares'][] = sprintf('Demo3::handle(arg1:%s,arg2:%s)', $arg1, $arg2);

        return $next($request);
    }
}
