<?php

declare(strict_types=1);

namespace Tests\Database\Ddd\Entity;

use Leevel\Database\Ddd\Entity;
use Leevel\Database\Ddd\Struct;

class DemoUpdatePropWhiteEntity extends Entity
{
    public const TABLE = 'test';

    public const ID = 'id';

    public const AUTO = 'id';

    public const STRUCT = [
        'id' => [
            self::UPDATE_PROP_WHITE => true,
            self::READONLY => true,
        ],
        'name' => [
            self::UPDATE_PROP_WHITE => true,
        ],
        'description' => [],
    ];

    #[Struct([
        self::UPDATE_PROP_WHITE => true,
        self::READONLY => true,
    ])]
    protected ?int $id = null;

    #[Struct([
        self::UPDATE_PROP_WHITE => true,
    ])]
    protected ?string $name = null;

    #[Struct([
    ])]
    protected ?string $description = null;
}
