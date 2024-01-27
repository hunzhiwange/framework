<?php

declare(strict_types=1);

namespace Leevel\Server\Console;

use Leevel\Server\Console\Base\Status as BaseStatus;

/**
 * Websocket 服务状态.
 */
class WebsocketStatus extends BaseStatus
{
    /**
     * 命令名字.
     */
    protected string $name = 'server:websocket:status';

    /**
     * 命令行描述.
     */
    protected string $description = 'Status of websocket service';

    protected string $connect = 'websocket';
}
