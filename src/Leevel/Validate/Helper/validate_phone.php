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

/**
 * 值是否为电话号码或者手机号码
 *
 * @param mixed $value
 *
 * @return bool
 */
function validate_phone($value): bool
{
    if (!is_scalar($value)) {
        return false;
    }

    $value = (string) ($value);

    return (11 === strlen($value) &&
        preg_match('/^13[0-9]{9}|15[012356789][0-9]{8}|18[0-9]{9}|14[579][0-9]{8}|17[0-9]{9}$/', $value)) ||
        preg_match('/^\d{3,4}-?\d{7,9}$/', $value);
}

class validate_phone
{
}
