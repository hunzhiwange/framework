<?php

declare(strict_types=1);

namespace Leevel\Support\Str;

/**
 * 随机小写字母数字.
 */
function rand_alpha_num_lowercase(int $length, ?string $charBox = null): string
{
    if (!$length) {
        return '';
    }

    if (null === $charBox) {
        $charBox = 'abcdefghijklmnopqrstuvwxyz1234567890';
    } else {
        $charBox = strtolower($charBox);
    }

    return rand_str($length, $charBox);
}

class rand_alpha_num_lowercase
{
}

// import fn.
class_exists(rand_str::class);
