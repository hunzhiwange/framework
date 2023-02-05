<?php

declare(strict_types=1);

namespace Leevel\Validate\Helper;

class Required
{
    /**
     * 不能为空.
     */
    public static function handle(mixed $value): bool
    {
        if (null === $value) {
            return false;
        }

        if (\is_string($value) && '' === trim($value)) {
            return false;
        }

        return true;
    }
}
