<?php

declare(strict_types=1);

namespace Leevel\Server;

use Leevel\Event\IDispatch;
use Leevel\Support\Manager as Managers;

/**
 * 服务管理器.
 *
 * @method static void                                                   start()                                        启动服务.
 * @method static \Swoole\Coroutine\Server|\Swoole\Coroutine\Http\Server getServer()                                    获取服务.
 * @method static array                                                  getConfig()                                    获取服务配置.
 * @method static \Swoole\Table;                                         getProcessStatisticTable()                     获取进程统计表.
 * @method static \Leevel\Di\IContainer                                  container()                                    返回 IOC 容器.
 * @method static void                                                   disconnect(?string $connect = null)            删除连接.
 * @method static array                                                  getConnects()                                  取回所有连接.
 * @method static string                                                 getDefaultConnect()                            返回默认连接.
 * @method static void                                                   setDefaultConnect(string $name)                设置默认连接.
 * @method static mixed                                                  getContainerConfig(?string $name = null)       获取容器配置值.
 * @method static void                                                   setContainerConfig(string $name, mixed $value) 设置容器配置值.
 * @method static void                                                   extend(string $connect, \Closure $callback)    扩展自定义连接.
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

        // @phpstan-ignore-next-line
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

        // @phpstan-ignore-next-line
        return new $driverClass($this->container, $this->normalizeConnectConfig($connect), $dispatch);
    }
}
