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

/**
 * 获取上传文件扩展名.
 *
 * @param string $fileName 文件名
 * @param int    $case     格式化参数 0 默认，1 转为大小 ，转为大小
 */
function get_extension(string $fileName, int $case = 0): string
{
    $fileName = pathinfo($fileName, PATHINFO_EXTENSION);

    if (1 === $case) {
        return strtoupper($fileName);
    }

    if (2 === $case) {
        return strtolower($fileName);
    }

    return $fileName;
}

class get_extension
{
}
