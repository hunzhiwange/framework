<?php

declare(strict_types=1);

namespace Leevel\Filesystem\Helper;

class TidyPath
{
    /**
     * 整理目录斜线风格.
     */
    public static function handle(string $path, bool $unix = true): string
    {
        $path = str_replace('\\', '/', $path);
        $path = (string) preg_replace('|/+|', '/', $path);
        $path = str_replace(':/', ':\\', $path);
        if (!$unix) {
            $path = str_replace('/', '\\', $path);
        }

        return rtrim($path, '\\/');
    }
}
