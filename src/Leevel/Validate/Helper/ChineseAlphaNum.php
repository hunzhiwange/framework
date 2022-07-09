<?php

declare(strict_types=1);

namespace Leevel\Validate\Helper;

class ChineseAlphaNum
{
    /**
     * 是否为中文、数字和字母.
     */
    public static function handle(mixed $value): bool
    {
        if (is_int($value)) {
            return true;
        }

        if (!is_string($value)) {
            return false;
        }

        return preg_match('/^[\x{4e00}-\x{9fa5}a-zA-Z0-9]+$/u', $value) > 0;
    }
}
