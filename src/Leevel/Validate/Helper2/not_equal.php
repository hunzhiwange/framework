<?php

declare(strict_types=1);

namespace Leevel\Validate\Helper;

use InvalidArgumentException;

/**
 * 两个值是否不相同.
 *
 * - 全等匹配，为了严禁.
 *
 * @throws \InvalidArgumentException
 */
function not_equal(mixed $value, array $param): bool
{
    if (!array_key_exists(0, $param)) {
        $e = 'Missing the first element of param.';

        throw new InvalidArgumentException($e);
    }

    return $value !== $param[0];
}

class not_equal
{
}
