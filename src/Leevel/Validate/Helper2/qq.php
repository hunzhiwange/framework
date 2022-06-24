<?php

declare(strict_types=1);

namespace Leevel\Validate\Helper;

/**
 * 是否为 QQ 号码.
 */
function qq(mixed $value): bool
{
    if (!is_scalar($value)) {
        return false;
    }

    return preg_match('/^[1-9]\d{4,11}$/', (string) $value) > 0;
}

class qq
{
}
