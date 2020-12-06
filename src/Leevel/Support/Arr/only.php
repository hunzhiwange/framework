<?php

declare(strict_types=1);

namespace Leevel\Support\Arr;

/**
 * 返回白名单过滤后的数据.
 */
function only(array &$input, array $filter): array
{
    $result = [];
    foreach ($filter as $f) {
        $result[$f] = $input[$f] ?? null;
    }

    return $input = $result;
}

class only
{
}
