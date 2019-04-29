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
 * 判断字符串中是否包含给定的字符结尾.
 *
 * @param string $toSearched
 * @param string $search
 *
 * @return bool
 */
function ends_with(string $toSearched, string $search): bool
{
    if ((string) $search === substr($toSearched, -strlen($search))) {
        return true;
    }

    return false;
}

class ends_with
{
}
