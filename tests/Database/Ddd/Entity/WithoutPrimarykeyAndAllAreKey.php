<?php

declare(strict_types=1);

namespace Tests\Database\Ddd\Entity;

use Leevel\Database\Ddd\Entity;
use Leevel\Database\Ddd\GetterSetter;

class WithoutPrimarykeyAndAllAreKey extends Entity
{
    use GetterSetter;

    public const TABLE = 'without_primarykey';

    public const ID = null;

    public const AUTO = null;

    public const STRUCT = [
        'goods_id'    => [],
        'description' => [],
        'name'        => [],
    ];
}
