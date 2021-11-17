<?php

declare(strict_types=1);

namespace Leevel\Protocol\Console;

use Leevel\Di\Container;
use Leevel\Protocol\Console\Base\Reload as BaseReload;
use Leevel\Protocol\IServer;

/**
 * HTTP 服务重启.
 */
class HttpReload extends BaseReload
{
    /**
     * 命令名字.
     */
    protected string $name = 'http:reload';

    /**
     * 命令行描述.
     */
    protected string $description = 'Reload http service';

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
        return PHP_EOL.'                     HTTP RELOAD'.PHP_EOL;
    }
}
