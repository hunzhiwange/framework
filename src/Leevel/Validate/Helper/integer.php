<?php

declare(strict_types=1);

namespace Leevel\Validate\Helper;

/**
 * 是否整型数字.
 */
function integer(mixed $value): bool
{
    return false !== filter_var($value, FILTER_VALIDATE_INT);
}

class integer
{
}
