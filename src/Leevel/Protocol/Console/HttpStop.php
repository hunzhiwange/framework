<?php

declare(strict_types=1);

namespace Leevel\Protocol\Console;

use Leevel\Di\Container;
use Leevel\Protocol\Console\Base\Stop as BaseStop;
use Leevel\Protocol\IServer;

/**
 * HTTP 服务停止.
 */
class HttpStop extends BaseStop
{
    /**
     * 命令名字.
    */
    protected string $name = 'http:stop';

    /**
     * 命令行描述.
    */
    protected string $description = 'Stop http service';

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
        return PHP_EOL.'                      HTTP STOP'.PHP_EOL;
    }
}
