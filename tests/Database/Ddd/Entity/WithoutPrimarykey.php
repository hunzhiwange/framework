<?php

declare(strict_types=1);

namespace Tests\Database\Ddd\Entity;

use Leevel\Database\Ddd\Entity;
use Leevel\Database\Ddd\Struct;

class WithoutPrimarykey extends Entity
{
    public const string TABLE = 'without_primarykey';

    public const string ID = 'goods_id';

    public const ?string AUTO = null;

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
