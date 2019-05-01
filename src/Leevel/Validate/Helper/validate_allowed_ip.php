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

namespace Leevel\Validate\Helper;

use InvalidArgumentException;

/**
 * 验证 IP 许可.
 *
 * @param mixed $value
 * @param array $parameter
 *
 * @return bool
 */
function validate_allowed_ip($value, array $parameter): bool
{
    if (!is_string($value)) {
        return false;
    }

    if (1 > count($parameter)) {
        throw new InvalidArgumentException('At least 1 parameter.');
    }

    return in_array($value, $parameter, true);
}

class validate_allowed_ip
{
}
