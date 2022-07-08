<?php

declare(strict_types=1);

namespace Leevel\Level\Console;

use Leevel\Di\Container;
use Leevel\Level\Console\Base\Status as BaseStatus;
use Leevel\Level\IServer;

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
