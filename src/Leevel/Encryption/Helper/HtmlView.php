<?php

declare(strict_types=1);

namespace Leevel\Encryption\Helper;

class HtmlView
{
    /**
     * 字符 HTML 安全显示.
     */
    public static function handle(string $strings): string
    {
        $strings = stripslashes($strings);

        return nl2br($strings);
    }
}
