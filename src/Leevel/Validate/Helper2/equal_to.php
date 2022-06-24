<?php

declare(strict_types=1);

namespace Leevel\Validate\Helper;

use InvalidArgumentException;
use Leevel\Validate\IValidator;

/**
 * 两个字段是否相同.
 *
 * @throws \InvalidArgumentException
 */
function equal_to(mixed $value, array $param, IValidator $validator): bool
{
    if (!array_key_exists(0, $param)) {
        $e = 'Missing the first element of param.';

        throw new InvalidArgumentException($e);
    }

    return $value === $validator->getFieldValue($param[0]);
}

class equal_to
{
}
