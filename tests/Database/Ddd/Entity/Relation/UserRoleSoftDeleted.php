<?php

declare(strict_types=1);

namespace Tests\Database\Ddd\Entity\Relation;

use Leevel\Database\Ddd\Entity;
use Leevel\Database\Ddd\Struct;

class UserRoleSoftDeleted extends Entity
{
    public const string TABLE = 'user_role_soft_deleted';

    public const string ID = 'id';

    public const string AUTO = 'id';

    public const string DELETE_AT = 'delete_at';

    #[Struct([
    ])]
    public ?int $id = null
    {
        set (int|null $v) {
            $this->id = $this->set('id', $v);
        }
    }

    #[Struct([
    ])]
    public ?int $userId = null
    {
        set (int|null $v) {
            $this->userId = $this->set('userId', $v);
        }
    }

    #[Struct([
    ])]
    public ?int $roleId = null
    {
        set (int|null $v) {
            $this->roleId = $this->set('roleId', $v);
        }
    }

    #[Struct([
    ])]
    public ?string $createAt = null
    {
        set (string|null $v) {
            $this->createAt = $this->set('createAt', $v);
        }
    }

    #[Struct([
        self::CREATE_FILL => 0,
    ])]
    public ?int $deleteAt = null
    {
        set (int|null $v) {
            $this->deleteAt = $this->set('deleteAt', $v);
        }
    }
}
