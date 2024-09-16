<?php

declare(strict_types=1);

namespace Leevel\Validate\Helper;

class Url
{
    public const string MESSAGE = '{field} 不是有效的 URL 地址';

    /**
     * 验证是否为 URL 地址.
     */
    public static function handle(mixed $value): bool
    {
        return false !== filter_var($value, FILTER_VALIDATE_URL);
    }
}
