<?php

declare(strict_types=1);

namespace Leevel\Level\Console;

use Leevel\Di\Container;
use Leevel\Level\Console\Base\Server as BaseServer;
use Leevel\Level\IServer;

/**
 * HTTP 服务启动.
 */
class Http extends BaseServer
{
    /**
     * 命令名字.
     */
    protected string $name = 'http:server';

    /**
     * 命令行描述.
     */
    protected string $description = 'Start http server';

    /**
     * 命令帮助.
     */
    protected string $help = <<<'EOF'
        The <info>%command.name%</info> command to start http server:
        
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
        return Container::singletons()->make('http.server');
    }

    /**
     * {@inheritDoc}
     */
    protected function getVersion(): string
    {
        return PHP_EOL.'                     HTTP SERVER'.PHP_EOL;
    }
}
