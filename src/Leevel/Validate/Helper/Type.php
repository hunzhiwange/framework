<?php

declare(strict_types=1);

namespace Leevel\Validate\Helper;

use Leevel\Support\Type\Type as BaseType;

class Type
{
    /**
     * 数据类型验证.
     *
     * @throws \InvalidArgumentException
     */
    public static function handle(mixed $value, array $param): bool
    {
        if (!\array_key_exists(0, $param)) {
            throw new \InvalidArgumentException('Missing the first element of param.');
        }

        if (!\is_string($param[0])) {
            return false;
        }

        return BaseType::handle($value, $param[0]);
    }
}
