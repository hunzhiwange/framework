<?php

declare(strict_types=1);

namespace Tests\Database\Ddd\Entity;

use Leevel\Database\Ddd\Entity;
use Leevel\Database\Ddd\Struct;

class CompositeId extends Entity
{
    public const string TABLE = 'composite_id';

    public const array ID = ['id1', 'id2'];

    public const ?string AUTO = null;

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
