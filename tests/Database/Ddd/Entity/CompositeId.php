<?php

declare(strict_types=1);

namespace Tests\Database\Ddd\Entity;

use Leevel\Database\Ddd\Entity;
use Leevel\Database\Ddd\GetterSetter;

class CompositeId extends Entity
{
    use GetterSetter;

    const TABLE = 'composite_id';

    const ID = ['id1', 'id2'];

    const AUTO = null;

    const STRUCT = [
        'id1'      => [],
        'id2'      => [],
        'name'     => [],
    ];
}
