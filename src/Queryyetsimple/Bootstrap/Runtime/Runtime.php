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
namespace Queryyetsimple\Bootstrap\Runtime;

use Queryyetsimple\Di\IContainer;

/**
 * 异常响应
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.04.09
 * @version 1.0
 */
class Runtime
{

    /**
     * 服务容器
     * 
     * @var \Queryyetsimple\Di\IContainer
     */
    protected static $container;

    /**
     * 接管 PHP 异常
     *
     * @param Exception $oException
     * @return void
     */
    public static function exceptionHandle($oException)
    {
        (new exception(static::$container, $oException))->run();
    }
    
    /**
     * 接管 PHP 错误
     *
     * @param int $nErrorNo
     * @param string $sErrStr
     * @param string $sErrFile
     * @param int $nErrLine
     * @return void
     */
    public static function errorHandle($nErrorNo, $sErrStr, $sErrFile, $nErrLine)
    {
        (new error(static::$container, $nErrorNo, $sErrStr, $sErrFile, $nErrLine))->run();
    }
    
    /**
     * 接管 PHP 致命错误
     *
     * @return void
     */
    public static function shutdownHandle()
    {
        (new shutdown(static::$container))->run();
    }
    
    /**
     * 设置项目容器
     *
     * @param \Queryyetsimple\Di\IContainer $container
     * @return void
     */
    public static function container(IContainer $container)
    {
        static::$container = $container;
    }
}
