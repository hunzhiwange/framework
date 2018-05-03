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

    /**
     * 返回项目
     *
     * @param string $path
     * @return static
     */
    public static function singletons(?string $path = null);

    /**
     * 程序版本
     *
     * @return string
     */
    public function version();

    /**
     * 是否以扩展方式运行
     *
     * @return boolean
     */
    public function runWithExtension();

    /**
     * {@inheritdoc}
     */
    public function make($name, ?array $args = null);

    /**
     * 设置项目路径
     *
     * @param string $path
     * @return void
     */
    public function setPath(string $path);

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
     * 设置应用路径
     *
     * @param string $path
     * @return $this
     */
    public function setPathApplication(string $path);

    /**
     * 设置公共路径
     *
     * @param string $path
     * @return $this
     */
    public function setPathCommon(string $path);

    /**
     * 公共路径
     *
     * @return string
     */
    public function pathCommon();

    /**
     * 设置运行时路径
     *
     * @param string $path
     * @return $this
     */
    public function setPathRuntime(string $path);

    /**
     * 运行路径
     *
     * @return string
     */
    public function pathRuntime();

    /**
     * 设置存储路径
     *
     * @param string $path
     * @return $this
     */
    public function setPathStorage(string $path);

    /**
     * 附件路径
     *
     * @return string
     */
    public function pathStorage();

    /**
     * 设置配置路径
     *
     * @param string $path
     * @return $this
     */
    public function setPathOption(string $path);

    /**
     * 配置路径
     *
     * @return string
     */
    public function pathOption();

    /**
     * 环境变量路径
     *
     * @return string
     */
    public function pathEnv();

    /**
     * 设置环境变量路径
     *
     * @param string $path
     * @return $this
     */
    public function setPathEnv(string $path);

    /**
     * 设置环境变量文件
     *
     * @param string $file
     * @return $this
     */
    public function setEnvFile($file);

    /**
     * 取得环境变量文件
     *
     * @return string
     */
    public function envFile();

    /**
     * 取得环境变量完整路径
     *
     * @return string
     */
    public function fullEnvPath();

    /**
     * 应用路径
     *
     * @param string $app
     * @return string
     */
    public function pathAnApplication(?string $app = null);

    /**
     * 取得应用缓存目录
     *
     * @param string $type
     * @return string
     */
    public function pathApplicationCache($type);

    /**
     * 取得应用目录
     *
     * @param string $type
     * @return string
     */
    public function pathApplicationDir($type);

    /**
     * 返回缓存路径
     * 
     * @return 返回缓存路径
     */
    public function pathCacheOptionFile();

    /**
     * 是否缓存配置
     *
     * @return boolean
     */
    public function isCachedOption();

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
     * 创建服务提供者
     *
     * @param string $provider
     * @return \Leevel\Di\Provider
     */
    public function makeProvider($provider);

    /**
     * 执行 bootstrap
     *
     * @param \Leevel\Di\Provider $provider
     * @return void
     */
    public function callProviderBootstrap(Provider $provider);

    /**
     * 初始化项目
     * 
     * @param array $bootstraps
     * @return void
     */
    public function bootstrap(array $bootstraps);

    /**
     * 框架基础提供者 register
     *
     * @return $this
     */
    public function registerProviders();

    /**
     * 执行框架基础提供者 bootstrap
     *
     * @return $this
     */
    public function bootstrapProviders();

    /**
     * 注册服务提供者
     *
     * @param \Leevel\Di\Provider|string $provider
     * @return \Leevel\Di\Provider
     */
    public function register($provider);
}
