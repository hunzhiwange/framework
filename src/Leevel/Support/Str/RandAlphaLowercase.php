<?php

declare(strict_types=1);

namespace Leevel\Support\Str;

/**
 * 随机小写字母.
 */
class RandAlphaLowercase
{
    public static function handle(int $length, ?string $charBox = null): string
    {
        if (!$length) {
            return '';
        }

        if (null === $charBox) {
            $charBox = 'abcdefghijklmnopqrstuvwxyz';
        } else {
            $charBox = strtolower($charBox);
        }

        return RandStr::handle($length, $charBox);
    }
}
