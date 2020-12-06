<?php

declare(strict_types=1);

namespace Tests\Database\Ddd\Entity;

use Leevel\Database\Ddd\Entity;
use Leevel\Database\Ddd\GetterSetter;

class DemoToArrayBlackEntity extends Entity
{
    use GetterSetter;

    const TABLE = 'test';

    const ID = 'id';

    const AUTO = 'id';

    const STRUCT = [
        'id'          => [],
        'name'        => [],
        'description' => [
            self::SHOW_PROP_BLACK => true,
        ],
        'address'     => [],
        'foo_bar'     => [
            self::SHOW_PROP_BLACK => true,
        ],
        'hello'       => [],
    ];
}
