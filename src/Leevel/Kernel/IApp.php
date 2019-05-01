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
 * (c) 2010-2019 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Leevel\Kernel;

use Composer\Autoload\ClassLoader;
use Leevel\Di\IContainer;
use Leevel\Di\Provider;

/**
 * 应用接口.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.04.23
 *
 * @version 1.0
 */
interface IApp extends IContainer
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
     * 返回应用.
     *
     * @param string $path
     *
     * @return static
     */
    public static function singletons(?string $path = null): self;

    /**
     * 程序版本.
     *
     * @return string
     */
    public function version(): string;

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
     * 设置应用路径.
     *
     * @param string $path
     */
    public function setPath(string $path): void;

    /**
     * 基础路径.
     *
     * @param string $path
     *
     * @return string
     */
    public function path(string $path = ''): string;

    /**
     * 设置应用路径.
     *
     * @param string $path
     */
    public function setAppPath(string $path): void;

    /**
     * 应用路径.
     *
     * @param bool|string $app
     * @param string      $path
     *
     * @return string
     */
    public function appPath($app = false, string $path = ''): string;

    /**
     * 取得应用主题目录.
     *
     * @param bool|string $app
     *
     * @return string
     */
    public function themePath($app = false): string;

    /**
     * 设置公共路径.
     *
     * @param string $path
     */
    public function setCommonPath(string $path): void;

    /**
     * 公共路径.
     *
     * @param string $path
     *
     * @return string
     */
    public function commonPath(string $path = ''): string;

    /**
     * 设置运行时路径.
     *
     * @param string $path
     */
    public function setRuntimePath(string $path): void;

    /**
     * 运行路径.
     *
     * @param string $path
     *
     * @return string
     */
    public function runtimePath(string $path = ''): string;

    /**
     * 设置存储路径.
     *
     * @param string $path
     */
    public function setStoragePath(string $path): void;

    /**
     * 附件路径.
     *
     * @param string $path
     *
     * @return string
     */
    public function storagePath(string $path = ''): string;

    /**
     * 设置资源路径.
     *
     * @param string $path
     */
    public function setPublicPath(string $path): void;

    /**
     * 资源路径.
     *
     * @param string $path
     *
     * @return string
     */
    public function publicPath(string $path = ''): string;

    /**
     * 设置主题路径.
     *
     * @param string $path
     */
    public function setThemesPath(string $path): void;

    /**
     * 主题路径.
     *
     * @param string $path
     *
     * @return string
     */
    public function themesPath(string $path = ''): string;

    /**
     * 设置配置路径.
     *
     * @param string $path
     */
    public function setOptionPath(string $path): void;

    /**
     * 配置路径.
     *
     * @param string $path
     *
     * @return string
     */
    public function optionPath(string $path = ''): string;

    /**
     * 设置语言包路径.
     *
     * @param string $path
     */
    public function setI18nPath(string $path): void;

    /**
     * 语言包路径.
     *
     * @param string $path
     *
     * @return string
     */
    public function i18nPath(?string $path = null): string;

    /**
     * 设置环境变量路径.
     *
     * @param string $path
     */
    public function setEnvPath(string $path): void;

    /**
     * 环境变量路径.
     *
     * @return string
     */
    public function envPath(): string;

    /**
     * 设置环境变量文件.
     *
     * @param string $file
     */
    public function setEnvFile(string $file): void;

    /**
     * 取得环境变量文件.
     *
     * @return string
     */
    public function envFile(): string;

    /**
     * 取得环境变量完整路径.
     *
     * @return string
     */
    public function fullEnvPath(): string;

    /**
     * 设置语言包缓存路径.
     *
     * @param string $i18nCachedPath
     */
    public function setI18nCachedPath(string $i18nCachedPath): void;

    /**
     * 返回语言包缓存路径.
     *
     * @param string $i18n
     *
     * @return string
     */
    public function i18nCachedPath(string $i18n): string;

    /**
     * 是否存在语言包缓存.
     *
     * @param string $i18n
     *
     * @return bool
     */
    public function isCachedI18n(string $i18n): bool;

    /**
     * 设置配置缓存路径.
     *
     * @param string $optionCachedPath
     */
    public function setOptionCachedPath(string $optionCachedPath): void;

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
     * 设置路由缓存路径.
     *
     * @param string $routerCachedPath
     */
    public function setRouterCachedPath(string $routerCachedPath);

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
     * 设置 Composer 对象.
     *
     * @param \Composer\Autoload\ClassLoader $composer
     */
    public function setComposer(ClassLoader $composer): void;

    /**
     * 取得 Composer 对象.
     *
     * @return \Composer\Autoload\ClassLoader
     */
    public function composer(): ClassLoader;

    /**
     * 获取命名空间目录真实路径.
     *
     * 一般用于获取文件 PSR4 所在的命名空间，当然如果存在命名空间。
     * 基于某个具体的类查询该类目录的真实路径。
     * 为简化开发和提升性能，必须提供具体的存在的类才能够获取目录的真实路径。
     *
     * @param string $specificClass
     * @param bool   $throwException
     *
     * @return string
     */
    public function namespacePath(string $specificClass, bool $throwException = true): string;

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
    public function callProviderBootstrap(Provider $provider): void;

    /**
     * 初始化应用.
     *
     * @param array $bootstraps
     */
    public function bootstrap(array $bootstraps): void;

    /**
     * 是否已经初始化引导
     *
     * @return bool
     */
    public function isBootstrap(): bool;

    /**
     * 框架基础提供者 register.
     */
    public function registerProviders(): void;

    /**
     * 执行框架基础提供者 bootstrap.
     */
    public function bootstrapProviders(): void;

    /**
     * 注册服务提供者.
     *
     * @param \Leevel\Di\Provider|string $provider
     *
     * @return \Leevel\Di\Provider
     */
    public function register($provider): Provider;
}
