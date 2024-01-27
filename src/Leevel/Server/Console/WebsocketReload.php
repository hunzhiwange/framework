<?php

declare(strict_types=1);

namespace Leevel\Server\Console;

use Leevel\Server\Console\Base\Reload as BaseReload;

/**
 * Websocket 服务重启.
 */
class WebsocketReload extends BaseReload
{
    /**
     * 命令名字.
     */
    protected string $name = 'server:websocket:reload';

    /**
     * 命令行描述.
     */
    protected string $description = 'Reload websocket service';

    protected string $connect = 'websocket';
}
