<?php

declare(strict_types=1);

namespace Leevel\Encryption\Helper;

/**
 * 字符过滤.
 */
function str_filter(mixed $data): mixed
{
    if (is_array($data)) {
        $result = [];
        foreach ($data as $key => $val) {
            $result[str_filter($key)] = str_filter($val);
        }

        return $result;
    }

    $data = trim((string) $data);
    $data = preg_replace(
        '/&amp;((#(\d{3,5}|x[a-fA-F0-9]{4}));)/',
        '&\\1',
        custom_htmlspecialchars($data)
    );
    $data = str_replace('　', '', $data);

    return $data;
}

class str_filter
{
}

// import fn.
class_exists(custom_htmlspecialchars::class);
