<?php

declare(strict_types=1);

namespace Leevel\Filesystem\Helper;

use Closure;
use DirectoryIterator;

/**
 * 浏览目录.
 */
function traverse_directory(string $path, bool $recursive, Closure $cal, array $filter = []): void
{
    if (!is_dir($path)) {
        return;
    }

    $instance = new DirectoryIterator($path);
    foreach ($instance as $file) {
        if ($file->isDot() ||
            in_array($file->getFilename(), $filter, true)) {
            continue;
        }

        $cal($file);

        if (true === $recursive && $file->isDir()) {
            traverse_directory($file->getPath().'/'.$file->getFilename(), true, $cal, $filter);
        }
    }
}

class traverse_directory
{
}
