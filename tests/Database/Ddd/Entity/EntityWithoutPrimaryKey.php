<?php

declare(strict_types=1);

namespace Tests\Database\Ddd\Entity;

use Leevel\Database\Ddd\Entity;
use Leevel\Database\Ddd\GetterSetter;

class EntityWithoutPrimaryKey extends Entity
{
    use GetterSetter;

    const TABLE = 'test';

    const ID = null;

    const AUTO = null;

    const STRUCT = [
        'name' => [],
    ];
}
