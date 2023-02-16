<?php

declare(strict_types=1);

namespace Tests\Database\Ddd\Entity\Relation;

use Leevel\Database\Ddd\Entity;
use Leevel\Database\Ddd\Struct;

class UserRoleSoftDeleted extends Entity
{
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

    #[Struct([
    ])]
    protected ?int $id = null;

    #[Struct([
    ])]
    protected ?int $userId = null;

    #[Struct([
    ])]
    protected ?int $roleId = null;

    #[Struct([
    ])]
    protected ?int $createAt = null;

    #[Struct([
        self::CREATE_FILL => 0,
    ])]
    protected ?int $deleteAt = null;
}
