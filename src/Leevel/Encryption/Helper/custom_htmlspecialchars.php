<?php

declare(strict_types=1);

namespace Leevel\Encryption\Helper;

/**
 * 字符 HTML 安全实体.
 */
function custom_htmlspecialchars(mixed $data): mixed
{
    if (!is_array($data)) {
        $data = (array) $data;
    }

    $data = array_map(function ($data) {
        if (is_string($data)) {
            $data = htmlspecialchars(trim($data));
        }

        return $data;
    }, $data);

    if (1 === count($data)) {
        $data = reset($data);
    }

    return $data;
}

class custom_htmlspecialchars
{
}
