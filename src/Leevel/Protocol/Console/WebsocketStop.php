<?php

declare(strict_types=1);

namespace Leevel\Protocol\Console;

use Leevel\Di\Container;
use Leevel\Protocol\Console\Base\Stop as BaseStop;
use Leevel\Protocol\IServer;

/**
 * Swoole WebSocket 服务停止.
 */
class WebsocketStop extends BaseStop
{
    /**
     * 命令名字.
     */
    protected string $name = 'websocket:stop';

    /**
     * 命令行描述.
     */
    protected string $description = 'Stop websocket service';

    /**
     * {@inheritDoc}
     */
    protected function createServer(): IServer
    {
        return Container::singletons()->make('websocket.server');
    }

    /**
     * {@inheritDoc}
     */
    protected function getVersion(): string
    {
        return PHP_EOL.'                   WEBSOCKET STOP'.PHP_EOL;
    }
}
