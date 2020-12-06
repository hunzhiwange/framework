<?php

declare(strict_types=1);

namespace Leevel\Validate\Helper;

/**
 * 是否不为 null.
 */
function not_null(mixed $value): bool
{
    return null !== $value;
}

class not_null
{
}
