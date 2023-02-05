<?php

declare(strict_types=1);

namespace Tests\Database\Ddd\Entity\Relation;

use Leevel\Database\Ddd\Entity;
use Leevel\Database\Ddd\GetterSetter;

class UserRoleSoftDeleted extends Entity
{
    use GetterSetter;

    public const TABLE = 'user_role_soft_deleted';

    public const ID = 'id';

    public const AUTO = 'id';

    public const STRUCT = [
        'id' => [],
        'user_id' => [],
        'role_id' => [],
        'create_at' => [],
        'delete_at' => [
            self::CREATE_FILL => 0,
        ],
    ];

    public const DELETE_AT = 'delete_at';
}
