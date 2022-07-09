<?php

declare(strict_types=1);

namespace Leevel\Validate\Helper;

use InvalidArgumentException;
use Leevel\Support\Type\Type as BaseType;

class Type
{
    /**
     * 数据类型验证.
     * @throws \InvalidArgumentException
     */
    public static function handle(mixed $value, array $param): bool
    {
        if (!array_key_exists(0, $param)) {
            $e = 'Missing the first element of param.';

            throw new InvalidArgumentException($e);
        }

        if (!is_string($param[0])) {
            return false;
        }

        return BaseType::handle($value, $param[0]);
    }
}
