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
trait Macro
{

    /**
     * 注册的动态扩展
     *
     * @var array
     */
    protected static $macro = [];

    /**
     * 注册一个扩展
     *
     * @param string $name
     * @param callable $macro
     * @return void
     */
    public static function macro($name, callable $macro)
    {
        static::$macro[$name] = $macro;
    }

    /**
     * 判断一个扩展是否注册
     *
     * @param string $name
     * @return bool
     */
    public static function hasMacro($name)
    {
        return isset(static::$macro[$name]);
    }

    /**
     * call 
     *
     * @param string $method
     * @param array $args
     * @return mixed
     */
    public static function __callStatic(string $method, array $args)
    {
        // 第一步：判断是否存在已经注册的命名
        if (static::hasMacro($method)) {
            if (static::$macro[$method] instanceof Closure) {
                return call_user_func_array(Closure::bind(static::$macro[$method], null, get_called_class()), $args);
            } else {
                return call_user_func_array(static::$macro[$method], $args);
            }
        }

        throw new BadMethodCallException(sprintf('Method %s is not exits.', $method));
    }

    /**
     * call 
     *
     * @param string $method
     * @param array $args
     * @return mixed
     */
    public function __call(string $method, array $args)
    {
        if (static::hasMacro($method)) {
            if (static::$macro[$method] instanceof Closure) {
                $reflection = new ReflectionFunction(static::$macro[$method]);

                return call_user_func_array(Closure::bind(static::$macro[$method], $reflection->getClosureThis() ? $this : null, get_class($this)), $args);
            } else {
                return call_user_func_array(static::$macro[$method], $args);
            }
        }

        throw new BadMethodCallException(sprintf('Method %s is not exits.', $method));
    }
}
