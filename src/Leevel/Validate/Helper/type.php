<?php

declare(strict_types=1);

namespace Leevel\Validate\Helper;

use InvalidArgumentException;
use function Leevel\Support\Type\type as baseType;
use Leevel\Support\Type\type as baseType;

/**
 * 数据类型验证.
 *
 * @throws \InvalidArgumentException
 */
function type(mixed $value, array $param): bool
{
    if (!array_key_exists(0, $param)) {
        $e = 'Missing the first element of param.';

        throw new InvalidArgumentException($e);
    }

    if (!is_string($param[0])) {
        return false;
    }

    return baseType($value, $param[0]);
}

class type
{
}

// import fn.
class_exists(baseType::class);
