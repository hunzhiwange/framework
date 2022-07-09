<?php

declare(strict_types=1);

namespace Leevel\Filesystem\Helper;

use Symfony\Component\Filesystem\Filesystem;

class Link
{
    /**
     * 创建软连接.
     */
    public static function handle(string $originDir, string $targetDir): void
    {
        (new Filesystem())->symlink($originDir, $targetDir, true);
    }
}
