<?php

declare(strict_types=1);

namespace Leevel\Encryption\Helper;

/**
 * 字符串文本化.
 */
function text(string $strings, bool $deep = true, array $black = []): string
{
    if (true === $deep && !$black) {
        $black = [
            ' ', '&nbsp;', '&', '=', '-',
            '#', '%', '!', '@', '^', '*', 'amp;',
        ];
    }

    $strings = clean_js($strings);
    $strings = (string) preg_replace('/\s(?=\s)/', '', $strings); // 彻底过滤空格
    $strings = (string) preg_replace('/[\n\r\t]/', ' ', $strings);
    if ($black) {
        $strings = str_replace($black, '', $strings);
    }
    $strings = strip_tags($strings);
    $strings = htmlspecialchars($strings);
    $strings = str_replace("'", '', $strings);

    return $strings;
}

class text
{
}

// import fn.
class_exists(clean_js::class);
