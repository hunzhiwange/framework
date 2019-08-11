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

use Leevel\Di\IContainer;

/**
 * 代理 app 接口.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2019.05.26
 *
 * @version 1.0
 *
 * @see \Leevel\Kernel\IApp 请保持接口设计的一致
 */
interface IApp
{
    /**
     * 程序版本.
     *
     * @return string
     */
    public static function version(): string;

    /**
     * 是否以扩展方式运行.
     *
     * @return bool
     */
    public static function runWithExtension(): bool;

    /**
     * 是否为 Console.
     *
     * @return bool
     */
    public static function console(): bool;

    /**
     * 设置应用路径.
     *
     * @param string $path
     */
    public static function setPath(string $path): void;

    /**
     * 基础路径.
     *
     * @param string $path
     *
     * @return string
     */
    public static function path(string $path = ''): string;

    /**
     * 设置应用路径.
     *
     * @param string $path
     */
    public static function setAppPath(string $path): void;

    /**
     * 应用路径.
     *
     * @param bool|string $app
     * @param string      $path
     *
     * @return string
     */
    public static function appPath($app = false, string $path = ''): string;

    /**
     * 取得应用主题目录.
     *
     * @param bool|string $app
     *
     * @return string
     */
    public static function themePath($app = false): string;

    /**
     * 设置公共路径.
     *
     * @param string $path
     */
    public static function setCommonPath(string $path): void;

    /**
     * 公共路径.
     *
     * @param string $path
     *
     * @return string
     */
    public static function commonPath(string $path = ''): string;

    /**
     * 设置运行时路径.
     *
     * @param string $path
     */
    public static function setRuntimePath(string $path): void;

    /**
     * 运行路径.
     *
     * @param string $path
     *
     * @return string
     */
    public static function runtimePath(string $path = ''): string;

    /**
     * 设置存储路径.
     *
     * @param string $path
     */
    public static function setStoragePath(string $path): void;

    /**
     * 附件路径.
     *
     * @param string $path
     *
     * @return string
     */
    public static function storagePath(string $path = ''): string;

    /**
     * 设置资源路径.
     *
     * @param string $path
     */
    public static function setPublicPath(string $path): void;

    /**
     * 资源路径.
     *
     * @param string $path
     *
     * @return string
     */
    public static function publicPath(string $path = ''): string;

    /**
     * 设置主题路径.
     *
     * @param string $path
     */
    public static function setThemesPath(string $path): void;

    /**
     * 主题路径.
     *
     * @param string $path
     *
     * @return string
     */
    public static function themesPath(string $path = ''): string;

    /**
     * 设置配置路径.
     *
     * @param string $path
     */
    public static function setOptionPath(string $path): void;

    /**
     * 配置路径.
     *
     * @param string $path
     *
     * @return string
     */
    public static function optionPath(string $path = ''): string;

    /**
     * 设置语言包路径.
     *
     * @param string $path
     */
    public static function setI18nPath(string $path): void;

    /**
     * 语言包路径.
     *
     * @param null|string $path
     *
     * @return string
     */
    public static function i18nPath(?string $path = null): string;

    /**
     * 设置环境变量路径.
     *
     * @param string $path
     */
    public static function setEnvPath(string $path): void;

    /**
     * 环境变量路径.
     *
     * @return string
     */
    public static function envPath(): string;

    /**
     * 设置环境变量文件.
     *
     * @param string $file
     */
    public static function setEnvFile(string $file): void;

    /**
     * 取得环境变量文件.
     *
     * @return string
     */
    public static function envFile(): string;

    /**
     * 取得环境变量完整路径.
     *
     * @return string
     */
    public static function fullEnvPath(): string;

    /**
     * 设置语言包缓存路径.
     *
     * @param string $i18nCachedPath
     */
    public static function setI18nCachedPath(string $i18nCachedPath): void;

    /**
     * 返回语言包缓存路径.
     *
     * @param string $i18n
     *
     * @return string
     */
    public static function i18nCachedPath(string $i18n): string;

    /**
     * 是否存在语言包缓存.
     *
     * @param string $i18n
     *
     * @return bool
     */
    public static function isCachedI18n(string $i18n): bool;

    /**
     * 设置配置缓存路径.
     *
     * @param string $optionCachedPath
     */
    public static function setOptionCachedPath(string $optionCachedPath): void;

    /**
     * 返回配置缓存路径.
     *
     * @return string
     */
    public static function optionCachedPath(): string;

    /**
     * 是否存在配置缓存.
     *
     * @return bool
     */
    public static function isCachedOption(): bool;

    /**
     * 设置路由缓存路径.
     *
     * @param string $routerCachedPath
     */
    public static function setRouterCachedPath(string $routerCachedPath): void;

    /**
     * 返回路由缓存路径.
     *
     * @return string
     */
    public static function routerCachedPath(): string;

    /**
     * 是否存在路由缓存.
     *
     * @return bool
     */
    public static function isCachedRouter(): bool;

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
    public static function namespacePath(string $specificClass, bool $throwException = true): string;

    /**
     * 是否开启 debug.
     *
     * @return bool
     */
    public static function debug(): bool;

    /**
     * 是否为开发环境.
     *
     * @return bool
     */
    public static function development(): bool;

    /**
     * 运行环境.
     *
     * @return string
     */
    public static function environment(): string;

    /**
     * 取得应用的环境变量.支持 boolean, empty 和 null.
     *
     * @param mixed      $name
     * @param null|mixed $defaults
     *
     * @return mixed
     */
    public static function env(string $name, $defaults = null);

    /**
     * 初始化应用.
     *
     * @param array $bootstraps
     */
    public static function bootstrap(array $bootstraps): void;

    /**
     * 注册应用服务提供者.
     */
    public static function registerAppProviders(): void;

    /**
     * 返回 IOC 容器.
     *
     * @return \Leevel\Di\IContainer
     */
    public static function container(): IContainer;
}
