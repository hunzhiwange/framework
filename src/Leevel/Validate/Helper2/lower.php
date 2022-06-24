<?php

declare(strict_types=1);

namespace Leevel\Validate\Helper;

/**
 * 验证是否都是小写.
 */
function lower($value): bool
{
    if (!is_string($value)) {
        return false;
    }
    
    return ctype_lower($value);
}

class lower
{
}
