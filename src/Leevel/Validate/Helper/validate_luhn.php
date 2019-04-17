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
 * 值是否为银行卡等符合 luhn 算法.
 *
 * @param mixed $value
 *
 * @return bool
 */
function validate_luhn($value): bool
{
    if (!is_scalar($value)) {
        return false;
    }

    $value = (string) ($value);

    if (!preg_match('/^[0-9]+$/', $value)) {
        return false;
    }

    $total = 0;

    for ($i = strlen($value); $i >= 1; $i--) {
        $index = $i - 1;

        if (0 === $i % 2) {
            $total += $value[$index];
        } else {
            $m = $value[$index] * 2;

            if ($m > 9) {
                $m = (int) ($m / 10) + $m % 10;
            }

            $total += $m;
        }
    }

    return 0 === $total % 10;
}
