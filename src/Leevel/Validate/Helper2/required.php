<?php

declare(strict_types=1);

namespace Leevel\Validate\Helper;

/**
 * 不能为空.
 */
function required(mixed $value): bool
{
    if (null === $value) {
        return false;
    }

    if (is_string($value) && '' === trim($value)) {
        return false;
    }

    return true;
}

class required
{
}
