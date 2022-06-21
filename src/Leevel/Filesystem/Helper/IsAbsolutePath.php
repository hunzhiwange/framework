<?php

declare(strict_types=1);

namespace Leevel\Filesystem\Helper;

use Symfony\Component\Filesystem\Filesystem;

/**
 * 判断是否为绝对路径.
 */
class IsAbsolutePath
{
    public static function handle(string $path): bool
    {
        return (new Filesystem())->isAbsolutePath($path);
    }
}
