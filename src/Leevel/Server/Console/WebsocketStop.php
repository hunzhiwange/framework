<?php

declare(strict_types=1);

namespace Leevel\Server\Console;

use Leevel\Server\Console\Base\Stop as BaseStop;

/**
 * Websocket 服务停止.
 */
class WebsocketStop extends BaseStop
{
    /**
     * 命令名字.
     */
    protected string $name = 'server:websocket:stop';

    /**
     * 命令行描述.
     */
    protected string $description = 'Stop websocket service';

    protected string $connect = 'websocket';
}
