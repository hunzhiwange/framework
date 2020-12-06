<?php

declare(strict_types=1);

namespace Tests\Database\Ddd\Entity;

use Leevel\Database\Ddd\Entity;
use Leevel\Database\Ddd\GetterSetter;

class DemoUnique extends Entity
{
    use GetterSetter;

    const TABLE = 'test_unique';

    const ID = 'id';

    const AUTO = 'id';

    const STRUCT = [
        'id' => [
            self::READONLY             => true,
        ],
        'name'       => [],
        'create_at'  => [],
        'identity'   => [],
    ];
}
