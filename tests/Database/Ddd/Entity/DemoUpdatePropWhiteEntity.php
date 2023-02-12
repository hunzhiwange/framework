<?php

declare(strict_types=1);

namespace Tests\Database\Ddd\Entity;

use Leevel\Database\Ddd\Entity;

class DemoUpdatePropWhiteEntity extends Entity
{
    public const TABLE = 'test';

    public const ID = 'id';

    public const AUTO = 'id';

    public const STRUCT = [
        'id' => [
            self::UPDATE_PROP_WHITE => true,
            self::READONLY => true,
        ],
        'name' => [
            self::UPDATE_PROP_WHITE => true,
        ],
        'description' => [],
    ];
}
