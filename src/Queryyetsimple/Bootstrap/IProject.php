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
namespace Queryyetsimple\Bootstrap;

use Queryyetsimple\{
    Di\Provider,
    Di\IContainer
};

/**
 * IProject 接口
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.04.23
 * @version 1.0
 */
interface IProject extends IContainer
{
    
    /**
     * QueryPHP 版本
     *
     * @var string
     */
    const VERSION = '1.0.0';
    
    /**
     * 执行项目
     *
     * @return void
     */
    public function run();

    /**
     * 运行笑脸初始化应用
     *
     * @return void
     */
    public function appInit();

    /**
     * 完成路由请求
     *
     * @return void
     */
    public function appRouter();
    
    /**
     * 执行应用
     *
     * @param string $app
     * @return void
     */
    public function appRun($app);
    
    /**
     * 返回项目
     *
     * @param \Composer\Autoload\ClassLoader $objComposer
     * @param array $arrOption
     * @return $this
     */
    public static function singletons($objComposer = null, $arrOption = []);
    
    /**
     * 程序版本
     *
     * @return number
     */
    public function version();

    /**
     * 系统所有应用
     *
     * @return array
     */
    public function apps();

    /**
     * 系统所有路由文件列表
     *
     * @return array
     */
    public function routers();

    /**
     * 系统所有环境变量
     *
     * @return array
     */
    public function envs();
    
    /**
     * 基础路径
     *
     * @return string
     */
    public function path();

    /**
     * 框架路径
     *
     * @return string
     */
    public function pathFramework();
    
    /**
     * 应用路径
     *
     * @return string
     */
    public function pathApplication();
    
    /**
     * 系统错误、异常、调试和跳转模板路径
     *
     * @param string $type
     * @return string
     */
    public function pathSystem($type);

    /**
     * 公共路径
     *
     * @return string
     */
    public function pathCommon();
    
    /**
     * 运行路径
     *
     * @return string
     */
    public function pathRuntime();
    
    /**
     * 资源路径
     *
     * @return string
     */
    public function pathPublic();
    
    /**
     * 应用路径
     *
     * @return string
     */
    public function pathApplicationCurrent();
    
    /**
     * 取得应用缓存目录
     *
     * @param string $strType
     * @return string
     */
    public function pathApplicationCache($strType);
    
    /**
     * 取得应用目录
     *
     * @param string $strType
     * @return string
     */
    public function pathApplicationDir($strType);
    
    /**
     * 是否开启 debug
     *
     * @return boolean
     */
    public function debug();
    
    /**
     * 是否为开发环境
     *
     * @return string
     */
    public function development();
    
    /**
     * 运行环境
     *
     * @return boolean
     */
    public function environment();
    
    /**
     * 是否为 API
     *
     * @return boolean
     */
    public function api();
    
    /**
     * 是否为 Console
     *
     * @return boolean
     */
    public function console();

    /**
     * 返回应用配置
     *
     * @return array
     */
    public function appOption();
    
    /**
     * 创建服务提供者
     *
     * @param string $strProvider
     * @return \queryyetsimple\Di\Provider
     */
    public function makeProvider($strProvider);
    
    /**
     * 执行 bootstrap
     *
     * @param \queryyetsimple\Di\Provider $objProvider
     * @return void
     */
    public function callProviderBootstrap(Provider $objProvider);
}
