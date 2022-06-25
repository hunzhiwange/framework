<?php

declare(strict_types=1);

namespace Leevel\Validate\Helper;

/**
 * 是否为 QQ 号码.
 */
class Qq
{
    public static function handle(mixed $value): bool
    {
        if (!is_scalar($value)) {
            return false;
        }

        return preg_match('/^[1-9]\d{4,11}$/', (string) $value) > 0;
    }
}
