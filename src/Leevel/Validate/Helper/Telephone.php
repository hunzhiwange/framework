<?php

declare(strict_types=1);

namespace Leevel\Validate\Helper;

class Telephone
{
    /**
     * 值是否为电话号码.
     */
    public static function handle(mixed $value): bool
    {
        if (!\is_scalar($value)) {
            return false;
        }

        $value = (string) $value;

        return preg_match('/^\d{3,4}-?\d{7,9}$/', $value) > 0;
    }
}
