<?php

declare(strict_types=1);

namespace Tests\Database\Ddd\Entity;

use Leevel\Database\Ddd\Entity;
use Leevel\Database\Ddd\Struct;

class DemoCreatePropWhiteEntity extends Entity
{
    public const TABLE = 'test';

    public const ID = 'id';

    public const AUTO = 'id';

    public const STRUCT = [
        'id' => [
            self::READONLY => true,
        ],
        'name' => [
            self::CREATE_PROP_WHITE => true,
        ],
        'description' => [],
    ];

    #[Struct([
        self::READONLY => true,
    ])]
    protected ?int $id = null;

    #[Struct([
        self::CREATE_PROP_WHITE => true,
    ])]
    protected ?string $name = null;

    #[Struct([
    ])]
    protected ?string $description = null;
}
