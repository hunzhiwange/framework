<?php

declare(strict_types=1);

namespace Leevel\Validate\Helper;

/**
 * 是否为数字.
 */
function number(mixed $value): bool
{
    return is_numeric($value);
}

class number
{
}
