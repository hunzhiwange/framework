<?php

declare(strict_types=1);

namespace Leevel\Encryption\Helper;

/**
 * 过滤十六进制字符串.
 */
class CleanHex
{
    public static function handle(string $strings): string
    {
        return (string) preg_replace('![\\][xX]([A-Fa-f0-9]{1,3})!', '', $strings);
    }
}
