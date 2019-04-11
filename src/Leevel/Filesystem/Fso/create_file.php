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

use InvalidArgumentException;

/**
 * 新建文件.
 *
 * @param string $path
 * @param int    $mode
 *
 * @return bool
 */
function create_file(string $path, int $mode = 0666): bool
{
    $dirname = dirname($path);

    if (is_file($dirname)) {
        throw new InvalidArgumentException('Dir cannot be a file.');
    }

    if (!is_dir($dirname)) {
        if (is_dir(dirname($dirname)) && !is_writable(dirname($dirname))) {
            $e = sprintf('Unable to create the %s directory.', $dirname);

            throw new InvalidArgumentException($e);
        }

        mkdir($dirname, 0777, true);
    }

    if (!is_writable($dirname) || !($file = fopen($path, 'a'))) {
        $e = sprintf('The directory "%s" is not writable', $dirname);

        throw new InvalidArgumentException($e);
    }

    $mode = $mode & ~umask();

    chmod($path, $mode);

    return fclose($file);
}
