<?php

declare(strict_types=1);

namespace Leevel\Validate\Helper;

/**
 * 验证是否都是大写.
 */
function upper(mixed $value): bool
{
    if (!is_string($value)) {
        return false;
    }
    
    return ctype_upper($value);
}

class upper
{
}
