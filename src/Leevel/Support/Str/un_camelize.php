<?php

declare(strict_types=1);

namespace Leevel\Support\Str;

/**
 * 驼峰转下划线.
 */
function un_camelize(string $value, string $separator = '_'): string
{
    return strtolower(preg_replace('/([a-z])([A-Z])/', '$1'.$separator.'$2', $value) ?: '');
}

class un_camelize
{
}
