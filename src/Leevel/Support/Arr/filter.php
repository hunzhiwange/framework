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

use InvalidArgumentException;

/**
 * 返回过滤后的数据.
 *
 * @param array $input
 * @param array $rules
 *
 * @return array
 */
function filter(array $input, array $rules): array
{
    foreach ($input as $k => &$v) {
        if (is_string($v)) {
            $v = trim($v);
        }

        if (isset($rules[$k])) {
            $rule = $rules[$k];

            if (!is_array($rule)) {
                $e = sprintf('Rule of `%s` must be an array.', $k);

                throw new InvalidArgumentException($e);
            }

            foreach ($rule as $r) {
                if (!is_callable($r)) {
                    $e = sprintf('Rule item of `%s` must be a callback type.', $k);

                    throw new InvalidArgumentException($e);
                }

                $v = $r($v);
            }
        }
    }

    return $input;
}

class filter
{
}
