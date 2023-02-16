<?php

declare(strict_types=1);

namespace Tests\Database\Ddd\Entity;

use Leevel\Database\Ddd\Entity;
use Leevel\Database\Ddd\Struct;

class DemoVersion extends Entity
{
    public const TABLE = 'test_version';

    public const ID = 'id';

    public const AUTO = 'id';

    public const STRUCT = [
        'id' => [
            self::READONLY => true,
        ],
        'name' => [],
        'available_number' => [],
        'real_number' => [],
        'version' => [],
    ];

    public const VERSION = 'version';

    #[Struct([
        self::READONLY => true,
    ])]
    protected ?int $id = null;

    #[Struct([
    ])]
    protected ?string $name = null;

    #[Struct([
    ])]
    protected ?string $availableNumber = null;

    #[Struct([
    ])]
    protected ?string $realNumber = null;

    #[Struct([
    ])]
    protected ?int $version = null;

    protected bool $version_ = true;
}
