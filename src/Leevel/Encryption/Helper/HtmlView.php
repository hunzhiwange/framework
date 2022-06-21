<?php

declare(strict_types=1);

namespace Leevel\Encryption\Helper;

/**
 * 字符 HTML 安全显示.
 */
class HtmlView
{
    public static function handle(string $strings): string
    {
        $strings = stripslashes($strings);
        return nl2br($strings);
    }
}
