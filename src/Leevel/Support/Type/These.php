<?php

declare(strict_types=1);

namespace Leevel\Support\Type;

/**
 * 验证参数是否为指定的类型集合.
 */
class These
{
    public static function handle(mixed $value, array $types): bool
    {
        foreach ($types as $type) {
            if (Type::handle($value, $type)) {
                return true;
            }
        }

        return false;
    }
}
