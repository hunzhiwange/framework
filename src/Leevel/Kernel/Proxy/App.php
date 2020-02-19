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

namespace Leevel\Kernel\Proxy;

use Leevel\Di\Container;
use Leevel\Di\IContainer as IBaseContainer;
use Leevel\Di\ICoroutine;
use Leevel\Di\Provider;
use Leevel\Kernel\App as BaseApp;

/**
 * 代理 app.
 *
 * @codeCoverageIgnore
 */
class App
{
    /**
     * call.
     *
     * @return mixed
     */
    public static function __callStatic(string $method, array $args)
    {
        return self::proxy()->{$method}(...$args);
    }

    /**
     * 获取程序版本.
     */
    public static function version(): string
    {
        return self::proxy()->version();
    }

    /**
     * 是否以扩展方式运行.
     */
    public static function runWithExtension(): bool
    {
        return self::proxy()->runWithExtension();
    }

    /**
     * 是否为 PHP 运行模式命令行.
     */
    public static function isConsole(): bool
    {
        return self::proxy()->isConsole();
    }

    /**
     * 设置应用路径.
     */
    public static function setPath(string $path): void
    {
        self::proxy()->setPath($path);
    }

    /**
     * 获取基础路径.
     */
    public static function path(string $path = ''): string
    {
        return self::proxy()->path($path);
    }

    /**
     * 设置应用路径.
     */
    public static function setAppPath(string $path): void
    {
        self::proxy()->setAppPath($path);
    }

    /**
     * 获取应用路径.
     *
     * @param bool|string $app
     */
    public static function appPath($app = false, string $path = ''): string
    {
        return self::proxy()->appPath($app, $path);
    }

    /**
     * 取得应用主题目录.
     *
     * @param bool|string $app
     */
    public static function themePath($app = false): string
    {
        return self::proxy()->themePath($app);
    }

    /**
     * 设置公共路径.
     */
    public static function setCommonPath(string $path): void
    {
        self::proxy()->setCommonPath($path);
    }

    /**
     * 获取公共路径.
     */
    public static function commonPath(string $path = ''): string
    {
        return self::proxy()->commonPath($path);
    }

    /**
     * 设置运行时路径.
     */
    public static function setRuntimePath(string $path): void
    {
        self::proxy()->setRuntimePath($path);
    }

    /**
     * 获取运行路径.
     */
    public static function runtimePath(string $path = ''): string
    {
        return self::proxy()->runtimePath($path);
    }

    /**
     * 设置附件存储路径.
     */
    public static function setStoragePath(string $path): void
    {
        self::proxy()->setStoragePath($path);
    }

    /**
     * 获取附件存储路径.
     */
    public static function storagePath(string $path = ''): string
    {
        return self::proxy()->storagePath($path);
    }

    /**
     * 设置资源路径.
     */
    public static function setPublicPath(string $path): void
    {
        self::proxy()->setPublicPath($path);
    }

    /**
     * 获取资源路径.
     */
    public static function publicPath(string $path = ''): string
    {
        return self::proxy()->publicPath($path);
    }

    /**
     * 设置主题路径.
     */
    public static function setThemesPath(string $path): void
    {
        self::proxy()->setThemesPath($path);
    }

    /**
     * 获取主题路径.
     */
    public static function themesPath(string $path = ''): string
    {
        return self::proxy()->themesPath($path);
    }

    /**
     * 设置配置路径.
     */
    public static function setOptionPath(string $path): void
    {
        self::proxy()->setOptionPath($path);
    }

    /**
     * 获取配置路径.
     */
    public static function optionPath(string $path = ''): string
    {
        return self::proxy()->optionPath($path);
    }

    /**
     * 设置语言包路径.
     */
    public static function setI18nPath(string $path): void
    {
        self::proxy()->setI18nPath($path);
    }

    /**
     * 获取语言包路径.
     */
    public static function i18nPath(?string $path = null): string
    {
        return self::proxy()->i18nPath($path);
    }

    /**
     * 设置环境变量路径.
     */
    public static function setEnvPath(string $path): void
    {
        self::proxy()->setEnvPath($path);
    }

    /**
     * 获取环境变量路径.
     */
    public static function envPath(): string
    {
        return self::proxy()->envPath();
    }

    /**
     * 设置环境变量文件.
     */
    public static function setEnvFile(string $file): void
    {
        self::proxy()->setEnvFile($file);
    }

    /**
     * 获取环境变量文件.
     */
    public static function envFile(): string
    {
        return self::proxy()->envFile();
    }

    /**
     * 获取环境变量完整路径.
     */
    public static function fullEnvPath(): string
    {
        return self::proxy()->fullEnvPath();
    }

    /**
     * 设置语言包缓存路径.
     */
    public static function setI18nCachedPath(string $i18nCachedPath): void
    {
        self::proxy()->setI18nCachedPath($i18nCachedPath);
    }

    /**
     * 获取语言包缓存路径.
     */
    public static function i18nCachedPath(string $i18n): string
    {
        return self::proxy()->i18nCachedPath($i18n);
    }

    /**
     * 是否存在语言包缓存.
     */
    public static function isCachedI18n(string $i18n): bool
    {
        return self::proxy()->isCachedI18n($i18n);
    }

    /**
     * 设置配置缓存路径.
     */
    public static function setOptionCachedPath(string $optionCachedPath): void
    {
        self::proxy()->setOptionCachedPath($optionCachedPath);
    }

    /**
     * 获取配置缓存路径.
     */
    public static function optionCachedPath(): string
    {
        return self::proxy()->optionCachedPath();
    }

    /**
     * 是否存在配置缓存.
     */
    public static function isCachedOption(): bool
    {
        return self::proxy()->isCachedOption();
    }

    /**
     * 设置路由缓存路径.
     */
    public static function setRouterCachedPath(string $routerCachedPath): void
    {
        self::proxy()->setRouterCachedPath($routerCachedPath);
    }

    /**
     * 获取路由缓存路径.
     */
    public static function routerCachedPath(): string
    {
        return self::proxy()->routerCachedPath();
    }

    /**
     * 是否存在路由缓存.
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
     */
    public static function namespacePath(string $specificClass, bool $throwException = true): string
    {
        return self::proxy()->namespacePath($specificClass, $throwException);
    }

    /**
     * 是否开启调试.
     */
    public static function isDebug(): bool
    {
        return self::proxy()->isDebug();
    }

    /**
     * 是否为开发环境.
     */
    public static function isDevelopment(): bool
    {
        return self::proxy()->isDevelopment();
    }

    /**
     * 获取运行环境.
     */
    public static function environment(): string
    {
        return self::proxy()->environment();
    }

    /**
     * 获取应用的环境变量.
     *
     * - 环境变量支持 boolean, empty 和 null.
     *
     * @param mixed      $name
     * @param null|mixed $defaults
     *
     * @return mixed
     */
    public static function env(string $name, $defaults = null)
    {
        return self::proxy()->env($name, $defaults);
    }

    /**
     * 初始化应用.
     */
    public static function bootstrap(array $bootstraps): void
    {
        self::proxy()->bootstrap($bootstraps);
    }

    /**
     * 注册应用服务提供者.
     */
    public static function registerAppProviders(): void
    {
        self::proxy()->registerAppProviders();
    }

    /**
     * 返回 IOC 容器.
     */
    public static function container(): IBaseContainer
    {
        return self::proxy()->container();
    }

    /**
     * 注册到容器.
     *
     * @param mixed      $name
     * @param null|mixed $service
     */
    public static function bind($name, $service = null, bool $share = false, bool $coroutine = false): IBaseContainer
    {
        return self::proxyContainer()->bind($name, $service, $share, $coroutine);
    }

    /**
     * 注册为实例.
     *
     * @param mixed $name
     * @param mixed $service
     */
    public static function instance($name, $service, bool $coroutine = false): IBaseContainer
    {
        return self::proxyContainer()->instance($name, $service, $coroutine);
    }

    /**
     * 注册单一实例.
     *
     * @param array|scalar $name
     * @param null|mixed   $service
     */
    public static function singleton($name, $service = null, bool $coroutine = false): IBaseContainer
    {
        return self::proxyContainer()->singleton($name, $service, $coroutine);
    }

    /**
     * 设置别名.
     *
     * @param array|string      $alias
     * @param null|array|string $value
     */
    public static function alias($alias, $value = null): IBaseContainer
    {
        return self::proxyContainer()->alias($alias, $value);
    }

    /**
     * 服务容器返回对象
     *
     * @return mixed
     */
    public static function make(string $name, array $args = [])
    {
        return self::proxyContainer()->make($name, $args);
    }

    /**
     * 实例回调自动注入.
     *
     * @param array|callable|string $callback
     *
     * @throws \InvalidArgumentException
     *
     * @return mixed
     */
    public static function call($callback, array $args = [])
    {
        return self::proxyContainer()->call($callback, $args);
    }

    /**
     * 删除服务和实例.
     */
    public static function remove(string $name): void
    {
        self::proxyContainer()->remove($name);
    }

    /**
     * 服务或者实例是否存在.
     */
    public static function exists(string $name): bool
    {
        return self::proxyContainer()->exists($name);
    }

    /**
     * 清理容器.
     */
    public static function clear(): void
    {
        self::proxyContainer()->clear();
    }

    /**
     * 执行 bootstrap.
     */
    public static function callProviderBootstrap(Provider $provider): void
    {
        self::proxyContainer()->callProviderBootstrap($provider);
    }

    /**
     * 创建服务提供者.
     */
    public static function makeProvider(string $provider): Provider
    {
        return self::proxyContainer()->makeProvider($provider);
    }

    /**
     * 注册服务提供者.
     *
     * @param \Leevel\Di\Provider|string $provider
     */
    public static function register($provider): Provider
    {
        return self::proxyContainer()->register($provider);
    }

    /**
     * 是否已经初始化引导.
     */
    public static function isBootstrap(): bool
    {
        return self::proxyContainer()->isBootstrap();
    }

    /**
     * 注册服务提供者.
     */
    public static function registerProviders(array $providers, array $deferredProviders = [], array $deferredAlias = []): void
    {
        self::proxyContainer()->registerProviders($providers, $deferredProviders, $deferredAlias);
    }

    /**
     * 设置协程.
     */
    public static function setCoroutine(ICoroutine $coroutine): void
    {
        self::proxyContainer()->setCoroutine($coroutine);
    }

    /**
     * 返回协程.
     *
     * @return \Leevel\Di\ICoroutine
     */
    public static function getCoroutine(): ?ICoroutine
    {
        return self::proxyContainer()->getCoroutine();
    }

    /**
     * 协程服务或者实例是否存在.
     */
    public static function existsCoroutine(string $name): bool
    {
        return self::proxyContainer()->existsCoroutine($name);
    }

    /**
     * 删除协程上下文服务和实例.
     */
    public static function removeCoroutine(?string $name = null): void
    {
        self::proxyContainer()->removeCoroutine($name);
    }

    /**
     * 设置服务到协程上下文.
     */
    public static function serviceCoroutine(string $service): void
    {
        self::proxyContainer()->serviceCoroutine($service);
    }

    /**
     * 代理 Container 服务.
     */
    public static function proxyContainer(): Container
    {
        return Container::singletons();
    }

    /**
     * 代理服务.
     */
    public static function proxy(): BaseApp
    {
        return Container::singletons()->make('app');
    }
}
