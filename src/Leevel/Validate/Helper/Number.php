<?php

declare(strict_types=1);

namespace Leevel\Validate\Helper;

/**
 * 是否为数字.
 */
class Number
{
    public static function handle(mixed $value): bool
    {
        return is_numeric($value);
    }
}
