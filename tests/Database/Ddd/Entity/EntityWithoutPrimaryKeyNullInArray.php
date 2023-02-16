<?php

declare(strict_types=1);

namespace Tests\Database\Ddd\Entity;

use Leevel\Database\Ddd\Entity;
use Leevel\Database\Ddd\Struct;

class EntityWithoutPrimaryKeyNullInArray extends Entity
{
    public const TABLE = 'test';

    public const ID = [null];

    public const AUTO = null;

    public const STRUCT = [
        'name' => [],
    ];

    #[Struct([
    ])]
    protected ?string $name = null;
}
