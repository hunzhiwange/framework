<?php

declare(strict_types=1);

namespace Leevel\Validate\Helper;

/**
 * 是否为 ipv4.
 */
function ipv4(mixed $value): bool
{
    return false !== filter_var($value, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4);
}

class ipv4
{
}
