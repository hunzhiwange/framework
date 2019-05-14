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

namespace Leevel\Support\Arr;

/**
 * 数组数据格式化.
 *
 * @param mixed  $inputs
 * @param string $delimiter
 * @param bool   $allowedEmpty
 *
 * @return mixed
 */
function normalize($inputs, string $delimiter = ',', bool $allowedEmpty = false)
{
    if (is_array($inputs) || is_string($inputs)) {
        if (!is_array($inputs)) {
            $inputs = (array) explode($delimiter, $inputs);
        }

        $inputs = array_filter($inputs);

        if (true === $allowedEmpty) {
            return $inputs;
        }

        $inputs = array_map('trim', $inputs);

        return array_filter($inputs, 'strlen');
    }

    return $inputs;
}

class normalize
{
}
