<?php

declare(strict_types=1);

namespace Leevel\Support\Str;

/**
 * 随机大写字母.
 */
function rand_alpha_uppercase(int $length, ?string $charBox = null): string
{
    if (!$length) {
        return '';
    }

    if (null === $charBox) {
        $charBox = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    } else {
        $charBox = strtoupper($charBox);
    }

    return rand_str($length, $charBox);
}

class rand_alpha_uppercase
{
}

// import fn.
class_exists(rand_str::class);
