<?php

declare(strict_types=1);

namespace Leevel\Support\Str;

class Camelize
{
    /**
     * 下划线转驼峰.
     */
    public static function handle(string $value, string $separator = '_'): string
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
}
