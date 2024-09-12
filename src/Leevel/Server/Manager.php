<?php

declare(strict_types=1);

namespace Leevel\Server;

use Leevel\Event\IDispatch;
use Leevel\Support\Manager as Managers;
use Swoole\Coroutine\Http\Server as SwooleHttpServer;
use Swoole\Coroutine\Server as SwooleServer;
use Swoole\Table;

/**
 * 服务管理器.
 */
class Manager extends Managers implements IServer
{
    public function start(): void
    {
        // @phpstan-ignore-next-line
        $this->callConnectMethod(__FUNCTION__);
    }

    public function getServer(): SwooleHttpServer|SwooleServer
    {
        // @phpstan-ignore-next-line
        return $this->callConnectMethod(__FUNCTION__);
    }

    public function getConfig(): array
    {
        // @phpstan-ignore-next-line
        return $this->callConnectMethod(__FUNCTION__);
    }

    public function getProcessStatisticTable(): Table
    {
        // @phpstan-ignore-next-line
        return $this->callConnectMethod(__FUNCTION__);
    }

    public function connect(?string $connect = null, bool $newConnect = false, ...$arguments): IServer
    {
        return parent::connect($connect, $newConnect, ...$arguments);
    }

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
