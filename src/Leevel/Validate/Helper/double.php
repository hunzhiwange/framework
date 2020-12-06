<?php

declare(strict_types=1);

namespace Leevel\Validate\Helper;

/**
 * 是否双精度浮点数.
 */
function double(mixed $value): bool
{
    if (!is_scalar($value)) {
        return false;
    }

    return preg_match('/^[-\+]?\d+(\.\d+)?$/', (string) ($value)) > 0;
}

class double
{
}
