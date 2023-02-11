<?php

declare(strict_types=1);

namespace Tests\Support\Fixtures;

use Leevel\Support\Enum;
use Leevel\Support\Msg;

enum RealEnumNoValue
{
    use Enum;

    #[Msg('启用')]
    case ENABLE;

    #[Msg('禁用')]
    case DISABLE;
}
