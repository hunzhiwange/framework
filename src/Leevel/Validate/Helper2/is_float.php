<?php

declare(strict_types=1);

namespace Leevel\Validate\Helper;

/**
 * 验证是否为浮点数.
 */
function is_float(mixed $value): bool
{
    return false !== filter_var($value, FILTER_VALIDATE_FLOAT);
}

class is_float
{
}
