<?php

declare(strict_types=1);

namespace Tests\Database\Query\Database;

use Leevel\Database\Condition;

class ForceMaster
{
    public function terminate(\Closure $next, Condition $condition, array $middlewaresConfigs, array $makeSql): array
    {
        $makeSql = array_merge(['force_master' => '/*FORCE_MASTER*/'], $makeSql);

        return $next($condition, $middlewaresConfigs, $makeSql);
    }
}
