<?php

declare(strict_types=1);

namespace Leevel\Validate\Helper;

/**
 * 值是否不为空.
 */
class NotEmpty
{
    public static function handle(mixed $value): bool
    {
        return !empty($value);
    }
}
