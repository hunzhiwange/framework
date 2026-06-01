<?php

declare(strict_types=1);

namespace Tests\Database\Ddd\Entity;

use Leevel\Database\Ddd\Entity;
use Leevel\Database\Ddd\Struct;

class EntityWithEnum extends Entity
{
    public const string TABLE = 'entity_with_enum';

    public const string ID = 'id';

    public const string AUTO = 'id';

    #[Struct([
        self::READONLY => true,
    ])]
    public ?int $id = null {
        set(int|string|null $v) {
            $this->id = $this->set('id', $v);
        }
    }

    #[Struct([])]
    public ?string $title = null {
        set(string|null $v) {
            $this->title = $this->set('title', $v);
        }
    }

    #[Struct([
        self::ENUM_CLASS => StatusEnum::class,
    ])]
    public ?int $status = null {
        set(int|string|null $v) {
            $this->status = $this->set('status', $v);
        }
    }
}
