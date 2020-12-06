<?php

declare(strict_types=1);

namespace Tests\Database\Ddd\Entity\Relation;

use Leevel\Database\Ddd\Entity;
use Leevel\Database\Ddd\GetterSetter;

class UserRoleSoftDeleted extends Entity
{
    use GetterSetter;

    const TABLE = 'user_role_soft_deleted';

    const ID = 'id';

    const AUTO = 'id';

    const STRUCT = [
        'id'        => [],
        'user_id'   => [],
        'role_id'   => [],
        'create_at' => [],
        'delete_at' => [
            self::CREATE_FILL => 0,
        ],
    ];

    const DELETE_AT = 'delete_at';
}
