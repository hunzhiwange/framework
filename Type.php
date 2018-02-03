<?php
/*
 * This file is part of the ************************ package.
 * ##########################################################
 * #   ____                          ______  _   _ ______   #
 * #  /     \       ___  _ __  _   _ | ___ \| | | || ___ \  #
 * # |   (  ||(_)| / _ \| '__|| | | || |_/ /| |_| || |_/ /  #
 * #  \____/ |___||  __/| |   | |_| ||  __/ |  _  ||  __/   #
 * #       \__   | \___ |_|    \__  || |    | | | || |      #
 * #     Query Yet Simple      __/  |\_|    |_| |_|\_|      #
 * #                          |___ /  Since 2010.10.03      #
 * ##########################################################
 *
 * The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
 * (c) 2010-2018 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Queryyetsimple\Support;

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
     * @param mixed $mixVar 待验证的变量
     * @param string $sType 变量类型
     * @return boolean
     */
    public static function var($mixVar, $sType)
    {
        // 整理参数，以支持 array:ini 格式
        $sType = trim($sType); 
        $sType = explode(':', $sType);
        $sType[0] = strtolower($sType[0]);

        switch ($sType[0]) {

            // 字符串
            case 'string': 
                return is_string($mixVar);

            // 整数
            case 'integer': 
            case 'int':
                return is_int($mixVar);

            // 浮点
            case 'float': 
                return is_float($mixVar);

            // 布尔
            case 'boolean': 
            case 'bool':
                return is_bool($mixVar);

            // 数字
            case 'num': 
            case 'numeric':
                return is_numeric($mixVar);

            // 标量（所有基础类型）
            case 'base': 
            case 'scalar':
                return is_scalar($mixVar);

            // 外部资源
            case 'handle': 
            case 'resource':
                return is_resource($mixVar);

            // 闭包
            case 'closure': 
                return $mixVar instanceof Closure;

            // 数组
            case 'array':
                if (! empty($sType[1])) {
                    $sType[1] = explode(',', $sType[1]);
                    return static::arr($mixVar, $sType[1]);
                } else {
                    return is_array($mixVar);
                }

            // 对象
            case 'object': 
                return is_object($mixVar);

            // bull
            case 'null': 
                return ($mixVar === null);

            // 回调函数
            case 'callback': 
                return is_callable($mixVar, false);

            // 类或者接口检验
            default: 
                return $mixVar instanceof $sType[0];
        }
    }

    /**
     * 判断字符串是否为数字
     *
     * @param string $strSearch
     * @since bool
     */
    public static function num($mixValue)
    {
        if (is_numeric($mixValue)) {
            return true;
        }
        return ! preg_match("/[^\d-.,]/", trim($mixValue, '\''));
    }

    /**
     * 判断字符串是否为整数
     *
     * @param string $strSearch
     * @since bool
     */
    public static function int($mixValue)
    {
        if (is_int($mixValue)) {
            return true;
        }
        return ctype_digit(strval($mixValue));
    }


    /**
     * 验证参数是否为指定的类型集合
     *
     * @param mixed $mixVar
     * @param mixed $mixTypes
     * @return boolean
     */
    public static function these($mixVar, $mixTypes)
    {
        if (! static::var($mixTypes, 'string') && ! static::arr($mixTypes, [
            'string'
        ])) {
            throw new InvalidArgumentException('The parameter must be string or an array of string elements.');
        }

        if (is_string($mixTypes)) {
            $mixTypes = ( array ) $mixTypes;
        }

        // 类型检查
        foreach ($mixTypes as $sType) { 
            if (static::var($mixVar, $sType)) {
                return true;
            }
        }

        return false;
    }

    /**
     * 验证数组中的每一项格式化是否正确
     *
     * @param array $arrArray
     * @param array $arrTypes
     * @return boolean
     */
    public static function arr($arrArray, array $arrTypes)
    {
        // 不是数组直接返回
        if (! is_array($arrArray)) { 
            return false;
        }

        // 判断数组内部每一个值是否为给定的类型
        foreach ($arrArray as &$mixValue) {
            $bRet = false;
            foreach ($arrTypes as $mixType) {
                if (static::var($mixValue, $mixType)) {
                    $bRet = true;
                    break;
                }
            }

            if (! $bRet) {
                return false;
            }
        }

        return true;
    }
}
