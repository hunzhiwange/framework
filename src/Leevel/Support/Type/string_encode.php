<?php

declare(strict_types=1);

namespace Leevel\Support\Type;

/**
 * 字符串编码.
 */
function string_encode(string|int|float $value): string
{
    if (is_int($value)) {
        return ':int:'.$value;
    }

    if (is_float($value)) {
        return ':float:'.$value;
    }

    return $value;
}

class string_encode
{
}
