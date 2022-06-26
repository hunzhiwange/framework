<?php

declare(strict_types=1);

namespace Leevel\Validate\Helper;

/**
 * 验证是否为 URL 地址.
 */
class Url
{
    public static function handle(mixed $value): bool
    {
        return false !== filter_var($value, FILTER_VALIDATE_URL);
    }
}
