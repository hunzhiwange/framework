<?php

declare(strict_types=1);

namespace Leevel\Support\Type;

class StringEncode
{
    /**
     * 字符串编码.
     */
    public static function handle(float|int|string $value, bool $autoType = true): string
    {
        if (\is_int($value)) {
            return ':int:'.$value;
        }

        if (\is_float($value)) {
            return ':float:'.$value;
        }

        return ($autoType ? '' : ':string:').$value;
    }
}
