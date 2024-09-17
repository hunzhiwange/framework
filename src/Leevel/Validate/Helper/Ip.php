<?php

declare(strict_types=1);

namespace Leevel\Validate\Helper;

class Ip
{
    public const string MESSAGE = '{field} 不是有效的 IP 地址';

    public const string ANTISENSE_MESSAGE = '{field} 不能是有效的 IP 地址';

    /**
     * 是否为合法的 IP 地址.
     */
    public static function handle(mixed $value): bool
    {
        return false !== filter_var($value, FILTER_VALIDATE_IP);
    }
}
