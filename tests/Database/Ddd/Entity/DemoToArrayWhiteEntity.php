<?php

declare(strict_types=1);

namespace Tests\Database\Ddd\Entity;

use Leevel\Database\Ddd\Entity;

class DemoToArrayWhiteEntity extends Entity
{
    public const TABLE = 'test';

    public const ID = 'id';

    public const AUTO = 'id';

    public const STRUCT = [
        'id' => [],
        'name' => [],
        'description' => [
            self::SHOW_PROP_WHITE => true,
        ],
        'address' => [],
        'foo_bar' => [
            self::SHOW_PROP_WHITE => true,
        ],
        'hello' => [],
    ];
}
