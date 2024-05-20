<?php

declare(strict_types=1);

namespace Tests\Support\Fixtures;

use Leevel\Support\Enum;
use Leevel\Support\Msg;

class StatusEnum
{
    use Enum;

    #[Msg]
    public const int ENABLE = 1;

    #[Msg]
    public const int DISABLE = 0;

    #[Msg('第三种可能')]
    public const int THREE = 3;
}
