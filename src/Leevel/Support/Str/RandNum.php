<?php

declare(strict_types=1);

namespace Leevel\Support\Str;

class RandNum
{
    /**
     * 随机数字.
     */
    public static function handle(int $length, ?string $charBox = null): string
    {
        if (!$length) {
            return '';
        }

        if (null === $charBox) {
            $charBox = '0123456789';
        }

        return RandStr::handle($length, $charBox);
    }
}
