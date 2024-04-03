<?php

declare(strict_types=1);

namespace Leevel\Server;

use Leevel\Support\Enum;
use Leevel\Support\Msg;

/**
 * 工作进程运行状态枚举.
 */
enum WorkerStatusEnum: int
{
    use Enum;

    #[Msg('正常')]
    case OK = 1;

    #[Msg('停止中')]
    case STOPPING = 2;

    #[Msg('已停止')]
    case STOPPED = 3;
}
