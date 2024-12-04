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
    public ?int $id1 = null
    {
        set (int|null $v) {
            $this->id1 = $this->set('id1', $v);
        }
    }

    #[Struct([
    ])]
    public ?int $id2 = null
    {
        set (int|null $v) {
            $this->id2 = $this->set('id2', $v);
        }
    }

    #[Struct([
    ])]
    public ?string $name = null
    {
        set (string|null $v) {
            $this->name = $this->set('name', $v);
        }
    }
}
