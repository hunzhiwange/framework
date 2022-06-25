<?php

declare(strict_types=1);

namespace Leevel\Validate\Helper;

/**
 * 字符串是否为数字和字母.
 */
class AlphaNum
{
    public static function handle(mixed $value): bool
    {
        if (is_int($value)) {
            return true;
        }

        if (!is_string($value)) {
            return false;
        }

        return preg_match('/^[A-Za-z0-9]+$/', (string) $value) > 0;
    }
}
