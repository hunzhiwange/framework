<?php

declare(strict_types=1);

namespace Leevel\Validate\Helper;

use function strlen;

/**
 * 值是否为手机号码.
 */
class Mobile
{
    public static function handle(mixed $value): bool
    {
        if (!is_scalar($value)) {
            return false;
        }

        $value = (string) $value;

        return 11 === strlen($value) && preg_match(
            '/^13[0-9]{9}|15[012356789][0-9]{8}|18[0-9]{9}|14[579][0-9]{8}|17[0-9]{9}$/',
            $value
        );
    }
}
