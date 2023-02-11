<?php

declare(strict_types=1);

namespace Tests\Database\Ddd\Entity;

use Leevel\Support\Enum;
use Leevel\Support\Msg;

enum StatusEnum: int
{
    use Enum;

    #[Msg('禁用')]
    case DISABLE = 0;

    #[Msg('启用')]
    case ENABLE = 1;
}
