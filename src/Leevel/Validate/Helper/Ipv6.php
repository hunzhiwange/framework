<?php

declare(strict_types=1);

namespace Leevel\Validate\Helper;

class Ipv6
{
    public const string MESSAGE = '{field} 不是有效的 IPV6 地址';

    public const string ANTISENSE_MESSAGE = '{field} 不能是有效的 IPV6 地址';

    /**
     * 是否为 ipv6.
     */
    public static function handle(mixed $value): bool
    {
        return false !== filter_var($value, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6);
    }
}
