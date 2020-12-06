<?php

declare(strict_types=1);

namespace Leevel\Support\Str;

/**
 * 随机字母数字.
 */
function rand_alpha_num(int $length, ?string $charBox = null): string
{
    if (!$length) {
        return '';
    }

    if (null === $charBox) {
        $charBox = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
    }

    return rand_str($length, $charBox);
}

class rand_alpha_num
{
}

// import fn.
class_exists(rand_str::class);
