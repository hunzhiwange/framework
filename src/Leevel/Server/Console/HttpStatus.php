<?php

declare(strict_types=1);

namespace Leevel\Server\Console;

use Leevel\Server\Console\Base\Status as BaseStatus;

/**
 * HTTP 服务状态.
 */
class HttpStatus extends BaseStatus
{
    /**
     * 命令名字.
     */
    protected string $name = 'server:http:status';

    /**
     * 命令行描述.
     */
    protected string $description = 'Status of http service';

    protected string $connect = 'http';
}
