<?php

declare(strict_types=1);

namespace Leevel\Filesystem\Helper;

use Symfony\Component\Filesystem\Filesystem;

class CreateFile
{
    /**
     * 创建文件.
     */
    public static function handle(string $path, ?string $content = null, int $mode = 0666): void
    {
        $filesystem = new Filesystem();
        if (!is_dir($dirname = dirname($path))) {
            $filesystem->mkdir($dirname);
        }

        if (null === $content) {
            $filesystem->touch($path);
        } else {
            $filesystem->dumpFile($path, $content);
        }

        $filesystem->chmod($path, $mode);
    }
}
