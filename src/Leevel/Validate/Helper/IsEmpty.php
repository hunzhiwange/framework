<?php

declare(strict_types=1);

namespace Leevel\Validate\Helper;

class IsEmpty
{
    public const string MESSAGE = '{field} 必须为空';

    /**
     * 值是否为空.
     */
    public static function handle(mixed $value): bool
    {
        return empty($value);
    }
}
