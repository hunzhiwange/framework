<?php

declare(strict_types=1);

namespace Tests\Database\Ddd\Entity;

use Leevel\Database\Ddd\Entity;
use Leevel\Database\Ddd\Struct;

class DemoReadonlyUpdateEntity extends Entity
{
    public const TABLE = 'test';

    public const ID = 'id';

    public const AUTO = 'id';

    public const STRUCT = [
        'id' => [],
        'name' => [
            self::READONLY => true,
        ],
        'description' => [],
    ];

    #[Struct([
    ])]
    protected ?int $id = null;

    #[Struct([
        self::READONLY => true,
    ])]
    protected ?string $name = null;

    #[Struct([
    ])]
    protected ?string $description = null;
}
