<?php

declare(strict_types=1);

namespace Tests\Support\Fixtures;

use Leevel\Support\Enum;
use Leevel\Support\Msg;

class StatusEnum
{
    use Enum;

    #[msg]
    public const string ENABLE = 1;

    #[msg]
    public const string DISABLE = 0;

    #[Msg('第三种可能')]
    public const string THREE = 3;
}
