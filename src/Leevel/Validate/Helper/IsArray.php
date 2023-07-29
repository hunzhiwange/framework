<?php

declare(strict_types=1);

namespace Leevel\Validate\Helper;

class IsArray
{
    /**
     * 验证是否为数组.
     */
    public static function handle(mixed $value): bool
    {
        return \is_array($value);
    }
}
