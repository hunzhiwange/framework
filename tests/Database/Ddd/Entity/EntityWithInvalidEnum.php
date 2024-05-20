<?php

declare(strict_types=1);

namespace Tests\Database\Ddd\Entity;

use Leevel\Database\Ddd\Entity;
use Leevel\Database\Ddd\GetterSetter;

class EntityWithInvalidEnum extends Entity
{
    use GetterSetter;

    public const string TABLE = 'entity_with_invalid_enum';

    public const string ID = 'id';

    public const string AUTO = 'id';

    public const array STRUCT = [
        'id' => [
            self::READONLY => true,
        ],
        'title' => [],
        'status' => [],
    ];

    public const array STATUS_ENUM = [
        'disable' => ['f'],
        'enable' => ['t'],
    ];
}
