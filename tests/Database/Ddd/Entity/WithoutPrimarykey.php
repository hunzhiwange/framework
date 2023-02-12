<?php

declare(strict_types=1);

namespace Tests\Database\Ddd\Entity;

use Leevel\Database\Ddd\Entity;

class WithoutPrimarykey extends Entity
{
    public const TABLE = 'without_primarykey';

    public const ID = 'goods_id';

    public const AUTO = null;

    public const STRUCT = [
        'goods_id' => [
            self::READONLY => true,
        ],
        'description' => [],
        'name' => [],
    ];
}
