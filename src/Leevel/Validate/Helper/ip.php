<?php

declare(strict_types=1);

namespace Leevel\Validate\Helper;

/**
 * 是否为合法的 IP 地址.
 */
function ip(mixed $value): bool
{
    return false !== filter_var($value, FILTER_VALIDATE_IP);
}

class ip
{
}
