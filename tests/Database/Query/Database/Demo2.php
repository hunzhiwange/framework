<?php

declare(strict_types=1);

namespace Tests\Database\Query\Database;

use Leevel\Database\Condition;

class Demo2
{
    public function handle(\Closure $next, Condition $condition, array $middlewaresOptions): array
    {
        $condition->where('id', '>', 5);
        $condition->where('id', '<=', 90);

        return $next($condition, $middlewaresOptions);
    }
}
