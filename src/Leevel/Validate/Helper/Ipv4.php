<?php

declare(strict_types=1);

namespace Leevel\Validate\Helper;

class Ipv4
{
    /**
     * 是否为 ipv4.
     */
    public static function handle(mixed $value): bool
    {
        return false !== filter_var($value, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4);
    }
}
