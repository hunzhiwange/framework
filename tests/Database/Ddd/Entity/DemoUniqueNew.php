<?php

declare(strict_types=1);

namespace Tests\Database\Ddd\Entity;

use Leevel\Database\Ddd\Entity;
use Leevel\Database\Ddd\Struct;

class DemoUniqueNew extends Entity
{
    public const string TABLE = 'test_unique';

    public const string ID = 'id';

    public const string AUTO = 'id';

    /**
     * Unique key.
     */
    public const array UNIQUE = [
        ['identity'],
    ];

    #[Struct([
        self::READONLY => true,
    ])]
    public ?int $id = null
    {
        set (int|string|null $v) {
            $this->id = $this->set('id', $v);
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

    #[Struct([
    ])]
    public ?string $createAt = null
    {
        set (string|null $v) {
            $this->createAt = $this->set('createAt', $v);
        }
    }

    #[Struct([
    ])]
    public ?string $identity = null
    {
        set (string|null $v) {
            $this->identity = $this->set('identity', $v);
        }
    }
}
