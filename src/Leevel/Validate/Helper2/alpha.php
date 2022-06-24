<?php

declare(strict_types=1);

namespace Leevel\Validate\Helper;

/**
 * 是否为英文字母.
 */
function alpha(mixed $value): bool
{
    if (!is_string($value)) {
        return false;
    }

    return preg_match('/^[A-Za-z]+$/', $value) > 0;
}

class alpha
{
}
