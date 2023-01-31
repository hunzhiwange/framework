<?php

declare(strict_types=1);

namespace Leevel\Support\Str;

class UnCamelize
{
    /**
     * 驼峰转下划线.
     */
    public static function handle(string $value, string $separator = '_'): string
    {
        return strtolower(preg_replace('/([a-z0-9])([A-Z])/', '$1'.$separator.'$2', $value) ?: '');
    }
}
