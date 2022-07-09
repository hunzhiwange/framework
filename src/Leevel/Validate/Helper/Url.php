<?php

declare(strict_types=1);

namespace Leevel\Validate\Helper;

class Url
{
    /**
     * 验证是否为 URL 地址.
     */
    public static function handle(mixed $value): bool
    {
        return false !== filter_var($value, FILTER_VALIDATE_URL);
    }
}
