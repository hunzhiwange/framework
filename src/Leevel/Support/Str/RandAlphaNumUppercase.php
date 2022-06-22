<?php

declare(strict_types=1);

namespace Leevel\Support\Str;

/**
 * 随机大写字母数字.
 */
class RandAlphaNumUppercase
{
    public static function handle(int $length, ?string $charBox = null): string
    {
        if (!$length) {
            return '';
        }

        if (null === $charBox) {
            $charBox = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        } else {
            $charBox = strtoupper($charBox);
        }

        return RandStr::handle($length, $charBox);
    }
}
