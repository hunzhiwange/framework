<?php

declare(strict_types=1);

namespace Leevel\Validate\Helper;

class Double
{
    public const string MESSAGE = '{field} 必须是双精度浮点数';

    /**
     * 是否双精度浮点数.
     */
    public static function handle(mixed $value): bool
    {
        if (!\is_scalar($value)) {
            return false;
        }

        return preg_match('/^[-\+]?\d+(\.\d+)?$/', (string) $value) > 0;
    }
}
