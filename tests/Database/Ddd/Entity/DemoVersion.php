<?php

declare(strict_types=1);

namespace Tests\Database\Ddd\Entity;

use Leevel\Database\Ddd\Entity;
use Leevel\Database\Ddd\Struct;

class DemoVersion extends Entity
{
    public const string TABLE = 'test_version';

    public const string ID = 'id';

    public const string AUTO = 'id';

    public const string VERSION = 'version';

    #[Struct([
        self::READONLY => true,
    ])]
    public ?int $id = null
    {
        set (int|null $v) {
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
    public ?string $availableNumber = null
    {
        set (string|null $v) {
            $this->availableNumber = $this->set('availableNumber', $v);
        }
    }

    #[Struct([
    ])]
    public ?string $realNumber = null
    {
        set (string|null $v) {
            $this->realNumber = $this->set('realNumber', $v);
        }
    }

    #[Struct([
    ])]
    public ?int $version = null
    {
        set (int|null $v) {
            $this->version = $this->set('version', $v);
        }
    }

    protected bool $enabledVersionFramework = true;
}
