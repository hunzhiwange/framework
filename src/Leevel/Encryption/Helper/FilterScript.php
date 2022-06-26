<?php

declare(strict_types=1);

namespace Leevel\Encryption\Helper;

/**
 * 过滤脚本.
 */
class FilterScript
{
    public static function handle(string $strings): string
    {
        return (string) preg_replace([
            '/<\s*script/',
            '/<\s*\/\s*script\s*>/',
            '/<\\?/',
            '/\\?>/',
        ], [
            '&lt;script',
            '&lt;/script&gt;',
            '&lt;?',
            '?&gt;',
        ], $strings);
    }
}
