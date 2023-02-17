<?php

declare(strict_types=1);

namespace Tests\Database\Ddd\Entity;

use Leevel\Database\Ddd\Entity;
use Leevel\Database\Ddd\Struct;

class EntityWithoutPrimaryKey extends Entity
{
    public const TABLE = 'test';

    public const ID = null;

    public const AUTO = null;

    #[Struct([
    ])]
    protected ?string $name = null;
}
