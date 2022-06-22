<?php

declare(strict_types=1);

namespace Leevel\Support\Str;

/**
 * 随机字母数字.
 */
class RandAlphaNum
{
    public static function handle(int $length, ?string $charBox = null): string
    {
        if (!$length) {
            return '';
        }

        if (null === $charBox) {
            $charBox = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        }

        return RandStr::handle($length, $charBox);
    }
}
