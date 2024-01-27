<?php

declare(strict_types=1);

namespace Tests\Database\Query\Database;

use Leevel\Database\Condition;

class Demo
{
    public function handle(\Closure $next, Condition $condition, array $middlewaresConfigs): array
    {
        $condition->where('id', '>', 5);
        $condition->where('id', '<=', 90);
        $middlewaresConfigs['hello_comment'] = 'hello comment';

        return $next($condition, $middlewaresConfigs);
    }

    public function terminate(\Closure $next, Condition $condition, array $middlewaresConfigs, array $makeSql): array
    {
        $makeSql = array_merge(['force_master' => '/*'.$middlewaresConfigs['hello_comment'].'*/'], $makeSql);

        return $next($condition, $middlewaresConfigs, $makeSql);
    }
}
