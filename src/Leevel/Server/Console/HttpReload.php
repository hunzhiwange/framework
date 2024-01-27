<?php

declare(strict_types=1);

namespace Leevel\Server\Console;

use Leevel\Server\Console\Base\Reload as BaseReload;

/**
 * HTTP 服务重启.
 */
class HttpReload extends BaseReload
{
    /**
     * 命令名字.
     */
    protected string $name = 'server:http:reload';

    /**
     * 命令行描述.
     */
    protected string $description = 'Reload http service';

    protected string $connect = 'http';
}
