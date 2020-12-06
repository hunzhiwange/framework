<?php

declare(strict_types=1);

namespace Leevel\Validate\Helper;

/**
 * 是否为小写英文字母.
 */
function alpha_lower(mixed $value): bool
{
    if (!is_string($value)) {
        return false;
    }

    return preg_match('/^[a-z]+$/', $value) > 0;
}

class alpha_lower
{
}
