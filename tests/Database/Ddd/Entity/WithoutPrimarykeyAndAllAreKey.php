<?php

declare(strict_types=1);

namespace Tests\Database\Ddd\Entity;

use Leevel\Database\Ddd\Entity;
use Leevel\Database\Ddd\Struct;

class WithoutPrimarykeyAndAllAreKey extends Entity
{
    public const string TABLE = 'without_primarykey';

    public const null ID = null;

    public const null AUTO = null;

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
