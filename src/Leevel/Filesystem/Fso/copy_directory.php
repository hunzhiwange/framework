<?php

declare(strict_types=1);

/*
 * This file is part of the ************************ package.
 * _____________                           _______________
 *  ______/     \__  _____  ____  ______  / /_  _________
 *   ____/ __   / / / / _ \/ __`\/ / __ \/ __ \/ __ \___
 *    __/ / /  / /_/ /  __/ /  \  / /_/ / / / / /_/ /__
 *      \_\ \_/\____/\___/_/   / / .___/_/ /_/ .___/
 *         \_\                /_/_/         /_/
 *
 * The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
 * (c) 2010-2020 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Leevel\Filesystem\Helper;

use DirectoryIterator;

/**
 * 复制目录.
 */
function copy_directory(string $sourcePath, string $targetPath, array $filter = []): bool
{
    if (!is_dir($sourcePath)) {
        return false;
    }

    $instance = new DirectoryIterator($sourcePath);
    foreach ($instance as $file) {
        if ($file->isDot() ||
            in_array($file->getFilename(), $filter, true)) {
            continue;
        }

        $newPath = $targetPath.'/'.$file->getFilename();

        if ($file->isFile()) {
            create_directory(dirname($newPath));
            copy($file->getRealPath(), $newPath);
        } elseif ($file->isDir()) {
            create_directory($newPath);
            copy_directory($file->getRealPath(), $newPath, $filter);
        }
    }

    return true;
}

class copy_directory
{
}

// import fn.
class_exists(create_directory::class);
