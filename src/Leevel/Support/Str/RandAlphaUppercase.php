<?php

declare(strict_types=1);

namespace Leevel\Support\Str;

class RandAlphaUppercase
{
    /**
     * 随机大写字母.
     */
    public static function handle(int $length, ?string $charBox = null): string
    {
        if (!$length) {
            return '';
        }

        if (null === $charBox) {
            $charBox = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        } else {
            $charBox = strtoupper($charBox);
        }

        return RandStr::handle($length, $charBox);
    }
}
