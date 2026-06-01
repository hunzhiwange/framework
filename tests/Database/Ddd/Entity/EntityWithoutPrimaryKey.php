<?php

declare(strict_types=1);

namespace Tests\Database\Ddd\Entity;

use Leevel\Database\Ddd\Entity;
use Leevel\Database\Ddd\Struct;

class EntityWithoutPrimaryKey extends Entity
{
    public const string TABLE = 'test';

    public const null ID = null;

    public const null AUTO = null;

    #[Struct([])]
    public ?string $name = null {
        set(string|null $v) {
            $this->name = $this->set('name', $v);
        }
    }
}
