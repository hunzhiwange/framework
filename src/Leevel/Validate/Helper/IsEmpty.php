<?php

declare(strict_types=1);

namespace Leevel\Validate\Helper;

class IsEmpty
{
    /**
     * 值是否为空.
     */
    public static function handle(mixed $value): bool
    {
        return empty($value);
    }
}
