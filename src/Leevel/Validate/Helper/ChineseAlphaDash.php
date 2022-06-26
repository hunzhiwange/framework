<?php

declare(strict_types=1);

namespace Leevel\Validate\Helper;

/**
 * 是否为中文、数字、下划线、短横线和字母.
 */
class ChineseAlphaDash
{
    public static function handle(mixed $value): bool
    {
        if (is_int($value)) {
            return true;
        }

        if (!is_string($value)) {
            return false;
        }

        return preg_match('/^[\x{4e00}-\x{9fa5}A-Za-z0-9\-\_]+$/u', $value) > 0;
    }
}
