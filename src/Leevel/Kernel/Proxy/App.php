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

namespace Leevel\Kernel\Proxy;

use Leevel\Di\Container;
use Leevel\Di\IContainer;
use Leevel\Kernel\App as BaseApp;

/**
 * 代理 app.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.08.18
 *
 * @version 1.0
 * @codeCoverageIgnore
 */
class App implements IApp
{
    /**
     * call.
     *
     * @param string $method
     * @param array  $args
     *
     * @return mixed
     */
    public static function __callStatic(string $method, array $args)
    {
        return self::proxy()->{$method}(...$args);
    }

    /**
     * 程序版本.
     *
     * @return string
     */
    public static function version(): string
    {
        return self::proxy()->version();
    }

    /**
     * 是否以扩展方式运行.
     *
     * @return bool
     */
    public static function runWithExtension(): bool
    {
        return self::proxy()->runWithExtension();
    }

    /**
     * 是否为 Console.
     *
     * @return bool
     */
    public static function console(): bool
    {
        return self::proxy()->console();
    }

    /**
     * 设置应用路径.
     *
     * @param string $path
     */
    public static function setPath(string $path): void
    {
        self::proxy()->setPath($path);
    }

    /**
     * 基础路径.
     *
     * @param string $path
     *
     * @return string
     */
    public static function path(string $path = ''): string
    {
        return self::proxy()->path($path);
    }

    /**
     * 设置应用路径.
     *
     * @param string $path
     */
    public static function setAppPath(string $path): void
    {
        self::proxy()->setAppPath($path);
    }

    /**
     * 应用路径.
     *
     * @param bool|string $app
     * @param string      $path
     *
     * @return string
     */
    public static function appPath($app = false, string $path = ''): string
    {
        return self::proxy()->appPath($app, $path);
    }

    /**
     * 取得应用主题目录.
     *
     * @param bool|string $app
     *
     * @return string
     */
    public static function themePath($app = false): string
    {
        return self::proxy()->themePath($app);
    }

    /**
     * 设置公共路径.
     *
     * @param string $path
     */
    public static function setCommonPath(string $path): void
    {
        self::proxy()->setCommonPath($path);
    }

    /**
     * 公共路径.
     *
     * @param string $path
     *
     * @return string
     */
    public static function commonPath(string $path = ''): string
    {
        return self::proxy()->commonPath($path);
    }

    /**
     * 设置运行时路径.
     *
     * @param string $path
     */
    public static function setRuntimePath(string $path): void
    {
        self::proxy()->setRuntimePath($path);
    }

    /**
     * 运行路径.
     *
     * @param string $path
     *
     * @return string
     */
    public static function runtimePath(string $path = ''): string
    {
        return self::proxy()->runtimePath($path);
    }

    /**
     * 设置存储路径.
     *
     * @param string $path
     */
    public static function setStoragePath(string $path): void
    {
        self::proxy()->setStoragePath($path);
    }

    /**
     * 附件路径.
     *
     * @param string $path
     *
     * @return string
     */
    public static function storagePath(string $path = ''): string
    {
        return self::proxy()->storagePath($path);
    }

    /**
     * 设置资源路径.
     *
     * @param string $path
     */
    public static function setPublicPath(string $path): void
    {
        self::proxy()->setPublicPath($path);
    }

    /**
     * 资源路径.
     *
     * @param string $path
     *
     * @return string
     */
    public static function publicPath(string $path = ''): string
    {
        return self::proxy()->publicPath($path);
    }

    /**
     * 设置主题路径.
     *
     * @param string $path
     */
    public static function setThemesPath(string $path): void
    {
        self::proxy()->setThemesPath($path);
    }

    /**
     * 主题路径.
     *
     * @param string $path
     *
     * @return string
     */
    public static function themesPath(string $path = ''): string
    {
        return self::proxy()->themesPath($path);
    }

    /**
     * 设置配置路径.
     *
     * @param string $path
     */
    public static function setOptionPath(string $path): void
    {
        self::proxy()->setOptionPath($path);
    }

    /**
     * 配置路径.
     *
     * @param string $path
     *
     * @return string
     */
    public static function optionPath(string $path = ''): string
    {
        return self::proxy()->optionPath($path);
    }

    /**
     * 设置语言包路径.
     *
     * @param string $path
     */
    public static function setI18nPath(string $path): void
    {
        self::proxy()->setI18nPath($path);
    }

    /**
     * 语言包路径.
     *
     * @param string $path
     *
     * @return string
     */
    public static function i18nPath(?string $path = null): string
    {
        return self::proxy()->i18nPath($path);
    }

    /**
     * 设置环境变量路径.
     *
     * @param string $path
     */
    public static function setEnvPath(string $path): void
    {
        self::proxy()->setEnvPath($path);
    }

    /**
     * 环境变量路径.
     *
     * @return string
     */
    public static function envPath(): string
    {
        return self::proxy()->envPath();
    }

    /**
     * 设置环境变量文件.
     *
     * @param string $file
     */
    public static function setEnvFile(string $file): void
    {
        self::proxy()->setEnvFile($file);
    }

    /**
     * 取得环境变量文件.
     *
     * @return string
     */
    public static function envFile(): string
    {
        return self::proxy()->envFile();
    }

    /**
     * 取得环境变量完整路径.
     *
     * @return string
     */
    public static function fullEnvPath(): string
    {
        return self::proxy()->fullEnvPath();
    }

    /**
     * 设置语言包缓存路径.
     *
     * @param string $i18nCachedPath
     */
    public static function setI18nCachedPath(string $i18nCachedPath): void
    {
        self::proxy()->setI18nCachedPath($i18nCachedPath);
    }

    /**
     * 返回语言包缓存路径.
     *
     * @param string $i18n
     *
     * @return string
     */
    public static function i18nCachedPath(string $i18n): string
    {
        return self::proxy()->i18nCachedPath($i18n);
    }

    /**
     * 是否存在语言包缓存.
     *
     * @param string $i18n
     *
     * @return bool
     */
    public static function isCachedI18n(string $i18n): bool
    {
        return self::proxy()->isCachedI18n($i18n);
    }

    /**
     * 设置配置缓存路径.
     *
     * @param string $optionCachedPath
     */
    public static function setOptionCachedPath(string $optionCachedPath): void
    {
        self::proxy()->setOptionCachedPath($optionCachedPath);
    }

    /**
     * 返回配置缓存路径.
     *
     * @return string
     */
    public static function optionCachedPath(): string
    {
        return self::proxy()->optionCachedPath();
    }

    /**
     * 是否存在配置缓存.
     *
     * @return bool
     */
    public static function isCachedOption(): bool
    {
        return self::proxy()->isCachedOption();
    }

    /**
     * 设置路由缓存路径.
     *
     * @param string $routerCachedPath
     */
    public static function setRouterCachedPath(string $routerCachedPath): void
    {
        self::proxy()->setRouterCachedPath($routerCachedPath);
    }

    /**
     * 返回路由缓存路径.
     *
     * @return string
     */
    public static function routerCachedPath(): string
    {
        return self::proxy()->routerCachedPath();
    }

    /**
     * 是否存在路由缓存.
     *
     * @return bool
     */
    public static function isCachedRouter(): bool
    {
        return self::proxy()->isCachedRouter();
    }

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
    public static function namespacePath(string $specificClass, bool $throwException = true): string
    {
        return self::proxy()->namespacePath($specificClass, $throwException);
    }

    /**
     * 是否开启 debug.
     *
     * @return bool
     */
    public static function debug(): bool
    {
        return self::proxy()->debug();
    }

    /**
     * 是否为开发环境.
     *
     * @return bool
     */
    public static function development(): bool
    {
        return self::proxy()->development();
    }

    /**
     * 运行环境.
     *
     * @return string
     */
    public static function environment(): string
    {
        return self::proxy()->environment();
    }

    /**
     * 初始化应用.
     *
     * @param array $bootstraps
     */
    public static function bootstrap(array $bootstraps): void
    {
        self::proxy()->bootstrap($bootstraps);
    }

    /**
     * 框架基础提供者 register.
     */
    public static function registerProviders(): void
    {
        self::proxy()->registerProviders();
    }

    /**
     * 返回 IOC 容器.
     *
     * @return \Leevel\Di\IContainer
     */
    public static function container(): IContainer
    {
        return self::proxy()->container();
    }

    /**
     * 代理服务
     *
     * @return \Leevel\Kernel\App
     */
    public static function proxy(): BaseApp
    {
        return Container::singletons()->make('app');
    }
}
