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

use Closure;

/**
 * 验证 PHP 各种变量类型.
 *
 * @param mixed  $value
 * @param string $type
 *
 * @return bool
 */
function type($value, string $type): bool
{
    // 整理参数，以支持 array:int 格式
    $tmp = explode(':', $type);
    $type = strtolower($tmp[0]);

    switch ($type) {
        // 字符串
        case 'str':
        case 'string':
            return is_string($value);
        // 整数
        case 'int':
        case 'integer':
            return is_int($value);
        // 浮点
        case 'float':
        case 'double':
            return is_float($value);
        // 布尔
        case 'bool':
        case 'boolean':
            return is_bool($value);
        // 数字
        case 'num':
        case 'numeric':
            return is_numeric($value);
        // 标量（所有基础类型）
        case 'base':
        case 'scalar':
            return is_scalar($value);
        // 外部资源
        case 'handle':
        case 'resource':
            return is_resource($value);
        // 闭包
        case 'closure':
            return $value instanceof Closure;
        // 数组
        case 'arr':
        case 'array':
            if (!empty($tmp[1])) {
                $tmp[1] = explode(',', $tmp[1]);

                return type_array($value, $tmp[1]);
            }

            return is_array($value);
        // 对象
        case 'obj':
        case 'object':
            return is_object($value);
        // null
        case 'null':
            return null === $value;
        // 回调函数
        case 'callback':
            return is_callable($value, false);
        // 类或者接口检验
        default:
            return $value instanceof $type;
    }
}

// @codeCoverageIgnoreStart
if (!function_exists('Leevel\\Support\\Type\\type_array')) {
    include __DIR__.'/type_array.php';
}
// @codeCoverageIgnoreEnd
