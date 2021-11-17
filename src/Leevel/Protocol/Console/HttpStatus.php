<?php

declare(strict_types=1);

namespace Leevel\Protocol\Console;

use Leevel\Di\Container;
use Leevel\Protocol\Console\Base\Status as BaseStatus;
use Leevel\Protocol\IServer;

/**
 * HTTP 服务列表.
 */
class HttpStatus extends BaseStatus
{
    /**
     * 命令名字.
     */
    protected string $name = 'http:status';

    /**
     * 命令行描述.
     */
    protected string $description = 'Status of http service';

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
        return PHP_EOL.'                     HTTP STATUS'.PHP_EOL;
    }
}
