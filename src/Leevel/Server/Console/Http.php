<?php

declare(strict_types=1);

namespace Leevel\Server\Console;

use Leevel\Server\Console\Base\Server as BaseServer;

/**
 * HTTP 服务启动.
 */
class Http extends BaseServer
{
    /**
     * 命令名字.
     */
    protected string $name = 'server:http';

    /**
     * 命令行描述.
     */
    protected string $description = 'Start http server';

    protected string $connect = 'http';
}
