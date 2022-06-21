<?php

declare(strict_types=1);

namespace Leevel\Filesystem\Helper;

use Symfony\Component\Filesystem\Filesystem;

/**
 * 创建目录.
 */
function create_directory(string $dir, int $mode = 0777): void
{
    $filesystem = new Filesystem();
    $filesystem->mkdir($dir, $mode);
}

class create_directory
{
}
