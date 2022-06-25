<?php

declare(strict_types=1);

namespace Leevel\Validate\Helper;

/**
 * 是否为大写英文字母.
 */
class AlphaUpper
{
    public static function handle(mixed $value): bool
    {
        if (!is_string($value)) {
            return false;
        }

        return preg_match('/^[A-Z]+$/', $value) > 0;
    }
}
