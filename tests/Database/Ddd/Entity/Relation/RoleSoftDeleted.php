<?php

declare(strict_types=1);

namespace Tests\Database\Ddd\Entity\Relation;

use Leevel\Database\Ddd\Entity;
use Leevel\Database\Ddd\GetterSetter;

class RoleSoftDeleted extends Entity
{
    use GetterSetter;

    public const TABLE = 'role_soft_deleted';

    public const ID = 'id';

    public const AUTO = 'id';

    public const STRUCT = [
        'id'        => [],
        'name'      => [],
        'create_at' => [],
        'delete_at' => [
            self::CREATE_FILL => 0,
        ],
    ];

    public const DELETE_AT = 'delete_at';
}
