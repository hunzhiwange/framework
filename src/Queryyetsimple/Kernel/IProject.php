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
 * (c) 2010-2018 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Leevel\Kernel;

use Leevel\Di\IContainer;
use Leevel\Di\Provider;

/**
 * IProject 接口.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.04.23
 *
 * @version 1.0
 */
interface IProject extends IContainer
{
    /**
     * QueryPHP 版本.
     *
     * @var string
     */
    const VERSION = '1.0.0';

    /**
     * 默认环境变量名字.
     *
     * @var string
     */
    const DEFAULT_ENV = '.env';

    /**
     * 返回项目.
     *
     * @param string $path
     *
     * @return static
     */
    public static function singletons(?string $path = null);

    /**
     * 程序版本.
     *
     * @return string
     */
    public function version();

    /**
     * 是否以扩展方式运行.
     *
     * @return bool
     */
    public function runWithExtension();

    /**
     * {@inheritdoc}
     */
    public function make($name, ?array $args = null);

    /**
     * 设置项目路径.
     *
     * @param string $path
     */
    public function setPath(string $path);

    /**
     * 基础路径.
     *
     * @param string $path
     *
     * @return string
     */
    public function path(string $path = '');

    /**
     * 应用路径.
     *
     * @param mixed  $app
     * @param string $path
     *
     * @return string
     */
    public function pathApp($app = true, string $path = '');

    /**
     * 设置应用路径.
     *
     * @param string $path
     *
     * @return $this
     */
    public function setPathApp(string $path);

    /**
     * 取得应用主题目录.
     *
     * @param string $app
     *
     * @return string
     */
    public function pathTheme(?string $app = null);

    /**
     * 设置公共路径.
     *
     * @param string $path
     *
     * @return $this
     */
    public function setPathCommon(string $path);

    /**
     * 公共路径.
     *
     * @param string $path
     *
     * @return string
     */
    public function pathCommon(string $path = '');

    /**
     * 设置运行时路径.
     *
     * @param string $path
     *
     * @return $this
     */
    public function setPathRuntime(string $path);

    /**
     * 运行路径.
     *
     * @param string $path
     *
     * @return string
     */
    public function pathRuntime(string $path = '');

    /**
     * 设置存储路径.
     *
     * @param string $path
     *
     * @return $this
     */
    public function setPathStorage(string $path);

    /**
     * 附件路径.
     *
     * @param string $path
     *
     * @return string
     */
    public function pathStorage(string $path = '');

    /**
     * 设置配置路径.
     *
     * @param string $path
     *
     * @return $this
     */
    public function setPathOption(string $path);

    /**
     * 配置路径.
     *
     * @return string
     */
    public function pathOption();

    /**
     * 设置语言包路径.
     *
     * @param string $path
     *
     * @return $this
     */
    public function setPathI18n(string $path);

    /**
     * 语言包路径.
     *
     * @return string
     */
    public function pathI18n();

    /**
     * 环境变量路径.
     *
     * @return string
     */
    public function pathEnv();

    /**
     * 设置环境变量路径.
     *
     * @param string $path
     *
     * @return $this
     */
    public function setPathEnv(string $path);

    /**
     * 设置环境变量文件.
     *
     * @param string $file
     *
     * @return $this
     */
    public function setEnvFile($file);

    /**
     * 取得环境变量文件.
     *
     * @return string
     */
    public function envFile();

    /**
     * 取得环境变量完整路径.
     *
     * @return string
     */
    public function fullEnvPath();

    /**
     * 返回语言包缓存路径.
     *
     * @param string $i18n
     *
     * @return string
     */
    public function pathCacheI18nFile(string $i18n): string;

    /**
     * 是否存在语言包缓存.
     *
     * @param string $i18n
     *
     * @return bool
     */
    public function isCachedI18n(string $i18n): bool;

    /**
     * 返回配置缓存路径.
     *
     * @return string
     */
    public function pathCacheOptionFile(): string;

    /**
     * 是否存在配置缓存.
     *
     * @return bool
     */
    public function isCachedOption(): bool;

    /**
     * 返回路由缓存路径.
     *
     * @return string
     */
    public function pathCacheRouterFile(): string;

    /**
     * 是否存在路由缓存.
     *
     * @return bool
     */
    public function isCachedRouter(): bool;

    /**
     * 取得 composer.
     *
     * @return \Composer\Autoload\ClassLoader
     */
    public function composer();

    /**
     * 获取命名空间路径.
     *
     * @param string $namespaces
     *
     * @return null|string
     */
    public function getPathByNamespace(string $namespaces);

    /**
     * 批量获取命名空间路径.
     *
     * @param array $namespaces
     *
     * @return array
     */
    public function getPathByNamespaces(array $namespaces): array;

    /**
     * 是否开启 debug.
     *
     * @return bool
     */
    public function debug();

    /**
     * 是否为开发环境.
     *
     * @return string
     */
    public function development();

    /**
     * 运行环境.
     *
     * @return bool
     */
    public function environment();

    /**
     * 是否为 API.
     *
     * @return bool
     */
    public function api();

    /**
     * 是否为 Console.
     *
     * @return bool
     */
    public function console();

    /**
     * 创建服务提供者.
     *
     * @param string $provider
     *
     * @return \Leevel\Di\Provider
     */
    public function makeProvider($provider);

    /**
     * 执行 bootstrap.
     *
     * @param \Leevel\Di\Provider $provider
     */
    public function callProviderBootstrap(Provider $provider);

    /**
     * 初始化项目.
     *
     * @param array $bootstraps
     */
    public function bootstrap(array $bootstraps);

    /**
     * 是否已经初始化引导
     *
     * @return bool
     */
    public function isBootstrap(): bool;

    /**
     * 框架基础提供者 register.
     *
     * @return $this
     */
    public function registerProviders();

    /**
     * 执行框架基础提供者 bootstrap.
     *
     * @return $this
     */
    public function bootstrapProviders();

    /**
     * 注册服务提供者.
     *
     * @param \Leevel\Di\Provider|string $provider
     *
     * @return \Leevel\Di\Provider
     */
    public function register($provider);
}
