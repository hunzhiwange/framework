<?php

declare(strict_types=1);

namespace Leevel\Validate\Helper;

/**
 * 是否为 null.
 */
function is_null(mixed $value): bool
{
    return null === $value;
}

class is_null
{
}
