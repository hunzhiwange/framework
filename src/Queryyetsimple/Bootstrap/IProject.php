<?php
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
namespace Leevel\Bootstrap;

use Leevel\{
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
     * 执行项目
     *
     * @return void
     */
    public function run();

    // /**
    //  * 运行笑脸初始化应用
    //  *
    //  * @return void
    //  */
    // public function appInit();

    // /**
    //  * 完成路由请求
    //  *
    //  * @return void
    //  */
    // public function appRouter();
    
    // /**
    //  * 执行应用
    //  *
    //  * @param string $app
    //  * @return void
    //  */
    // public function appRun($app);

    /**
     * 是否以扩展方式运行
     *
     * @return boolean
     */
    public function runWithExtension();
    
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
     * @return string
     */
    public function version();

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
     * 取得 composer
     *
     * @return \Composer\Autoload\ClassLoader
     */
    public function composer();

    /**
     * 获取命名空间路径
     *
     * @param string $namespaces
     * @return string|null
     */
    public function getPathByNamespace($namespaces);
    
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
     * @return \leevel\Di\Provider
     */
    public function makeProvider($strProvider);
    
    /**
     * 执行 bootstrap
     *
     * @param \leevel\Di\Provider $objProvider
     * @return void
     */
    public function callProviderBootstrap(Provider $objProvider);

    /**
     * QueryPHP 版本
     *
     * @var string
     */
    const VERSION = '1.0.0';
    
    /**
     * 默认环境变量名字
     *
     * @var string
     */
    const DEFAULT_ENV = '.env';
}
