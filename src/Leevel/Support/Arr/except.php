<?php

declare(strict_types=1);

namespace Leevel\Support\Arr;

/**
 * 返回黑名单排除后的数据.
 */
function except(array &$input, array $filter): array
{
    foreach ($filter as $f) {
        if (array_key_exists($f, $input)) {
            unset($input[$f]);
        }
    }

    return $input;
}

class except
{
}
