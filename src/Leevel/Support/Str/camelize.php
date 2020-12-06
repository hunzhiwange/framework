<?php

declare(strict_types=1);

namespace Leevel\Support\Str;

/**
 * 下划线转驼峰.
 */
function camelize(string $value, string $separator = '_'): string
{
    if (false === strpos($value, $separator)) {
        return $value;
    }

    $value = $separator.str_replace($separator, ' ', $value);

    return ltrim(
        str_replace(
            ' ',
            '',
            ucwords($value)
        ),
        $separator
    );
}

class camelize
{
}
