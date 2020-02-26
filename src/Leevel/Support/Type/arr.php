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

namespace Leevel\Support\Type;

/**
 * 验证数组中的每一项类型是否正确.
 *
 * - 数组支持 int,string 格式，值类型
 * - 数组支持 int:string,string:array 格式，键类型:值类型
 * - 数组支持 string:array:string:array:string:int 无限层级格式，键类型:值类型:键类型:值类型...(值类型|键类型:值类型)
 */
function arr(array $data, array $types): bool
{
    foreach ($data as $key => $value) {
        $result = false;
        foreach ($types as $type) {
            if (false !== ($position = strpos($type, ':'))) {
                if (type($key, substr($type, 0, $position)) &&
                    type($value, substr($type, $position + 1))) {
                    $result = true;

                    break;
                }
            } else {
                if (type($value, $type)) {
                    $result = true;

                    break;
                }
            }
        }

        if (false === $result) {
            return false;
        }
    }

    return true;
}

class arr
{
}

// import fn.
class_exists(type::class);
