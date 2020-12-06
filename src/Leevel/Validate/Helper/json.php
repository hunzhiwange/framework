<?php

declare(strict_types=1);

namespace Leevel\Validate\Helper;

use Exception;

/**
 * 验证是否为正常的 JSON 数据.
 */
function json(mixed $value): bool
{
    if (is_object($value) && !method_exists($value, '__toString')) {
        return false;
    }

    if (is_string($value) && class_exists($value) &&
        !method_exists($value, '__toString')) {
        return false;
    }

    try {
        json_decode((string) $value, true, 512, JSON_THROW_ON_ERROR);

        return true;
    } catch (Exception) {
        return false;
    }
}

class json
{
}
