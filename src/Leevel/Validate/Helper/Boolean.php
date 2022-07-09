<?php

declare(strict_types=1);

namespace Leevel\Validate\Helper;

class Boolean
{
    /**
     * 验证是否为布尔值.
     */
    public static function handle(mixed $value): bool
    {
        return in_array($value, [
            true,
            false,
            0,
            1,
            '0',
            '1',
            't',
            'f',
        ], true);
    }
}
