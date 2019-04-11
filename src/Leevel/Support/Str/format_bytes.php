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

namespace Leevel\Support\Str;

/**
 * 文件大小格式化.
 *
 * @param int  $fileSize
 * @param bool $withUnit
 *
 * @return string
 */
function format_bytes(int $fileSize, bool $withUnit = true): string
{
    if ($fileSize >= 1073741824) {
        $fileSize = round($fileSize / 1073741824, 2).($withUnit ? 'G' : '');
    } elseif ($fileSize >= 1048576) {
        $fileSize = round($fileSize / 1048576, 2).($withUnit ? 'M' : '');
    } elseif ($fileSize >= 1024) {
        $fileSize = round($fileSize / 1024, 2).($withUnit ? 'K' : '');
    } else {
        $fileSize = $fileSize.($withUnit ? 'B' : '');
    }

    return $fileSize;
}
