<?php

declare(strict_types=1);

namespace Tests\Database\Ddd\Entity;

use Leevel\Database\Ddd\Entity;
use Leevel\Database\Ddd\Struct;

class EntityWithoutPrimaryKeyNullInArray extends Entity
{
    public const string TABLE = 'test';

    public const array ID = [null];

    public const null AUTO = null;

    #[Struct([
    ])]
    protected ?string $name = null;
}
