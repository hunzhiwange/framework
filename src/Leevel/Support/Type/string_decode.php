<?php

declare(strict_types=1);

namespace Leevel\Support\Type;

/**
 * 字符串解码.
 */
function string_decode(string $value): float|int|string
{
    if (0 === strpos($value, ':float:')) {
        return (float) substr($value, strlen(':float:'));
    }

    if (0 === strpos($value, ':int:')) {
        return (int) substr($value, strlen(':int:'));
    }

    return $value;
}

class string_decode
{
}
