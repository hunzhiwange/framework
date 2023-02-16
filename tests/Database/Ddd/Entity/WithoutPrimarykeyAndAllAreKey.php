<?php

declare(strict_types=1);

namespace Tests\Database\Ddd\Entity;

use Leevel\Database\Ddd\Entity;
use Leevel\Database\Ddd\Struct;

class WithoutPrimarykeyAndAllAreKey extends Entity
{
    public const TABLE = 'without_primarykey';

    public const ID = null;

    public const AUTO = null;

    public const STRUCT = [
        'goods_id' => [],
        'description' => [],
        'name' => [],
    ];

    #[Struct([
        self::READONLY => true,
    ])]
    protected ?int $goodsId = null;

    #[Struct([
    ])]
    protected ?string $description = null;

    #[Struct([
    ])]
    protected ?string $name = null;
}
