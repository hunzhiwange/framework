<?php

declare(strict_types=1);

namespace Leevel\Support\Str;

/**
 * 随机字母.
 */
class RandAlpha
{
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
