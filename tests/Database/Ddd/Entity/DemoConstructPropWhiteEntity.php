<?php

declare(strict_types=1);

namespace Tests\Database\Ddd\Entity;

use Leevel\Database\Ddd\Entity;
use Leevel\Database\Ddd\Struct;

class DemoConstructPropWhiteEntity extends Entity
{
    public const TABLE = 'test';

    public const ID = 'id';

    public const AUTO = 'id';

    #[Struct([
        self::READONLY => true,
        self::CONSTRUCT_PROP_WHITE => true,
    ])]
    protected ?int $id = null;

    #[Struct([
    ])]
    protected ?string $name = null;
}
