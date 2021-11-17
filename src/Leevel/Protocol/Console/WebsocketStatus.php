<?php

declare(strict_types=1);

namespace Leevel\Protocol\Console;

use Leevel\Di\Container;
use Leevel\Protocol\Console\Base\Status as BaseStatus;
use Leevel\Protocol\IServer;

/**
 * Swoole WebSocket 服务列表.
 */
class WebsocketStatus extends BaseStatus
{
    /**
     * 命令名字.
     */
    protected string $name = 'websocket:status';

    /**
     * 命令行描述.
     */
    protected string $description = 'Status of websocket service';

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
        return PHP_EOL.'                  WEBSOCKET STATUS'.PHP_EOL;
    }
}
