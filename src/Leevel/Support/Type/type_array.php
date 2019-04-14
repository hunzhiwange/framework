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

namespace Leevel\Support\Type;

/**
 * 验证数组中的每一项格式化是否正确.
 *
 * @param mixed $arr
 * @param array $types
 *
 * @return bool
 */
function type_array($arr, array $types): bool
{
    // 不是数组直接返回
    if (!is_array($arr)) {
        return false;
    }

    // 判断数组内部每一个值是否为给定的类型
    foreach ($arr as $value) {
        $ret = false;

        foreach ($types as $item) {
            if (type($value, $item)) {
                $ret = true;

                break;
            }
        }

        if (!$ret) {
            return false;
        }
    }

    return true;
}

// @codeCoverageIgnoreStart
if (!function_exists('Leevel\\Support\\Type\\type')) {
    include __DIR__.'/type.php';
}
// @codeCoverageIgnoreEnd
