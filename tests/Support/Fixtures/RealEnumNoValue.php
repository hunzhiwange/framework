<?php

declare(strict_types=1);

namespace Tests\Support\Fixtures;

use Leevel\Support\Enum;

enum RealEnumNoValue
{
    use Enum;

    #[msg('启用')]
    case ENABLE;

    #[msg('禁用')]
    case DISABLE;
}
