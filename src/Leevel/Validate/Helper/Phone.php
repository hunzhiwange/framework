<?php

declare(strict_types=1);

namespace Leevel\Validate\Helper;

use function strlen;

class Phone
{
    /**
     * 值是否为电话号码或者手机号码.
     */
    public static function handle(mixed $value): bool
    {
        if (!is_scalar($value)) {
            return false;
        }

        $value = (string) $value;

        return (11 === strlen($value) &&
            preg_match('/^13[0-9]{9}|15[012356789][0-9]{8}|18[0-9]{9}|14[579][0-9]{8}|17[0-9]{9}$/', $value)) ||
            preg_match('/^\d{3,4}-?\d{7,9}$/', $value);
    }
}
