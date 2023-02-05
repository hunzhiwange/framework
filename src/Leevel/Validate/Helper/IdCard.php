<?php

declare(strict_types=1);

namespace Leevel\Validate\Helper;

class IdCard
{
    /**
     * 是否为大陆身份证.
     */
    public static function handle(mixed $value): bool
    {
        if (!\is_string($value)) {
            return false;
        }

        return preg_match(
            '/^[1-9]\d{5}[1-9]\d{3}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{3}(\d|x|X)$/',
            $value
        ) > 0;
    }
}
