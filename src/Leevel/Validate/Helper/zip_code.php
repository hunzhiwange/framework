<?php

declare(strict_types=1);

namespace Leevel\Validate\Helper;

/**
 * 是否为中国邮政编码.
 */
function zip_code(mixed $value): bool
{
    if (!is_scalar($value)) {
        return false;
    }

    return preg_match('/^[1-9]\d{5}$/', (string) $value) > 0;
}

class zip_code
{
}
