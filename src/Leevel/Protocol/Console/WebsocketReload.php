<?php

declare(strict_types=1);

namespace Leevel\Protocol\Console;

use Leevel\Di\Container;
use Leevel\Protocol\Console\Base\Reload as BaseReload;
use Leevel\Protocol\IServer;

/**
 * Swoole WebSocket 服务重启.
 */
class WebsocketReload extends BaseReload
{
    /**
     * 命令名字.
    */
    protected string $name = 'websocket:reload';

    /**
     * 命令行描述.
    */
    protected string $description = 'Reload websocket service';

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
        return PHP_EOL.'                  WEBSOCKET RELOAD'.PHP_EOL;
    }
}
