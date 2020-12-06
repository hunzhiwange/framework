<?php

declare(strict_types=1);

namespace Tests\Database\Ddd\Entity;

use Leevel\Database\Ddd\Entity;
use Leevel\Database\Ddd\GetterSetter;

class DemoUpdatePropWhiteEntity extends Entity
{
    use GetterSetter;

    const TABLE = 'test';

    const ID = 'id';

    const AUTO = 'id';

    const STRUCT = [
        'id' => [
            self::UPDATE_PROP_WHITE => true,
            self::READONLY          => true,
        ],
        'name' => [
            self::UPDATE_PROP_WHITE => true,
        ],
        'description' => [],
    ];
}
