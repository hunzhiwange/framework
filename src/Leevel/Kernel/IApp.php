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
 * (c) 2010-2020 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Leevel\Kernel;

use Leevel\Di\IContainer;

/**
 * 应用接口.
 */
interface IApp
{
    /**
     * Leevel 版本.
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
     * 程序版本.
     */
    public function version(): string;

    /**
     * 是否以扩展方式运行.
     */
    public function runWithExtension(): bool;

    /**
     * 是否为 Console.
     */
    public function console(): bool;

    /**
     * 设置基础路径.
     */
    public function setPath(string $path): void;

    /**
     * 基础路径.
     */
    public function path(string $path = ''): string;

    /**
     * 设置应用路径.
     */
    public function setAppPath(string $path): void;

    /**
     * 应用路径.
     *
     * @param bool|string $app
     */
    public function appPath($app = false, string $path = ''): string;

    /**
     * 取得应用主题目录.
     *
     * @param bool|string $app
     */
    public function themePath($app = false): string;

    /**
     * 设置公共路径.
     */
    public function setCommonPath(string $path): void;

    /**
     * 公共路径.
     */
    public function commonPath(string $path = ''): string;

    /**
     * 设置运行时路径.
     */
    public function setRuntimePath(string $path): void;

    /**
     * 运行路径.
     */
    public function runtimePath(string $path = ''): string;

    /**
     * 设置存储路径.
     */
    public function setStoragePath(string $path): void;

    /**
     * 附件路径.
     */
    public function storagePath(string $path = ''): string;

    /**
     * 设置资源路径.
     */
    public function setPublicPath(string $path): void;

    /**
     * 资源路径.
     */
    public function publicPath(string $path = ''): string;

    /**
     * 设置主题路径.
     */
    public function setThemesPath(string $path): void;

    /**
     * 主题路径.
     */
    public function themesPath(string $path = ''): string;

    /**
     * 设置配置路径.
     */
    public function setOptionPath(string $path): void;

    /**
     * 配置路径.
     */
    public function optionPath(string $path = ''): string;

    /**
     * 设置语言包路径.
     */
    public function setI18nPath(string $path): void;

    /**
     * 语言包路径.
     */
    public function i18nPath(?string $path = null): string;

    /**
     * 设置环境变量路径.
     */
    public function setEnvPath(string $path): void;

    /**
     * 环境变量路径.
     */
    public function envPath(): string;

    /**
     * 设置环境变量文件.
     */
    public function setEnvFile(string $file): void;

    /**
     * 取得环境变量文件.
     */
    public function envFile(): string;

    /**
     * 取得环境变量完整路径.
     */
    public function fullEnvPath(): string;

    /**
     * 设置语言包缓存路径.
     */
    public function setI18nCachedPath(string $i18nCachedPath): void;

    /**
     * 返回语言包缓存路径.
     */
    public function i18nCachedPath(string $i18n): string;

    /**
     * 是否存在语言包缓存.
     */
    public function isCachedI18n(string $i18n): bool;

    /**
     * 设置配置缓存路径.
     */
    public function setOptionCachedPath(string $optionCachedPath): void;

    /**
     * 返回配置缓存路径.
     */
    public function optionCachedPath(): string;

    /**
     * 是否存在配置缓存.
     */
    public function isCachedOption(): bool;

    /**
     * 设置路由缓存路径.
     */
    public function setRouterCachedPath(string $routerCachedPath): void;

    /**
     * 返回路由缓存路径.
     */
    public function routerCachedPath(): string;

    /**
     * 是否存在路由缓存.
     */
    public function isCachedRouter(): bool;

    /**
     * 获取命名空间目录真实路径.
     *
     * 一般用于获取文件 PSR4 所在的命名空间，当然如果存在命名空间。
     * 基于某个具体的类查询该类目录的真实路径。
     * 为简化开发和提升性能，必须提供具体的存在的类才能够获取目录的真实路径。
     */
    public function namespacePath(string $specificClass, bool $throwException = true): string;

    /**
     * 是否开启 debug.
     */
    public function debug(): bool;

    /**
     * 是否为开发环境.
     */
    public function development(): bool;

    /**
     * 运行环境.
     */
    public function environment(): string;

    /**
     * 取得应用的环境变量.
     *
     * - 环境变量支持 boolean, empty 和 null.
     *
     * @param mixed      $name
     * @param null|mixed $defaults
     *
     * @return mixed
     */
    public function env(string $name, $defaults = null);

    /**
     * 初始化应用.
     */
    public function bootstrap(array $bootstraps): void;

    /**
     * 注册应用服务提供者.
     */
    public function registerAppProviders(): void;

    /**
     * 返回 IOC 容器.
     */
    public function container(): IContainer;
}
