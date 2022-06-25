<?php

declare(strict_types=1);

namespace Leevel\Validate\Helper;

/**
 * 值是否为空.
 */
class IsEmpty
{
    public static function handle(mixed $value): bool
    {
        return empty($value);
    }
}
