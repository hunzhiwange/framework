<?php

declare(strict_types=1);

namespace Leevel\Level;

use Swoole\Server as SwooleServer;

/**
 * 协议接口.
 */
interface IServer
{
    /**
     * 设置为守护进程.
     */
    public function setDaemonize(bool $daemonize = true): void;

    /**
     * 添加自定义进程.
     *
     * @throws \InvalidArgumentException
     */
    public function process(string $process): void;

    /**
     * 创建服务.
     */
    public function createServer(): void;

    /**
     * Swoole 服务启动.
     */
    public function startServer(): void;

    /**
     * 返回 Swoole 服务.
     *
     * @throws \RuntimeException
     */
    public function getServer(): SwooleServer;
}
