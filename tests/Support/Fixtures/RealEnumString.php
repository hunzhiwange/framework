<?php

declare(strict_types=1);

namespace Tests\Support\Fixtures;

use Leevel\Support\Enum;
use Leevel\Support\Msg;

enum RealEnumString: string
{
    use Enum;

    #[Msg('世界')]
    case HELLO = 'hello';

    #[Msg('你好')]
    case WORLD = 'world';
}
