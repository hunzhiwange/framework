<?php

declare(strict_types=1);

namespace Leevel\Validate\Helper;

/**
 * 值是否不为空.
 */
function not_empty(mixed $value): bool
{
    return !empty($value);
}

class not_empty
{
}
