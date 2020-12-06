<?php

declare(strict_types=1);

namespace Leevel\Encryption\Helper;

/**
 * 过滤脚本.
 */
function filter_script(string $strings): string
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

class filter_script
{
}
