<?php

declare(strict_types=1);

namespace Tests\Database\Ddd\Entity;

use Leevel\Database\Ddd\Entity;
use Leevel\Database\Ddd\GetterSetter;

class DemoToArrayBlackEntity extends Entity
{
    use GetterSetter;

    public const TABLE = 'test';

    public const ID = 'id';

    public const AUTO = 'id';

    public const STRUCT = [
        'id' => [],
        'name' => [],
        'description' => [
            self::SHOW_PROP_BLACK => true,
        ],
        'address' => [],
        'foo_bar' => [
            self::SHOW_PROP_BLACK => true,
        ],
        'hello' => [],
    ];
}
