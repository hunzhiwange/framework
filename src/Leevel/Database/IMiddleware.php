<?php

declare(strict_types=1);

namespace Leevel\Database;

/**
 * 扩展条件构造器中间件.
 */
interface IMiddleware
{
    public function handle(\Closure $next, Condition $condition, array $middlewaresOptions): array;

    public function terminate(\Closure $next, Condition $condition, array $middlewaresOptions, array $makeSql): array;
}
