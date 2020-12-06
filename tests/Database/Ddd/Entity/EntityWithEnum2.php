<?php

declare(strict_types=1);

namespace Tests\Database\Ddd\Entity;

use Leevel\Database\Ddd\Entity;
use Leevel\Database\Ddd\GetterSetter;

class EntityWithEnum2 extends Entity
{
    use GetterSetter;

    const TABLE = 'entity_with_enum';

    const ID = 'id';

    const AUTO = 'id';

    const STRUCT = [
        'id' => [
            self::READONLY           => true,
        ],
        'title'       => [],
        'status'      => [],
    ];

    const STATUS_ENUM = [
        'disable' => ['f', '禁用'],
        'enable'  => ['t', '启用'],
    ];
}
