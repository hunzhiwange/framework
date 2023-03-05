<?php

declare(strict_types=1);

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
    public const VERSION = '1.1.14';

    /**
     * 默认环境变量名字.
     */
    public const DEFAULT_ENV = '.env';

    /**
     * 获取程序版本.
     */
    public function version(): string;

    /**
     * 是否为 PHP 运行模式命令行.
     */
    public function isConsole(): bool;

    /**
     * 设置基础路径.
     */
    public function setPath(string $path): void;

    /**
     * 获取基础路径.
     */
    public function path(string $path = ''): string;

    /**
     * 设置应用路径.
     */
    public function setAppPath(string $path): void;

    /**
     * 获取应用路径.
     */
    public function appPath(string $path = ''): string;

    /**
     * 设置存储路径.
     */
    public function setStoragePath(string $path): void;

    /**
     * 获取存储路径.
     */
    public function storagePath(string $path = ''): string;

    /**
     * 设置主题路径.
     */
    public function setThemesPath(string $path): void;

    /**
     * 获取主题路径.
     */
    public function themesPath(string $path = ''): string;

    /**
     * 设置配置路径.
     */
    public function setOptionPath(string $path): void;

    /**
     * 获取配置路径.
     */
    public function optionPath(string $path = ''): string;

    /**
     * 设置语言包路径.
     */
    public function setI18nPath(string $path): void;

    /**
     * 获取语言包路径.
     */
    public function i18nPath(?string $path = null): string;

    /**
     * 设置环境变量路径.
     */
    public function setEnvPath(string $path): void;

    /**
     * 获取环境变量路径.
     */
    public function envPath(): string;

    /**
     * 设置环境变量文件.
     */
    public function setEnvFile(string $file): void;

    /**
     * 获取环境变量文件.
     */
    public function envFile(): string;

    /**
     * 获取环境变量完整路径.
     */
    public function fullEnvPath(): string;

    /**
     * 设置语言包缓存路径.
     */
    public function setI18nCachedPath(string $i18nCachedPath): void;

    /**
     * 获取语言包缓存路径.
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
     * 获取配置缓存路径.
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
     * 获取路由缓存路径.
     */
    public function routerCachedPath(): string;

    /**
     * 是否存在路由缓存.
     */
    public function isCachedRouter(): bool;

    /**
     * 获取命名空间目录真实路径.
     */
    public function namespacePath(string $namespace): string;

    /**
     * 是否开启调试.
     */
    public function isDebug(): bool;

    /**
     * 是否为开发环境.
     */
    public function isDevelopment(): bool;

    /**
     * 获取运行环境.
     */
    public function environment(): string;

    /**
     * 取得应用的环境变量.
     *
     * - 环境变量支持 boolean, empty 和 null 值.
     */
    public function env(string $name, mixed $defaults = null): mixed;

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
