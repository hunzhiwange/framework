<?php

declare(strict_types=1);

namespace Leevel\Support\Str;

class RandAlphaNumLowercase
{
    /**
     * 随机小写字母数字.
     */
    public static function handle(int $length, ?string $charBox = null): string
    {
        if (!$length) {
            return '';
        }

        if (null === $charBox) {
            $charBox = 'abcdefghijklmnopqrstuvwxyz1234567890';
        } else {
            $charBox = strtolower($charBox);
        }

        return RandStr::handle($length, $charBox);
    }
}
