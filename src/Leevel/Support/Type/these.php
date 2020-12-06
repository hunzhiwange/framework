<?php

declare(strict_types=1);

namespace Leevel\Support\Type;

/**
 * 验证参数是否为指定的类型集合.
 */
function these(mixed $value, array $types): bool
{
    foreach ($types as $type) {
        if (type($value, $type)) {
            return true;
        }
    }

    return false;
}

class these
{
}

// import fn.
class_exists(type::class);
