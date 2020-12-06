<?php

declare(strict_types=1);

namespace Leevel\Validate\Helper;

use DateTimeZone;
use Exception;

/**
 * 是否为正确的时区.
 */
function timezone(mixed $value): bool
{
    try {
        if (!is_string($value)) {
            return false;
        }

        new DateTimeZone($value);
    } catch (Exception) {
        return false;
    }

    return true;
}

class timezone
{
}
