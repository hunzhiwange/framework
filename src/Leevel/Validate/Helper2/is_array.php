<?php

declare(strict_types=1);

namespace Leevel\Validate\Helper;

use function is_array as base_is_array;

/**
 * 验证是否为数组.
 */
function is_array(mixed $value): bool
{
    return base_is_array($value);
}

class is_array
{
}
