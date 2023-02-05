<?php

declare(strict_types=1);

namespace Leevel\Validate\Helper;

class AlphaLower
{
    /**
     * 是否为小写英文字母.
     */
    public static function handle(mixed $value): bool
    {
        if (!\is_string($value)) {
            return false;
        }

        return preg_match('/^[a-z]+$/', $value) > 0;
    }
}
