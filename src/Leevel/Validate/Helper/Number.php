<?php

declare(strict_types=1);

namespace Leevel\Validate\Helper;

class Number
{
    public const string MESSAGE = '{field} 必须是数字';

    /**
     * 是否为数字.
     */
    public static function handle(mixed $value): bool
    {
        return is_numeric($value);
    }
}
