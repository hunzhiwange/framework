<?php

declare(strict_types=1);

namespace Leevel\Validate\Helper;

class IsFloat
{
    /**
     * 验证是否为浮点数.
     */
    public static function handle(mixed $value): bool
    {
        return false !== filter_var($value, FILTER_VALIDATE_FLOAT);
    }
}
