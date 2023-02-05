<?php

declare(strict_types=1);

namespace Tests\Support\Fixtures;

use Leevel\Support\Enum;

enum RealEnumInt: int
{
    use Enum;

    #[msg('未完成')]
    case FALSE = 1;

    #[msg('已完成')]
    case TRUE = 2;
}
