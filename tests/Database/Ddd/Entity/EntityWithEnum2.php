<?php

declare(strict_types=1);

namespace Tests\Database\Ddd\Entity;

use Leevel\Database\Ddd\Entity;
use Leevel\Database\Ddd\GetterSetter;

class EntityWithEnum2 extends Entity
{
    use GetterSetter;

    public const TABLE = 'entity_with_enum';

    public const ID = 'id';

    public const AUTO = 'id';

    public const STRUCT = [
        'id' => [
            self::READONLY           => true,
        ],
        'title'       => [],
        'status'      => [],
    ];

    #[status('禁用')]
    public const STATUS_DISABLE = 'f';

    #[status('启用')]
    public const STATUS_ENABLE = 't';
}
