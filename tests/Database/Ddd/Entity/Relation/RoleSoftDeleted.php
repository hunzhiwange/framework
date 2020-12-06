<?php

declare(strict_types=1);

namespace Tests\Database\Ddd\Entity\Relation;

use Leevel\Database\Ddd\Entity;
use Leevel\Database\Ddd\GetterSetter;

class RoleSoftDeleted extends Entity
{
    use GetterSetter;

    const TABLE = 'role_soft_deleted';

    const ID = 'id';

    const AUTO = 'id';

    const STRUCT = [
        'id'        => [],
        'name'      => [],
        'create_at' => [],
        'delete_at' => [
            self::CREATE_FILL => 0,
        ],
    ];

    const DELETE_AT = 'delete_at';
}
