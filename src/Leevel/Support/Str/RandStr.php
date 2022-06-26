<?php

declare(strict_types=1);

namespace Leevel\Support\Str;

/**
 * 随机字符串.
 */
class RandStr
{
    public static function handle(int $length, string $charBox): string
    {
        if (!$length || !$charBox) {
            return '';
        }

        return substr(str_shuffle($charBox), 0, $length);
    }
}
