<?php

declare(strict_types=1);

namespace Leevel\Support\Arr;

class Only
{
    /**
     * 只允许白名单键的数据.
     */
    public static function handle(array $input, array $filter): array
    {
        foreach ($input as $k => $v) {
            if (!in_array($k, $filter, true)) {
                unset($input[$k]);
            }
        }

        return $input;
    }
}
