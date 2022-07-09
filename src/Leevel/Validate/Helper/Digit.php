<?php

declare(strict_types=1);

namespace Leevel\Validate\Helper;

class Digit
{
    /**
     * 检测字符串中的字符是否都是数字，负数和小数会检测不通过.
     */
    public static function handle(mixed $value): bool
    {
        if (!is_string($value)) {
            return false;
        }

        return ctype_digit($value);
    }
}
