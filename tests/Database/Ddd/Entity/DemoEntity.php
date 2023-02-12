<?php

declare(strict_types=1);

namespace Tests\Database\Ddd\Entity;

use Leevel\Database\Ddd\Entity;

class DemoEntity extends Entity
{
    public const TABLE = 'test';

    public const ID = 'id';

    public const AUTO = 'id';

    public const STRUCT = [
        'id' => [
            self::READONLY => true,
        ],
        'name' => [],
    ];
}
