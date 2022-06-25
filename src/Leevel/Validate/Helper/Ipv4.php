<?php

declare(strict_types=1);

namespace Leevel\Validate\Helper;

/**
 * 是否为 ipv4.
 */
class Ipv4
{
    public static function handle(mixed $value): bool
    {
        return false !== filter_var($value, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4);
    }
}
