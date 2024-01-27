<?php

declare(strict_types=1);

namespace Leevel\Server;

use Leevel\Di\IContainer;
use Swoole\Coroutine\Http\Server as SwooleHttpServer;
use Swoole\Coroutine\Server as SwooleServer;

/**
 * 服务端接口.
 */
interface IServer
{
    public function __construct(IContainer $container, array $config = []);

    public function start(): void;

    public function getServer(): SwooleServer|SwooleHttpServer;

    public function getConfig(): array;
}
