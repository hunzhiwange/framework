<?php

declare(strict_types=1);

namespace Leevel\View\Proxy;

use Leevel\Di\Container;
use Leevel\View\Manager;

/**
 * 代理 view.
 *
 * @method static string                display(string $file, array $vars = [], ?string $ext = null) 加载视图文件.
 * @method static void                  setVar(array|string $name, mixed $value = null)              设置模板变量.
 * @method static mixed                 getVar(?string $name = null)                                 获取变量值.
 * @method static void                  deleteVar(array $name)                                       删除变量值.
 * @method static void                  clearVar()                                                   清空变量值.
 * @method static \Leevel\Di\IContainer container()                                                  返回 IOC 容器.
 * @method static \Leevel\View\IView    connect(?string $connect = null, bool $newConnect = false)   连接并返回连接对象.
 * @method static \Leevel\View\IView    reconnect(?string $connect = null)                           重新连接.
 * @method static void                  disconnect(?string $connect = null)                          删除连接.
 * @method static array                 getConnects()                                                取回所有连接.
 * @method static string                getDefaultConnect()                                          返回默认连接.
 * @method static void                  setDefaultConnect(string $name)                              设置默认连接.
 * @method static mixed                 getContainerConfig(?string $name = null)                     获取容器配置值.
 * @method static void                  setContainerConfig(string $name, mixed $value)               设置容器配置值.
 * @method static void                  extend(string $connect, \Closure $callback)                  扩展自定义连接.
 * @method static array                 normalizeConnectConfig(string $connect)                      整理连接配置.
 */
class View
{
    /**
     * 实现魔术方法 __callStatic.
     */
    public static function __callStatic(string $method, array $args): mixed
    {
        return self::proxy()->{$method}(...$args);
    }

    /**
     * 代理服务.
     */
    public static function proxy(): Manager
    {
        // @phpstan-ignore-next-line
        return Container::singletons()->make('views');
    }
}
