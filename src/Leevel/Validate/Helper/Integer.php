<?php

declare(strict_types=1);

namespace Leevel\Validate\Helper;

/**
 * 是否整型数字.
 */
class Integer
{
    public static function handle(mixed $value): bool
    {
        return false !== filter_var($value, FILTER_VALIDATE_INT);
    }
}
