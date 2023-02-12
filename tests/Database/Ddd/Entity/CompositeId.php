<?php

declare(strict_types=1);

namespace Tests\Database\Ddd\Entity;

use Leevel\Database\Ddd\Entity;

class CompositeId extends Entity
{
    public const TABLE = 'composite_id';

    public const ID = ['id1', 'id2'];

    public const AUTO = null;

    public const STRUCT = [
        'id1' => [],
        'id2' => [],
        'name' => [],
    ];
}
