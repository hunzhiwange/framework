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
 * 根据 ID 获取打散目录.
 *
 * @param int $dataId
 *
 * @return array
 */
function distributed(int $dataId): array
{
    $dataId = abs((int) $dataId);
    $dataId = sprintf('%09d', $dataId); // 格式化为 9 位数，前面不够填充 0

    return [
        substr($dataId, 0, 3).'/'.
            substr($dataId, 3, 2).'/'.
            substr($dataId, 5, 2).'/',
        substr($dataId, -2),
    ];
}
