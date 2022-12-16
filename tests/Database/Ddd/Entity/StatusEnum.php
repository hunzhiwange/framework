<?php

declare(strict_types=1);

namespace Tests\Database\Ddd\Entity;

use Leevel\Support\Enum;

enum Status2Enum:int
{
    use Enum;

    #[msg('禁用')]
    case DISABLE = 0;

    #[msg('启用')]
    case ENABLE = 1;
}
