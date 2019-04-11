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

namespace Leevel\Encryption\Safe;

use RuntimeException;

/**
 * 访问时间限制.
 *
 * @param array $limitTime
 * @param int   $time
 */
function limit_time(array $limitTime, int $time): void
{
    if (empty($limitTime)) {
        return;
    }

    $limitMinTime = strtotime($limitTime[0]);
    $limitMaxTime = strtotime($limitTime[1] ?? '');

    if (false === $limitMinTime || false === $limitMaxTime) {
        return;
    }

    if ($limitMaxTime < $limitMinTime) {
        $limitMaxTime += 60 * 60 * 24;
    }

    if ($time < $limitMinTime || $time > $limitMaxTime) {
        return;
    }

    $e = sprintf(
        'You can only before %s or after %s to access this.',
        date('Y-m-d H:i:s', $limitMinTime),
        date('Y-m-d H:i:s', $limitMaxTime)
    );

    throw new RuntimeException($e);
}
