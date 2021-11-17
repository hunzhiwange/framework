<?php

declare(strict_types=1);

namespace Leevel\Validate\Helper;

use InvalidArgumentException;

/**
 * 是否处于某个范围.
 *
 * @throws \InvalidArgumentException
 */
function in(mixed $value, array $param): bool
{
    if (!array_key_exists(0, $param)) {
        $e = 'Missing the first element of param.';

        throw new InvalidArgumentException($e);
    }

    return in_array($value, $param, true);
}

class in
{
}
