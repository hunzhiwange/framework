<?php

declare(strict_types=1);

namespace Leevel\Validate\Helper;

/**
 * 是否为中文.
 */
function chinese(mixed $value): bool
{
    if (!is_string($value)) {
        return false;
    }

    return preg_match('/^[\x{4e00}-\x{9fa5}]+$/u', $value) > 0;
}

class chinese
{
}
