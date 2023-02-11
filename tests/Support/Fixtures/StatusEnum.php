<?php

declare(strict_types=1);

namespace Tests\Support\Fixtures;

use Leevel\Support\Enum;
use Leevel\Support\Msg;

class StatusEnum
{
    use Enum;

    #[msg]
    public const ENABLE = 1;

    #[msg]
    public const DISABLE = 0;

    #[Msg('第三种可能')]
    public const THREE = 3;
}
