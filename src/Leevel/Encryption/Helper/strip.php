<?php

declare(strict_types=1);

namespace Leevel\Encryption\Helper;

/**
 * 字符过滤 JS 和 HTML 标签.
 */
function strip(string $strings): string
{
    $strings = trim($strings);
    $strings = clean_js($strings);
    $strings = strip_tags($strings);

    return $strings;
}

class strip
{
}

// import fn.
class_exists(clean_js::class);
