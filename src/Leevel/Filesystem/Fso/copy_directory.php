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
 * (c) 2010-2019 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Leevel\Filesystem\Fso;

use DirectoryIterator;

/**
 * 复制目录.
 *
 * @param string $sourcePath
 * @param string $targetPath
 * @param array  $filter
 */
function copy_directory(string $sourcePath, string $targetPath, array $filter = []): void
{
    if (!is_dir($sourcePath)) {
        return;
    }

    $instance = new DirectoryIterator($sourcePath);

    foreach ($instance as $file) {
        if ($file->isDot() ||
            in_array($file->getFilename(), $filter, true)) {
            continue;
        }

        $newPath = $targetPath.'/'.$file->getFilename();

        if ($file->isFile()) {
            if (!is_dir($newPath)) {
                create_directory(dirname($newPath));
            }

            if (!copy($file->getRealPath(), $newPath)) {
                return;
            }
        } elseif ($file->isDir()) {
            if (!is_dir($newPath)) {
                create_directory($newPath);
            }

            if (!copy_directory($file->getRealPath(), $newPath, $filter)) {
                return;
            }
        }
    }
}

if (!function_exists('Leevel\\Filesystem\\Fso\\create_directory')) {
    include __DIR__.'/create_directory.php';
}
