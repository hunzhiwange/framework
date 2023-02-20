<?php

declare(strict_types=1);

namespace Leevel\Database;

/**
 * 扩展条件构造器中间件.
 */
interface IExtendMiddleware
{
    public function handle(\Closure $next, Condition $condition, array $extendMiddlewaresOptions): array;

    public function terminate(\Closure $next, Condition $condition, array $extendMiddlewaresOptions, array $makeSql): array;
}
