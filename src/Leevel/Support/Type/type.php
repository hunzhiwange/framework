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
 * 验证 PHP 各种变量类型.
 *
 * - 支持 PHP 内置或者自定义的 is_array,is_int,is_custom 等函数
 * - 数组支持 array:int,string 格式，值类型
 * - 数组支持 array:int:string,string:array 格式，键类型:值类型
 * - 数组支持 array:string:array:string:array:string:int 无限层级格式，键类型:值类型:键类型:值类型...(值类型|键类型:值类型)
 *
 * @see https://www.php.net/manual/zh/function.is-array.php
 */
function type(mixed $value, string $type): bool
{
    if (0 === strpos($type, 'array:')) {
        return arr($value, explode(',', substr($type, 6)));
    }

    if (function_exists($isTypeFunction = 'is_'.$type)) {
        return $isTypeFunction($value);
    }

    return $value instanceof $type;
}

class type
{
}

// import fn.
class_exists(arr::class);
