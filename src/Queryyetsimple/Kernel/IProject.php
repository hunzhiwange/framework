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

use Composer\Autoload\ClassLoader;
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
    public function runWithExtension(): bool;

    /**
     * 是否为 Console.
     *
     * @return bool
     */
    public function console(): bool;

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
     * 设置应用路径.
     *
     * @param string $path
     *
     * @return $this
     */
    public function setAppPath(string $path);

    /**
     * 应用路径.
     *
     * @param bool|string $app
     * @param string      $path
     *
     * @return string
     */
    public function appPath($app = false, string $path = '');

    /**
     * 取得应用主题目录.
     *
     * @param bool|string $app
     *
     * @return string
     */
    public function themePath($app = false);

    /**
     * 设置公共路径.
     *
     * @param string $path
     *
     * @return $this
     */
    public function setCommonPath(string $path);

    /**
     * 公共路径.
     *
     * @param string $path
     *
     * @return string
     */
    public function commonPath(string $path = '');

    /**
     * 设置运行时路径.
     *
     * @param string $path
     *
     * @return $this
     */
    public function setRuntimePath(string $path);

    /**
     * 运行路径.
     *
     * @param string $path
     *
     * @return string
     */
    public function runtimePath(string $path = '');

    /**
     * 设置存储路径.
     *
     * @param string $path
     *
     * @return $this
     */
    public function setStoragePath(string $path);

    /**
     * 附件路径.
     *
     * @param string $path
     *
     * @return string
     */
    public function storagePath(string $path = '');

    /**
     * 设置资源路径.
     *
     * @param string $path
     */
    public function setPublicPath(string $path);

    /**
     * 资源路径.
     *
     * @param string $path
     *
     * @return string
     */
    public function publicPath(string $path = '');

    /**
     * 设置主题路径.
     *
     * @param string $path
     */
    public function setThemesPath(string $path);

    /**
     * 主题路径.
     *
     * @param string $path
     *
     * @return string
     */
    public function themesPath(string $path = '');

    /**
     * 设置配置路径.
     *
     * @param string $path
     *
     * @return $this
     */
    public function setOptionPath(string $path);

    /**
     * 配置路径.
     *
     * @param string $path
     *
     * @return string
     */
    public function optionPath(string $path = '');

    /**
     * 设置语言包路径.
     *
     * @param string $path
     *
     * @return $this
     */
    public function setI18nPath(string $path);

    /**
     * 语言包路径.
     *
     * @param string $path
     *
     * @return string
     */
    public function i18nPath($path = null);

    /**
     * 设置环境变量路径.
     *
     * @param string $path
     *
     * @return $this
     */
    public function setEnvPath(string $path);

    /**
     * 环境变量路径.
     *
     * @return string
     */
    public function envPath();

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
    public function i18nCachedPath($i18n): string;

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
    public function optionCachedPath(): string;

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
    public function routerCachedPath(): string;

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
     * @codeCoverageIgnore
     */
    public function composer(): ClassLoader;

    /**
     * 获取命名空间路径.
     *
     * @param string $namespaces
     *
     * @return null|string
     * @codeCoverageIgnore
     */
    public function getPathByComposer($namespaces);

    /**
     * 是否开启 debug.
     *
     * @return bool
     */
    public function debug(): bool;

    /**
     * 是否为开发环境.
     *
     * @return bool
     */
    public function development(): bool;

    /**
     * 运行环境.
     *
     * @return string
     */
    public function environment(): string;

    /**
     * 创建服务提供者.
     *
     * @param string $provider
     *
     * @return \Leevel\Di\Provider
     */
    public function makeProvider(string $provider): Provider;

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
    public function register($provider): Provider;
}
