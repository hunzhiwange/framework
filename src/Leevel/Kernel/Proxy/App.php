<?php

declare(strict_types=1);

namespace Leevel\Kernel\Proxy;

use Error;
use Leevel\Di\Container;
use Leevel\Kernel\App as BaseApp;

/**
 * 代理 app.
 *
 * @method static string version()                                                                                    获取程序版本.
 * @method static bool isConsole()                                                                                    是否为 PHP 运行模式命令行.
 * @method static void setPath(string $path)                                                                          设置应用路径.
 * @method static string path(string $path = '')                                                                      获取基础路径.
 * @method static void setAppPath(string $path)                                                                       设置应用路径.
 * @method static string appPath(string $path = '')                                                     获取应用路径.
 * @method static void setStoragePath(string $path)                                                                   设置存储路径.
 * @method static string storagePath(string $path = '')                                                               获取存储路径.
 * @method static void setThemesPath(string $path)                                                                    设置主题路径.
 * @method static string themesPath(string $path = '')                                                                获取主题路径.
 * @method static void setOptionPath(string $path)                                                                    设置配置路径.
 * @method static string optionPath(string $path = '')                                                                获取配置路径.
 * @method static void setI18nPath(string $path)                                                                      设置语言包路径.
 * @method static string i18nPath(?string $path = null)                                                               获取语言包路径.
 * @method static void setEnvPath(string $path)                                                                       设置环境变量路径.
 * @method static string envPath()                                                                                    获取环境变量路径.
 * @method static void setEnvFile(string $file)                                                                       设置环境变量文件.
 * @method static string envFile()                                                                                    获取环境变量文件.
 * @method static string fullEnvPath()                                                                                获取环境变量完整路径.
 * @method static void setI18nCachedPath(string $i18nCachedPath)                                                      设置语言包缓存路径.
 * @method static string i18nCachedPath(string $i18n)                                                                 获取语言包缓存路径.
 * @method static bool isCachedI18n(string $i18n)                                                                     是否存在语言包缓存.
 * @method static void setOptionCachedPath(string $optionCachedPath)                                                  设置配置缓存路径.
 * @method static string optionCachedPath()                                                                           获取配置缓存路径.
 * @method static bool isCachedOption()                                                                               是否存在配置缓存.
 * @method static void setRouterCachedPath(string $routerCachedPath)                                                  设置路由缓存路径.
 * @method static string routerCachedPath()                                                                           获取路由缓存路径.
 * @method static bool isCachedRouter()                                                                               是否存在路由缓存.
 * @method static string namespacePath(string $specificClass)                                                         获取命名空间目录真实路径.
 * @method static bool isDebug()                                                                                      是否开启调试.
 * @method static bool isDevelopment()                                                                                是否为开发环境.
 * @method static string environment()                                                                                获取运行环境.
 * @method static mixed env(string $name, $defaults = null)                                                           获取应用的环境变量.
 * @method static void bootstrap(array $bootstraps)                                                                   初始化应用.
 * @method static void registerAppProviders()                                                                         注册应用服务提供者.
 * @method static \Leevel\Di\IContainer container()                                                                   返回 IOC 容器.
 * @method static \Leevel\Di\IContainer bind($name, $service = null, bool $share = false, bool $coroutine = false)    注册到容器.
 * @method static \Leevel\Di\IContainer instance($name, $service, int $cid = \Leevel\Di\IContainer::NOT_COROUTINE_ID) 注册为实例.
 * @method static \Leevel\Di\IContainer singleton($name, $service = null, bool $coroutine = false)                    注册单一实例.
 * @method static \Leevel\Di\IContainer alias($alias, $value = null)                                                  设置别名.
 * @method static mixed make(string $name, array $args = [], int $cid = \Leevel\Di\IContainer::DEFAULT_COROUTINE_ID)  创建容器服务并返回.
 * @method static mixed call($callback, array $args = [])                                                             实例回调自动注入.
 * @method static void remove(string $name, int $cid = \Leevel\Di\IContainer::DEFAULT_COROUTINE_ID)                   删除服务和实例.
 * @method static bool exists(string $name)                                                                           服务或者实例是否存在.
 * @method static void clear()                                                                                        清理容器.
 * @method static void callProviderBootstrap(\Leevel\Di\Provider $provider)                                           执行 bootstrap.
 * @method static \Leevel\Di\Provider makeProvider(string $provider)                                                  创建服务提供者.
 * @method static \Leevel\Di\Provider register($provider)                                                             注册服务提供者.
 * @method static bool isBootstrap()                                                                                  是否已经初始化引导.
 * @method static void registerProviders(array $providers, array $deferredProviders = [], array $deferredAlias = [])  注册服务提供者.
 * @method static void setCoroutine(\Leevel\Di\ICoroutine $coroutine)                                                 设置协程.
 * @method static ?\Leevel\Di\ICoroutine getCoroutine()                                                               返回协程.
 * @method static bool existsCoroutine(string $name, int $cid = \Leevel\Di\IContainer::DEFAULT_COROUTINE_ID)          协程服务或者实例是否存在.
 * @method static void removeCoroutine(?string $name = null, int $cid = \Leevel\Di\IContainer::DEFAULT_COROUTINE_ID)  删除协程上下文服务和实例.
 * @method static void serviceCoroutine(string $service)                                                              设置服务到协程上下文.
 */
class App
{
    /**
     * 实现魔术方法 __callStatic.
     */
    public static function __callStatic(string $method, array $args): mixed
    {
        try {
            return self::proxy()->{$method}(...$args);
        } catch (Error $e) {
            if (false !== strpos($e->getMessage(), sprintf('Call to undefined method %s::%s()', BaseApp::class, $method))) {
                return self::proxyContainer()->{$method}(...$args);
            }

            throw $e;
        }
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
