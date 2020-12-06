<?php

declare(strict_types=1);

namespace Tests\Database\Ddd\Entity;

use Leevel\Database\Ddd\Entity;
use Leevel\Database\Ddd\GetterSetter;

class WithoutPrimarykeyAndAllAreKey extends Entity
{
    use GetterSetter;

    const TABLE = 'without_primarykey';

    const ID = null;

    const AUTO = null;

    const STRUCT = [
        'goods_id'    => [],
        'description' => [],
        'name'        => [],
    ];
}
