<?php

declare(strict_types=1);

namespace Leevel\Validate\Helper;

/**
 * 验证是否为浮点数.
 */
class IsFloat
{
    public static function handle(mixed $value): bool
    {
        return false !== filter_var($value, FILTER_VALIDATE_FLOAT);
    }
}
