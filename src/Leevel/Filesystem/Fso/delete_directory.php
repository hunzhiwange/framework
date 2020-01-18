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
 * 删除目录.
 */
function delete_directory(string $dir, bool $recursive = false): void
{
    if (!file_exists($dir) || !is_dir($dir)) {
        return;
    }

    if (!$recursive) {
        rmdir($dir);
    } else {
        $instance = new DirectoryIterator($dir);
        foreach ($instance as $file) {
            if ($file->isDot()) {
                continue;
            }

            if ($file->isFile()) {
                if (!unlink($file->getRealPath())) {
                    return;
                }
            } elseif ($file->isDir()) {
                delete_directory($file->getRealPath(), $recursive);
            }
        }

        rmdir($dir);
    }
}

class delete_directory
{
}
