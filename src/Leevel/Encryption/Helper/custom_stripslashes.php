<?php

declare(strict_types=1);

namespace Leevel\Encryption\Helper;

/**
 * 移除魔术方法转义.
 */
function custom_stripslashes(mixed $data, bool $recursive = true): mixed
{
    if (true === $recursive && is_array($data)) {
        $result = [];
        foreach ($data as $key => $value) {
            $result[custom_stripslashes($key)] = custom_stripslashes($value);
        }

        return $result;
    }

    if (is_string($data)) {
        $data = stripslashes($data);
    }

    return $data;
}

class custom_stripslashes
{
}
