<?php

declare(strict_types=1);

namespace Leevel\Server\Console;

use Leevel\Server\Console\Base\Server as BaseServer;

/**
 * Websocket 服务启动.
 */
class Websocket extends BaseServer
{
    /**
     * 命令名字.
     */
    protected string $name = 'server:websocket';

    /**
     * 命令行描述.
     */
    protected string $description = 'Start websocket server';

    protected string $connect = 'websocket';
}
