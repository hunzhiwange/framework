<?php

declare(strict_types=1);

namespace Leevel\Encryption\Helper;

/**
 * HTML 过滤.
 */
function html_filter(mixed $data): mixed
{
    if (is_array($data)) {
        $result = [];
        foreach ($data as $key => $val) {
            $result[html_filter($key)] = html_filter($val);
        }

        return $result;
    }

    $data = trim((string) $data);
    $data = (string) preg_replace([
        '/<\s*a[^>]*href\s*=\s*[\'\"]?(javascript|vbscript)[^>]*>/i',
        '/<([^>]*)on(\w)+=[^>]*>/i',
        '/<\s*\/?\s*(script|i?frame)[^>]*\s*>/i',
    ], [
        '<a href="#">',
        '<$1>',
        '&lt;$1&gt;',
    ], $data);
    $data = str_replace('　', '', $data);

    return $data;
}

class html_filter
{
}
