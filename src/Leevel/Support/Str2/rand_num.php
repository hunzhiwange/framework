<?php

declare(strict_types=1);

namespace Leevel\Support\Str;

/**
 * 随机数字.
 */
function rand_num(int $length, ?string $charBox = null): string
{
    if (!$length) {
        return '';
    }

    if (null === $charBox) {
        $charBox = '0123456789';
    }

    return rand_str($length, $charBox);
}

class rand_num
{
}

// import fn.
class_exists(rand_str::class);
