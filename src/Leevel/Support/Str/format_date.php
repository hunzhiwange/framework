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
 * 日期格式化.
 *
 * @param int    $dateTemp
 * @param array  $lang
 * @param string $dateFormat
 *
 * @return string
 */
function format_date(int $dateTemp, array $lang = [], string $dateFormat = 'Y-m-d H:i'): string
{
    $sec = time() - $dateTemp;

    if ($sec < 0) {
        return date($dateFormat, $dateTemp);
    }

    $hover = (int) (floor($sec / 3600));

    if (0 === $hover) {
        if (0 === ($min = (int) (floor($sec / 60)))) {
            return $sec.' '.($lang['seconds'] ?? 'seconds ago');
        }

        return $min.' '.($lang['minutes'] ?? 'minutes ago');
    }
    if ($hover < 24) {
        return $hover.' '.($lang['hours'] ?? 'hours ago');
    }

    return date($dateFormat, $dateTemp);
}
