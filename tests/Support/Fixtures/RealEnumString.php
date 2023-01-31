<?php

declare(strict_types=1);

namespace Tests\Support\Fixtures;

use Leevel\Support\Enum;

enum RealEnumString:string
{
    use Enum;

    #[msg('世界')]
    case HELLO = 'hello';

    #[msg('你好')]
    case WORLD = 'world';
}
