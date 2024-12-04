<?php

declare(strict_types=1);

namespace Tests\Database\Ddd\Entity;

use Leevel\Database\Ddd\Entity;
use Leevel\Database\Ddd\Struct;

class SoftDeleteNotFoundDeleteAtField extends Entity
{
    public const string TABLE = 'demo';

    public const string ID = 'id';

    public const string AUTO = 'id';

    public const string DELETE_AT = 'delete_at';

    #[Struct([
        self::READONLY => true,
    ])]
    public ?int $id = null
    {
        set (int|null $v) {
            $this->id = $this->set('id', $v);
        }
    }
}
