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

namespace Leevel\Validate\Helper;

use DateTimeZone;
use Exception;

/**
 * 是否为正确的时区.
 *
 * @param mixed $value
 *
 * @return bool
 */
function validate_timezone($value): bool
{
    try {
        if (!is_string($value)) {
            return false;
        }

        new DateTimeZone($value);
    } catch (Exception $e) {
        return false;
    }

    return true;
}

class validate_timezone
{
}
