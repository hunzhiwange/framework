<?php

declare(strict_types=1);

namespace Tests\Support\Fixtures;

use Leevel\Support\Enum;
use Leevel\Support\Msg;

class Enum2
{
    use Enum;

    #[Msg('错误类型%s:%s', '我是', '谁')]
    public const ERROR_ONE = 100010;
}
