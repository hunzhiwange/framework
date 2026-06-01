<?php

declare(strict_types=1);

namespace Tests\Database\Ddd\Entity\Relation;

use Leevel\Database\Ddd\Entity;
use Leevel\Database\Ddd\Struct;

class UserRole extends Entity
{
    public const string TABLE = 'user_role';

    public const string ID = 'id';

    public const string AUTO = 'id';

    #[Struct([])]
    public ?int $id = null {
        set(int|string|null $v) {
            $this->id = $this->set('id', $v);
        }
    }

    #[Struct([])]
    public ?int $userId = null {
        set(int|string|null $v) {
            $this->userId = $this->set('userId', $v);
        }
    }

    #[Struct([])]
    public ?int $roleId = null {
        set(int|string|null $v) {
            $this->roleId = $this->set('roleId', $v);
        }
    }

    #[Struct([])]
    public ?string $createAt = null {
        set(string|null $v) {
            $this->createAt = $this->set('createAt', $v);
        }
    }

    #[Struct([])]
    public ?int $deleteAt = null {
        set(int|string|null $v) {
            $this->deleteAt = $this->set('deleteAt', $v);
        }
    }
}
