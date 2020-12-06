<?php

declare(strict_types=1);

namespace Leevel\Encryption\Helper;

/**
 * 字符 HTML 安全显示.
 */
function html_view(string $strings): string
{
    $strings = stripslashes($strings);
    $strings = nl2br($strings);

    return $strings;
}

class html_view
{
}
