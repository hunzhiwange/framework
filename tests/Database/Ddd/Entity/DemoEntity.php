<?php

declare(strict_types=1);

namespace Tests\Database\Ddd\Entity;

use Leevel\Database\Ddd\Entity;
use Leevel\Database\Ddd\Struct;

class DemoEntity extends Entity
{
    public const string TABLE = 'test';

    public const string ID = 'id';

    public const string AUTO = 'id';

    #[Struct([
        self::READONLY => true,
    ])]
    public ?int $id
    {
        set (int|null $v) {
            $this->id = $this->set('id', $v);
        }
    }

    #[Struct([
    ])]
    public ?string $name
    {
        set (string|null $v) {
            $this->name = $this->set('name', $v);
        }
    }
}
