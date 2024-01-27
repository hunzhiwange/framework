<?php

declare(strict_types=1);

namespace Leevel\Filesystem\Helper;

class TraverseDirectory
{
    /**
     * 浏览目录.
     */
    public static function handle(string $path, bool $recursive, \Closure $cal, array $filter = []): void
    {
        if (!is_dir($path)) {
            return;
        }

        $instance = new \DirectoryIterator($path);
        foreach ($instance as $file) {
            if ($file->isDot() || \in_array($file->getFilename(), $filter, true)) {
                continue;
            }

            $cal($file);

            if ($recursive && $file->isDir()) {
                self::handle($file->getPath().'/'.$file->getFilename(), true, $cal, $filter);
            }
        }
    }
}
