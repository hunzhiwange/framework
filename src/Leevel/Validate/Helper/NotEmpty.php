<?php

declare(strict_types=1);

namespace Leevel\Validate\Helper;

class NotEmpty
{
    /**
     * 值是否不为空.
     */
    public static function handle(mixed $value): bool
    {
        return !empty($value);
    }
}
