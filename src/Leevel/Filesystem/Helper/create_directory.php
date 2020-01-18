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

use RuntimeException;

/**
 * 创建目录.
 *
 * @throws \RuntimeException
 *
 * @return true
 */
function create_directory(string $dir, int $mode = 0777, bool $writableValid = true): bool
{
    if (!is_dir($dir)) {
        if (is_dir($parDir = dirname($dir)) && !is_writable($parDir)) {
            $e = sprintf('Dir `%s` is not writeable.', $parDir);

            throw new RuntimeException($e);
        }
        mkdir($dir, $mode, true);
    }

    if (true === $writableValid && !is_writable($dir)) {
        $e = sprintf('Dir `%s` is not writeable.', $dir);

        throw new RuntimeException($e);
    }

    return true;
}

class create_directory
{
}
