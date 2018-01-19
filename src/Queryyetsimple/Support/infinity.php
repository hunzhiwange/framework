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
namespace queryyetsimple\support;

use Closure;
use ReflectionFunction;
use BadMethodCallException;

/**
 * 实现类的无限扩展功能
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.05.04
 * @version 1.0
 */
trait infinity
{

    /**
     * 注册的动态扩展
     *
     * @var array
     */
    protected static $arrInfinity = [];

    /**
     * 注册一个扩展
     *
     * @param string $strName
     * @param callable $calInfinity
     * @return void
     */
    public static function infinity($strName, callable $calInfinity)
    {
        static::$arrInfinity[$strName] = $calInfinity;
    }

    /**
     * 判断一个扩展是否注册
     *
     * @param string $strName
     * @return bool
     */
    public static function hasInfinity($strName)
    {
        return isset(static::$arrInfinity[$strName]);
    }

    /**
     * call 
     *
     * @param string $sMethod
     * @param array $arrArgs
     * @return mixed
     */
    public static function __callStatic(string $sMethod, array $arrArgs)
    {
        // 第一步：判断是否存在已经注册的命名
        if (static::hasInfinity($sMethod)) {
            if (static::$arrInfinity[$sMethod] instanceof Closure) {
                return call_user_func_array(Closure::bind(static::$arrInfinity[$sMethod], null, get_called_class()), $arrArgs);
            } else {
                return call_user_func_array(static::$arrInfinity[$sMethod], $arrArgs);
            }
        }

        throw new BadMethodCallException(sprintf('Method %s is not exits.', $sMethod));
    }

    /**
     * call 
     *
     * @param string $sMethod
     * @param array $arrArgs
     * @return mixed
     */
    public function __call(string $sMethod, array $arrArgs)
    {
        if (static::hasInfinity($sMethod)) {
            if (static::$arrInfinity[$sMethod] instanceof Closure) {
                $objReflection = new ReflectionFunction(static::$arrInfinity[$sMethod]);
                return call_user_func_array(Closure::bind(static::$arrInfinity[$sMethod], $objReflection->getClosureThis() ? $this : null, get_class($this)), $arrArgs);
            } else {
                return call_user_func_array(static::$arrInfinity[$sMethod], $arrArgs);
            }
        }

        throw new BadMethodCallException(sprintf('Method %s is not exits.', $sMethod));
    }
}
