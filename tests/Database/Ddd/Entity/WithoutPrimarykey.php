<?php

declare(strict_types=1);

namespace Tests\Database\Ddd\Entity;

use Leevel\Database\Ddd\Entity;
use Leevel\Database\Ddd\GetterSetter;

class WithoutPrimarykey extends Entity
{
    use GetterSetter;

    const TABLE = 'without_primarykey';

    const ID = 'goods_id';

    const AUTO = null;

    const STRUCT = [
        'goods_id' => [
            self::READONLY => true,
        ],
        'description' => [],
        'name'        => [],
    ];
}
