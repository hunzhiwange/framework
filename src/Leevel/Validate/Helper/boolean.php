<?php

declare(strict_types=1);

namespace Leevel\Validate\Helper;

/**
 * 验证是否为布尔值.
 */
function boolean(mixed $value): bool
{
    return in_array($value, [
        true,
        false,
        0,
        1,
        '0',
        '1',
        't',
        'f',
    ], true);
}

class boolean
{
}
