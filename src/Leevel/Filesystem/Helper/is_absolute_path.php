<?php

declare(strict_types=1);

namespace Leevel\Filesystem\Helper;

use Symfony\Component\Filesystem\Filesystem;

/**
 * 判断是否为绝对路径.
 */
function is_absolute_path(string $path): bool
{
    return (new Filesystem())->isAbsolutePath($path);
}

class is_absolute_path
{
}
