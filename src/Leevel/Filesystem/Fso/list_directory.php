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

namespace Leevel\Filesystem\Fso;

use Closure;
use DirectoryIterator;

/**
 * 浏览目录.
 */
function list_directory(string $path, bool $recursive, Closure $cal, array $filter = []): void
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
            list_directory($file->getPath().'/'.$file->getFilename(), true, $cal, $filter);
        }
    }
}

class list_directory
{
}
