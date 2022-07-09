<?php

declare(strict_types=1);

namespace Leevel\Filesystem\Helper;

use Symfony\Component\Filesystem\Filesystem;

class DeleteDirectory
{
    /**
     * 删除目录.
     */
    public static function handle(string $dir): void
    {
        if (!is_dir($dir) || !file_exists($dir)) {
            return;
        }

        $filesystem = new Filesystem();
        $filesystem->remove($dir);
    }
}
