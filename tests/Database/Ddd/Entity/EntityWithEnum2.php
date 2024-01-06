<?php

declare(strict_types=1);

namespace Tests\Database\Ddd\Entity;

use Leevel\Database\Ddd\Entity;
use Leevel\Database\Ddd\Struct;

class EntityWithEnum2 extends Entity
{
    public const TABLE = 'entity_with_enum';

    public const ID = 'id';

    public const AUTO = 'id';

    #[Struct([
        self::READONLY => true,
    ])]
    protected ?int $id = null;

    #[Struct([
    ])]
    protected ?string $title = null;

    #[Struct([
        self::ENUM_CLASS => StatusEnum::class,
    ])]
    protected ?string $status = null;
}
