<?php

declare(strict_types=1);

namespace Tests\Database\Ddd\Entity\Relation;

use Leevel\Database\Ddd\Entity;

class UserRole extends Entity
{
    public const TABLE = 'user_role';

    public const ID = 'id';

    public const AUTO = 'id';

    public const STRUCT = [
        'id' => [],
        'user_id' => [],
        'role_id' => [],
        'create_at' => [],
    ];
}
