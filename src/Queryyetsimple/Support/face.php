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
use Exception;
use RuntimeException;
use BadMethodCallException;

/**
 * 实现类的静态访问门面
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.05.04
 * @version 1.0
 */
abstract class face
{

    /**
     * 项目容器
     *
     * @var \queryyetsimple\support\icontainer
     */
    protected static $container;

    /**
     * 注入容器实例
     *
     * @var object
     */
    protected static $instances = [];

    /**
     * 获取注册容器的实例
     *
     * @return mixed
     */
    public static function faces()
    {
        $unique = static::name();

        if (isset(self::$instances[$unique])) {
            return self::$instances[$unique];
        }

        if (! is_object(self::$instances[$unique] = self::container()->make($unique))) {
            throw new RuntimeException(sprintf('Services %s was not found in the IOC container.', $unique));
        }

        return self::$instances[$unique];
    }

    /**
     * 返回服务容器
     *
     * @return \queryyetsimple\support\icontainer
     */
    public static function container(): icontainer
    {
        return self::$container;
    }

    /**
     * 设置服务容器
     *
     * @param \queryyetsimple\support\icontainer $container
     * @return void
     */
    public static function setContainer(icontainer $container): void
    {
        self::$container = $container;
    }

    /**
     * 门面名字
     *
     * @return string
     */
    protected static function name(): string {
        return '';
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
        $instance = static::faces();
        if (! $instance) {
            throw new RuntimeException('Can not find instance from container.');
        }

        $method = [
            $instance,
            $method
        ];
        if (! is_callable($method)) {
            throw new BadMethodCallException(sprintf('Method %s is not exits.', $method));
        }

        return call_user_func_array($method, $args);
    }
}
