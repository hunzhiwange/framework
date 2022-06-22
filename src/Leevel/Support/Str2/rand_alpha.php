<?php

declare(strict_types=1);

namespace Leevel\Support\Str;

/**
 * 随机字母.
 */
function rand_alpha(int $length, ?string $charBox = null): string
{
    if (!$length) {
        return '';
    }

    if (null === $charBox) {
        $charBox = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    }

    return rand_str($length, $charBox);
}

class rand_alpha
{
}

// import fn.
class_exists(rand_str::class);
