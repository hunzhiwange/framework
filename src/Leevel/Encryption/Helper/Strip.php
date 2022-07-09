<?php

declare(strict_types=1);

namespace Leevel\Encryption\Helper;

class Strip
{
    /**
     * 字符过滤 JS 和 HTML 标签.
     */
    public static function handle(string $strings): string
    {
        $strings = trim($strings);
        $strings = CleanJs::handle($strings);
        $strings = strip_tags($strings);

        return $strings;
    }
}
