<?php

declare(strict_types=1);

namespace Leevel\Support\Type;

class StringDecode
{
    /**
     * 字符串解码.
     */
    public static function handle(string $value, bool $autoType = true): float|int|string
    {
        if (str_starts_with($value, ':string:')) {
            return substr($value, \strlen(':string:'));
        }

        if (str_starts_with($value, ':float:')) {
            return (float) substr($value, \strlen(':float:'));
        }

        if (str_starts_with($value, ':int:')) {
            return (int) substr($value, \strlen(':int:'));
        }

        if (!$autoType) {
            return $value;
        }

        return ctype_digit($value) ?
            (int) $value : (is_numeric($value) ? (float) $value : $value);
    }
}
