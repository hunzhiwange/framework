<?php

declare(strict_types=1);

namespace Leevel\Filesystem\Helper;

use Symfony\Component\Filesystem\Filesystem;

/**
 * 删除目录.
 */
function delete_directory(string $dir): void
{
    if (!is_dir($dir) || !file_exists($dir)) {
        return;
    }

    $filesystem = new Filesystem();
    $filesystem->remove($dir);
}

class delete_directory
{
}
