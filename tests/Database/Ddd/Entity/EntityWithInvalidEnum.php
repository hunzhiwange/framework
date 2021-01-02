<?php

declare(strict_types=1);

namespace Tests\Database\Ddd\Entity;

use Leevel\Database\Ddd\Entity;
use Leevel\Database\Ddd\GetterSetter;

class EntityWithInvalidEnum extends Entity
{
    use GetterSetter;

    public const TABLE = 'entity_with_invalid_enum';

    public const ID = 'id';

    public const AUTO = 'id';

    public const STRUCT = [
        'id' => [
            self::READONLY           => true,
        ],
        'title'       => [],
        'status'      => [],
    ];

    public const STATUS_ENUM = [
        'disable' => ['f'],
        'enable'  => ['t'],
    ];
}
