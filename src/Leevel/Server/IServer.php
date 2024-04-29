<?php

declare(strict_types=1);

namespace Leevel\Server;

use Swoole\Coroutine\Http\Server as SwooleHttpServer;
use Swoole\Coroutine\Server as SwooleServer;
use Swoole\Table;

/**
 * 服务端接口.
 */
interface IServer
{
    /**
     * 启动服务.
     */
    public function start(): void;

    /**
     * 获取服务.
     */
    public function getServer(): SwooleHttpServer|SwooleServer;

    /**
     * 获取服务配置.
     */
    public function getConfig(): array;

    /**
     * 获取进程统计表.
     */
    public function getProcessStatisticTable(): Table;
}
