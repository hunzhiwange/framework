<?php

declare(strict_types=1);

namespace Leevel\Server;

use Leevel\Event\IDispatch;
use Leevel\Support\Manager as Managers;

/**
 * 服务管理器.
 *
 * @method static string                display(string $file, array $vars = [], ?string $ext = null) 加载视图文件.
 * @method static void                  setParseResolver(\Closure $parseResolver)                    设置 parser 解析回调.
 * @method static string                getCachePath(string $file)                                   获取编译路径.
 * @method static void                  setVar($name, $value = null)                                 设置模板变量.
 * @method        static                getVar(?string $name = null)                                 获取变量值.
 * @method static void                  deleteVar(array $name)                                       删除变量值.
 * @method static void                  clearVar()                                                   清空变量值.
 * @method static \Leevel\Di\IContainer container()                                                  返回 IOC 容器.
 * @method static void                  disconnect(?string $connect = null)                          删除连接.
 * @method static array                 getConnects()                                                取回所有连接.
 * @method static string                getDefaultConnect()                                          返回默认连接.
 * @method static void                  setDefaultConnect(string $name)                              设置默认连接.
 * @method static mixed                 getContainerConfig(?string $name = null)                     获取容器配置值.
 * @method static void                  setContainerConfig(string $name, mixed $value)               设置容器配置值.
 * @method static void                  extend(string $connect, \Closure $callback)                  扩展自定义连接.
 */
class Manager extends Managers
{
    /**
     * {@inheritDoc}
     */
    public function connect(?string $connect = null, bool $newConnect = false, ...$arguments): IServer
    {
        return parent::connect($connect, $newConnect, ...$arguments);
    }

    /**
     * {@inheritDoc}
     */
    public function reconnect(?string $connect = null, ...$arguments): IServer
    {
        return parent::reconnect($connect, ...$arguments);
    }

    /**
     * 取得配置命名空间.
     */
    protected function getConfigNamespace(): string
    {
        return 'server';
    }

    /**
     * 创建 http 服务驱动.
     */
    protected function makeConnectHttp(string $connect, ?string $driverClass = null): Http
    {
        $driverClass = $this->getDriverClass(Http::class, $driverClass);

        /** @var IDispatch $dispatch */
        $dispatch = $this->container->make(IDispatch::class);

        // @var Http $http
        return new $driverClass($this->container, $this->normalizeConnectConfig($connect), $dispatch);
    }

    /**
     * 创建 Websocket 服务驱动.
     */
    protected function makeConnectWebsocket(string $connect, ?string $driverClass = null): Http
    {
        $driverClass = $this->getDriverClass(Websocket::class, $driverClass);

        /** @var IDispatch $dispatch */
        $dispatch = $this->container->make(IDispatch::class);

        // @var Websocket $http
        return new $driverClass($this->container, $this->normalizeConnectConfig($connect), $dispatch);
    }
}
