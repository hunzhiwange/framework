<?php

declare(strict_types=1);

namespace Leevel\Support\Str;

class RandAlpha
{
    /**
     * 随机字母.
     */
    public static function handle(int $length, ?string $charBox = null): string
    {
        if (!$length) {
            return '';
        }

        if (null === $charBox) {
            $charBox = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        }

        return RandStr::handle($length, $charBox);
    }
}
