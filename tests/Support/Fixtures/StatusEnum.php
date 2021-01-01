<?php

declare(strict_types=1);

namespace Tests\Support\Fixtures;

use Leevel\Support\Enum;

class StatusEnum extends Enum
{
    const ENABLE = 1;
    
    const DISABLE = 0;

    #[msg('第三种可能')]
    const THREE = 3;
}

