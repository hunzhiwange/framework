<?php

declare(strict_types=1);

namespace Tests\Database\Ddd\Entity;

use Leevel\Database\Ddd\Entity;
use Leevel\Database\Ddd\Struct;

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

    #[Struct([
    ])]
    protected ?int $id1 = null;

    #[Struct([
    ])]
    protected ?int $id2 = null;

    #[Struct([
    ])]
    protected ?string $name = null;
}
