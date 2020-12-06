<?php

declare(strict_types=1);

namespace Leevel\Support\Str;

/**
 * 随机字符串.
 */
function rand_str(int $length, string $charBox): string
{
    if (!$length || !$charBox) {
        return '';
    }

    return substr(str_shuffle($charBox), 0, $length);
}

class rand_str
{
}
