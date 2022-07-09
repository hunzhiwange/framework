<?php

declare(strict_types=1);

namespace Leevel\Filesystem\Helper;

use Symfony\Component\Filesystem\Filesystem;

class CreateDirectory
{
    /**
     * 创建目录.
     */
    public static function handle(string $dir, int $mode = 0777): void
    {
        $filesystem = new Filesystem();
        $filesystem->mkdir($dir, $mode);
    }
}
