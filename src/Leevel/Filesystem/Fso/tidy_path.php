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

/**
 * 整理目录斜线风格
 *
 * @param string $path
 * @param bool   $unix
 *
 * @return string
 */
function tidy_path(string $path, bool $unix = true): string
{
    $path = str_replace('\\', '/', $path);
    $path = preg_replace('|/+|', '/', $path);
    $path = str_replace(':/', ':\\', $path);

    if (!$unix) {
        $path = str_replace('/', '\\', $path);
    }

    return rtrim($path, '\\/');
}

class tidy_path
{
}
