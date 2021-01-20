<?php

declare(strict_types=1);

namespace Leevel\Validate\Helper;

/**
 * 值是否为电话号码.
 */
function telephone(mixed $value): bool
{
    if (!is_scalar($value)) {
        return false;
    }

    $value = (string) $value;

    return preg_match('/^\d{3,4}-?\d{7,9}$/', $value) > 0;
}

class telephone
{
}
