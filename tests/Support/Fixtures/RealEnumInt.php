<?php

declare(strict_types=1);

namespace Tests\Support\Fixtures;

use Leevel\Support\Enum;
use Leevel\Support\Msg;

enum RealEnumInt: int
{
    use Enum;

    #[Msg('未完成')]
    case FALSE = 1;

    #[Msg('已完成')]
    case TRUE = 2;
}
