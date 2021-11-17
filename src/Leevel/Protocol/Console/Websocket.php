<?php

declare(strict_types=1);

namespace Leevel\Protocol\Console;

use Leevel\Di\Container;
use Leevel\Protocol\Console\Base\Server as BaseServer;
use Leevel\Protocol\IServer;

/**
 * Swoole WebSocket 服务启动.
 */
class Websocket extends BaseServer
{
    /**
     * 命令名字.
     */
    protected string $name = 'websocket:server';

    /**
     * 命令行描述.
     */
    protected string $description = 'Start websocket server';

    /**
     * 命令帮助.
     */
    protected string $help = <<<'EOF'
        The <info>%command.name%</info> command to start websocket server:
        
          <info>php %command.full_name%</info>
        
        You can also by using the <comment>--daemonize</comment> option:
        
          <info>php %command.full_name% --daemonize</info>
        
          <info>php %command.full_name% -d</info>
        EOF;

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
        return PHP_EOL.'                  WEBSOCKET SERVER'.PHP_EOL;
    }
}
