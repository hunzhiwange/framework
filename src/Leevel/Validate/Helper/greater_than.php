<?php

declare(strict_types=1);

namespace Leevel\Validate\Helper;

use InvalidArgumentException;

/**
 * 大于.
 * @throws \InvalidArgumentException
 */
function greater_than(mixed $value, array $param): bool
{
    if (!array_key_exists(0, $param)) {
        $e = 'Missing the first element of param.';

        throw new InvalidArgumentException($e);
    }

    return $value > $param[0];
}

class greater_than
{
}
