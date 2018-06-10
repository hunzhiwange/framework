<?php declare(strict_types=1);
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
 * (c) 2010-2018 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Leevel\Support;

use Closure;
use InvalidArgumentException;

/**
 * 类型判断辅助函数
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.05.05
 * @version 1.0
 */
class Type
{
    
    /**
     * 验证 PHP 各种变量类型
     *
     * @param mixed $value 待验证的变量
     * @param string $type 变量类型
     * @return boolean
     */
    public static function vars($value, $type)
    {
        // 整理参数，以支持 array:ini 格式
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
                if (! empty($tmp[1])) {
                    $tmp[1] = explode(',', $tmp[1]);
                    
                    return static::arr($value, $tmp[1]);
                } else {
                    return is_array($value);
                }

            // 对象
            case 'obj':
            case 'object':
                return is_object($value);

            // null
            case 'null': 
                return $value === null;

            // 回调函数
            case 'callback': 
                return is_callable($value, false);

            // 类或者接口检验
            default: 
                return $value instanceof $type;
        }
    }

    /**
     * 判断字符串是否为数字
     *
     * @param string $value
     * @since bool
     */
    public static function num($value)
    {
        if (is_numeric($value)) {
            return true;
        }

        return ! preg_match("/[^\d-.,]/", trim($value, '\''));
    }

    /**
     * 判断字符串是否为整数
     *
     * @param string $value
     * @since bool
     */
    public static function ints($value)
    {
        if (is_int($value)) {
            return true;
        }

        return ctype_digit(strval($value));
    }

    /**
     * 验证参数是否为指定的类型集合
     *
     * @param mixed $value
     * @param mixed $types
     * @return boolean
     */
    public static function these($value, $types)
    {
        if (! static::vars($types, 'string') && ! static::arr($types, [
            'string'
        ])) {
            throw new InvalidArgumentException(
                'The parameter must be string or an array of string elements.'
            );
        }

        if (is_string($types)) {
            $types = (array)$types;
        }

        // 类型检查
        foreach ($types as $item) { 
            if (static::vars($value, $item)) {
                return true;
            }
        }

        return false;
    }

    /**
     * 验证数组中的每一项格式化是否正确
     *
     * @param array $arr
     * @param array $types
     * @return boolean
     */
    public static function arr($arr, array $types)
    {
        // 不是数组直接返回
        if (! is_array($arr)) { 
            return false;
        }

        // 判断数组内部每一个值是否为给定的类型
        foreach ($arr as $value) {
            $ret = false;

            foreach ($types as $item) {
                if (static::vars($value, $item)) {
                    $ret = true;
                    break;
                }
            }

            if (! $ret) {
                return false;
            }
        }

        return true;
    }
}
