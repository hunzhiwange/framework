<?php

declare(strict_types=1);

namespace Tests\Database\Ddd\Entity\Relation;

use Leevel\Database\Ddd\Entity;
use Leevel\Database\Ddd\EntityCollection as Collection;
use Leevel\Database\Ddd\Relation\ManyMany;
use Leevel\Database\Ddd\Struct;

class User extends Entity
{
    public const string TABLE = 'user';

    public const string ID = 'id';

    public const string AUTO = 'id';

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
    public ?string $name = null
    {
        set (string|null $v) {
            $this->name = $this->set('name', $v);
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
        self::MANY_MANY => Role::class,
        self::MIDDLE_ENTITY => UserRole::class,
        self::SOURCE_KEY => 'id',
        self::TARGET_KEY => 'id',
        self::MIDDLE_SOURCE_KEY => 'user_id',
        self::MIDDLE_TARGET_KEY => 'role_id',
    ])]
    public ?Collection $role = null
    {
        set (Collection|null $v) {
            $this->role = $this->set('role', $v);
        }
    }

    #[Struct([
        self::MANY_MANY => RoleSoftDeleted::class,
        self::MIDDLE_ENTITY => UserRoleSoftDeleted::class,
        self::SOURCE_KEY => 'id',
        self::TARGET_KEY => 'id',
        self::MIDDLE_SOURCE_KEY => 'user_id',
        self::MIDDLE_TARGET_KEY => 'role_id',
    ])]
    public ?Collection $roleSoftDeleted = null
    {
        set (Collection|null $v) {
            $this->roleSoftDeleted = $this->set('roleSoftDeleted', $v);
        }
    }

    #[Struct([
        self::MANY_MANY => RoleSoftDeleted::class,
        self::MIDDLE_ENTITY => UserRoleSoftDeleted::class,
        self::SOURCE_KEY => 'id',
        self::TARGET_KEY => 'id',
        self::MIDDLE_SOURCE_KEY => 'user_id',
        self::MIDDLE_TARGET_KEY => 'role_id',
        self::RELATION_SCOPE => 'withSoftDeleted',
    ])]
    public ?Collection $roleMiddleWithSoftDeleted = null
    {
        set (Collection|null $v) {
            $this->roleMiddleWithSoftDeleted = $this->set('roleMiddleWithSoftDeleted', $v);
        }
    }

    #[Struct([
        self::MANY_MANY => RoleSoftDeleted::class,
        self::MIDDLE_ENTITY => UserRoleSoftDeleted::class,
        self::SOURCE_KEY => 'id',
        self::TARGET_KEY => 'id',
        self::MIDDLE_SOURCE_KEY => 'user_id',
        self::MIDDLE_TARGET_KEY => 'role_id',
        self::RELATION_SCOPE => 'onlySoftDeleted',
    ])]
    public ?Collection $roleMiddleOnlySoftDeleted = null
    {
        set (Collection|null $v) {
            $this->roleMiddleOnlySoftDeleted = $this->set('roleMiddleOnlySoftDeleted', $v);
        }
    }

    #[Struct([
        self::MANY_MANY => RoleSoftDeleted::class,
        self::MIDDLE_ENTITY => UserRoleSoftDeleted::class,
        self::SOURCE_KEY => 'id',
        self::TARGET_KEY => 'id',
        self::MIDDLE_SOURCE_KEY => 'user_id',
        self::MIDDLE_TARGET_KEY => 'role_id',
        self::RELATION_SCOPE => 'notFound',
    ])]
    public ?Collection $roleRelationScopeNotFound = null
    {
        set (Collection|null $v) {
            $this->roleRelationScopeNotFound = $this->set('roleRelationScopeNotFound', $v);
        }
    }

    #[Struct([
        self::MANY_MANY => RoleSoftDeleted::class,
        self::MIDDLE_ENTITY => UserRoleSoftDeleted::class,
        self::SOURCE_KEY => 'id',
        self::TARGET_KEY => 'id',
        self::MIDDLE_SOURCE_KEY => 'user_id',
        self::MIDDLE_TARGET_KEY => 'role_id',
        self::RELATION_SCOPE => 'foundButPrivate',
    ])]
    public ?Collection $roleRelationScopeFoundButPrivate = null
    {
        set (Collection|null $v) {
            $this->roleRelationScopeFoundButPrivate = $this->set('roleRelationScopeFoundButPrivate', $v);
        }
    }

    #[Struct([
        self::MANY_MANY => Role::class,
        self::SOURCE_KEY => 'id',
        self::TARGET_KEY => 'id',
        self::MIDDLE_SOURCE_KEY => 'user_id',
        self::MIDDLE_TARGET_KEY => 'role_id',
    ])]
    public ?Collection $roleNotDefinedMiddleEntity = null
    {
        set (Collection|null $v) {
            $this->roleNotDefinedMiddleEntity = $this->set('roleNotDefinedMiddleEntity', $v);
        }
    }

    #[Struct([
        self::MANY_MANY => Role::class,
        self::MIDDLE_ENTITY => UserRole::class,
        self::TARGET_KEY => 'id',
        self::MIDDLE_SOURCE_KEY => 'user_id',
        self::MIDDLE_TARGET_KEY => 'role_id',
    ])]
    public ?Collection $roleNotDefinedSourceKey = null
    {
        set (Collection|null $v) {
            $this->roleNotDefinedSourceKey = $this->set('roleNotDefinedSourceKey', $v);
        }
    }

    #[Struct([
        self::MANY_MANY => Role::class,
        self::MIDDLE_ENTITY => UserRole::class,
        self::SOURCE_KEY => 'id',
        self::MIDDLE_SOURCE_KEY => 'user_id',
        self::MIDDLE_TARGET_KEY => 'role_id',
    ])]
    public ?Collection $roleNotDefinedTargetKey = null
    {
        set (Collection|null $v) {
            $this->roleNotDefinedTargetKey = $this->set('roleNotDefinedTargetKey', $v);
        }
    }

    #[Struct([
        self::MANY_MANY => Role::class,
        self::MIDDLE_ENTITY => UserRole::class,
        self::SOURCE_KEY => 'id',
        self::TARGET_KEY => 'id',
        self::MIDDLE_TARGET_KEY => 'role_id',
    ])]
    public ?Collection $roleNotDefinedMiddleSourceKey = null
    {
        set (Collection|null $v) {
            $this->roleNotDefinedMiddleSourceKey = $this->set('roleNotDefinedMiddleSourceKey', $v);
        }
    }

    #[Struct([
        self::MANY_MANY => Role::class,
        self::MIDDLE_ENTITY => UserRole::class,
        self::SOURCE_KEY => 'id',
        self::TARGET_KEY => 'id',
        self::MIDDLE_SOURCE_KEY => 'user_id',
    ])]
    public ?Collection $roleNotDefinedMiddleTargetKey = null
    {
        set (Collection|null $v) {
            $this->roleNotDefinedMiddleTargetKey = $this->set('roleNotDefinedMiddleTargetKey', $v);
        }
    }

    #[Struct([
        self::MANY_MANY => Role::class,
        self::MIDDLE_ENTITY => UserRole::class,
        self::SOURCE_KEY => 'id',
        self::TARGET_KEY => 'id',
        self::MIDDLE_SOURCE_KEY => 'user_id',
        self::MIDDLE_TARGET_KEY => 'role_id',
        self::RELATION_SCOPE => 'middleField',
    ])]
    public ?Collection $roleMiddleField = null
    {
        set (Collection|null $v) {
            $this->roleMiddleField = $this->set('roleMiddleField', $v);
        }
    }

    #[Struct([
        self::MANY_MANY => RoleSoftDeleted::class,
        self::MIDDLE_ENTITY => UserRoleSoftDeleted::class,
        self::SOURCE_KEY => 'id',
        self::TARGET_KEY => 'id',
        self::MIDDLE_SOURCE_KEY => 'user_id',
        self::MIDDLE_TARGET_KEY => 'role_id',
        self::RELATION_SCOPE => 'middleOnlySoftDeletedAndMiddleFieldAndOtherTableCondition',
    ])]
    public ?Collection $roleMiddleOnlySoftDeletedAndMiddleFieldAndOtherTableCondition = null
    {
        set (Collection|null $v) {
            $this->roleMiddleOnlySoftDeletedAndMiddleFieldAndOtherTableCondition = $this->set('roleMiddleOnlySoftDeletedAndMiddleFieldAndOtherTableCondition', $v);
        }
    }

    protected function relationScopeWithSoftDeleted(ManyMany $relation): void
    {
        $relation->middleWithSoftDeleted();
    }

    protected function relationScopeOnlySoftDeleted(ManyMany $relation): void
    {
        $relation->middleOnlySoftDeleted();
    }

    protected function relationScopeMiddleField(ManyMany $relation): void
    {
        $relation->middleField(['create_at', 'middle_id' => 'id']);
    }

    protected function relationScopeMiddleOnlySoftDeletedAndMiddleFieldAndOtherTableCondition(ManyMany $relation): void
    {
        $relation
            ->middleOnlySoftDeleted()
            ->middleField(['create_at', 'middle_id' => 'id'])
            ->setColumns('id,name')
            ->where('id', '>', 3)
        ;
    }

    /** @phpstan-ignore-next-line */
    private function relationScopeFoundButPrivate(ManyMany $relation): void {}
}
