<?php

declare(strict_types=1);

namespace Tests\Database\Ddd\Entity\Relation;

use Leevel\Database\Ddd\Entity;
use Leevel\Database\Ddd\Struct;

class UserRole extends Entity
{
    public const TABLE = 'user_role';

    public const ID = 'id';

    public const AUTO = 'id';

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
    protected ?string $createAt = null;

    #[Struct([
    ])]
    protected ?int $deleteAt = null;
}
