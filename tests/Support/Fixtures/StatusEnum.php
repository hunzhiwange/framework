<?php

declare(strict_types=1);

namespace Tests\Support\Fixtures;

use Leevel\Support\Enum;

class StatusEnum extends Enum
{
    #[msg]
    public const ENABLE = 1;

    #[msg]
    public const DISABLE = 0;

    #[msg('第三种可能')]
    public const THREE = 3;
}
