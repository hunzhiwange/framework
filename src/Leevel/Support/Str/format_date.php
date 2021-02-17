<?php

declare(strict_types=1);

namespace Leevel\Support\Str;

/**
 * 日期格式化.
 */
function format_date(int $dateTemp, array $lang = [], string $dateFormat = 'Y-m-d H:i'): string
{
    $sec = time() - $dateTemp;
    if ($sec < 0) {
        return date($dateFormat, $dateTemp);
    }

    $hover = (int) floor($sec / 3600);
    if (0 === $hover) {
        if (0 === ($min = (int) floor($sec / 60))) {
            return $sec.' '.($lang['seconds'] ?? 'seconds ago');
        }

        return $min.' '.($lang['minutes'] ?? 'minutes ago');
    }
    if ($hover < 24) {
        return $hover.' '.($lang['hours'] ?? 'hours ago');
    }

    return date($dateFormat, $dateTemp);
}

class format_date
{
}
