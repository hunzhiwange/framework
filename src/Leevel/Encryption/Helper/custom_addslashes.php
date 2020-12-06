<?php

declare(strict_types=1);

namespace Leevel\Encryption\Helper;

/**
 * 添加模式转义.
 */
function custom_addslashes(mixed $data, bool $recursive = true): mixed
{
    if (true === $recursive && is_array($data)) {
        $result = [];
        foreach ($data as $key => $value) {
            $result[custom_addslashes($key)] = custom_addslashes($value);
        }

        return $result;
    }

    if (is_string($data)) {
        $data = addslashes($data);
    }

    return $data;
}

class custom_addslashes
{
}
