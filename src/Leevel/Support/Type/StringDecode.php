<?php

declare(strict_types=1);

namespace Leevel\Support\Type;

/**
 * 字符串解码.
 */
class StringDecode
{
    public static function handle(string $value, bool $autoType = true): float|int|string
    {
        if (0 === strpos($value, ':string:')) {
            return substr($value, strlen(':string:'));
        }

        if (0 === strpos($value, ':float:')) {
            return (float) substr($value, strlen(':float:'));
        }

        if (0 === strpos($value, ':int:')) {
            return (int) substr($value, strlen(':int:'));
        }

        if (!$autoType) {
            return $value;
        }

        return ctype_digit($value) ?
            (int) $value : (is_numeric($value) ? (float) $value : $value);
    }
}
