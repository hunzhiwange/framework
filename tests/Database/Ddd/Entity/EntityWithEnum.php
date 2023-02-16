<?php

declare(strict_types=1);

namespace Tests\Database\Ddd\Entity;

use Leevel\Database\Ddd\Entity;
use Leevel\Database\Ddd\Struct;

class EntityWithEnum extends Entity
{
    public const TABLE = 'entity_with_enum';

    public const ID = 'id';

    public const AUTO = 'id';

    public const STRUCT = [
        'id' => [
            self::READONLY => true,
        ],
        'title' => [],
        'status' => [
            self::ENUM_CLASS => StatusEnum::class,
        ],
    ];

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
