<?php

declare(strict_types=1);

namespace Leevel\Support\Arr;

/**
 * 排除掉黑名单键的数据.
 */
class Except
{
    public static function handle(array $input, array $filter): array
    {
        foreach ($filter as $f) {
            if (array_key_exists($f, $input)) {
                unset($input[$f]);
            }
        }

        return $input;
    }
}
