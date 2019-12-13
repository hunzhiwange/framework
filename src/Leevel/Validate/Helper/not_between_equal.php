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

use InvalidArgumentException;

/**
 * 未处于 betweenEqual 范围，包含等于.
 *
 * @param mixed $value
 *
 * @throws \InvalidArgumentException
 */
function not_between_equal($value, array $param): bool
{
    if (!array_key_exists(0, $param) ||
        !array_key_exists(1, $param)) {
        $e = 'Missing the first or second element of param.';

        throw new InvalidArgumentException($e);
    }

    return $value <= $param[0] || $value >= $param[1];
}

class not_between_equal
{
}