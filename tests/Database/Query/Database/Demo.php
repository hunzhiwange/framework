<?php

declare(strict_types=1);

namespace Tests\Database\Query\Database;

use Leevel\Database\Condition;

class Demo
{
    public function handle(\Closure $next, Condition $condition, array $middlewaresOptions): array
    {
        $condition->where('id', '>', 5);
        $condition->where('id', '<=', 90);
        $middlewaresOptions['hello_comment'] = 'hello comment';

        return $next($condition, $middlewaresOptions);
    }

    public function terminate(\Closure $next, Condition $condition, array $middlewaresOptions, array $makeSql): array
    {
        $makeSql = array_merge(['force_master' => '/*'.$middlewaresOptions['hello_comment'].'*/'], $makeSql);

        return $next($condition, $middlewaresOptions, $makeSql);
    }
}
