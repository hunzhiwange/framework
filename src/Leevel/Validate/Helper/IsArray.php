<?php

declare(strict_types=1);

namespace Leevel\Validate\Helper;

class IsArray
{
    public const string MESSAGE = '{field} 必须是数组';

    public const string ANTISENSE_MESSAGE = '{field} 不能是数组';

    /**
     * 验证是否为数组.
     */
    public static function handle(mixed $value): bool
    {
        return \is_array($value);
    }
}
