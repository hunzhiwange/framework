<?php

declare(strict_types=1);

namespace Leevel\Validate\Helper;

/**
 * 值是否为空.
 */
function is_empty(mixed $value): bool
{
    return empty($value);
}

class is_empty
{
}
