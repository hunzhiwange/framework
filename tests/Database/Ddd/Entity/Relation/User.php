<?php

declare(strict_types=1);

namespace Tests\Database\Ddd\Entity\Relation;

use Leevel\Database\Ddd\Entity;
use Leevel\Database\Ddd\GetterSetter;
use Leevel\Database\Ddd\Relation\ManyMany;

class User extends Entity
{
    use GetterSetter;

    const TABLE = 'user';

    const ID = 'id';

    const AUTO = 'id';

    const STRUCT = [
        'id'        => [],
        'name'      => [],
        'create_at' => [],
        'role'      => [
            self::MANY_MANY         => Role::class,
            self::MIDDLE_ENTITY     => UserRole::class,
            self::SOURCE_KEY        => 'id',
            self::TARGET_KEY        => 'id',
            self::MIDDLE_SOURCE_KEY => 'user_id',
            self::MIDDLE_TARGET_KEY => 'role_id',
        ],
        'role_soft_deleted'      => [
            self::MANY_MANY         => RoleSoftDeleted::class,
            self::MIDDLE_ENTITY     => UserRoleSoftDeleted::class,
            self::SOURCE_KEY        => 'id',
            self::TARGET_KEY        => 'id',
            self::MIDDLE_SOURCE_KEY => 'user_id',
            self::MIDDLE_TARGET_KEY => 'role_id',
        ],
        'role_middle_with_soft_deleted'      => [
            self::MANY_MANY         => RoleSoftDeleted::class,
            self::MIDDLE_ENTITY     => UserRoleSoftDeleted::class,
            self::SOURCE_KEY        => 'id',
            self::TARGET_KEY        => 'id',
            self::MIDDLE_SOURCE_KEY => 'user_id',
            self::MIDDLE_TARGET_KEY => 'role_id',
            self::RELATION_SCOPE    => 'withSoftDeleted',
        ],
        'role_middle_only_soft_deleted'      => [
            self::MANY_MANY         => RoleSoftDeleted::class,
            self::MIDDLE_ENTITY     => UserRoleSoftDeleted::class,
            self::SOURCE_KEY        => 'id',
            self::TARGET_KEY        => 'id',
            self::MIDDLE_SOURCE_KEY => 'user_id',
            self::MIDDLE_TARGET_KEY => 'role_id',
            self::RELATION_SCOPE    => 'onlySoftDeleted',
        ],
        'role_relation_scope_not_found'      => [
            self::MANY_MANY         => RoleSoftDeleted::class,
            self::MIDDLE_ENTITY     => UserRoleSoftDeleted::class,
            self::SOURCE_KEY        => 'id',
            self::TARGET_KEY        => 'id',
            self::MIDDLE_SOURCE_KEY => 'user_id',
            self::MIDDLE_TARGET_KEY => 'role_id',
            self::RELATION_SCOPE    => 'notFound',
        ],
        'role_relation_scope_found_but_private'      => [
            self::MANY_MANY         => RoleSoftDeleted::class,
            self::MIDDLE_ENTITY     => UserRoleSoftDeleted::class,
            self::SOURCE_KEY        => 'id',
            self::TARGET_KEY        => 'id',
            self::MIDDLE_SOURCE_KEY => 'user_id',
            self::MIDDLE_TARGET_KEY => 'role_id',
            self::RELATION_SCOPE    => 'foundButPrivate',
        ],
        'role_not_defined_middle_entity'      => [
            self::MANY_MANY         => Role::class,
            self::SOURCE_KEY        => 'id',
            self::TARGET_KEY        => 'id',
            self::MIDDLE_SOURCE_KEY => 'user_id',
            self::MIDDLE_TARGET_KEY => 'role_id',
        ],
        'role_not_defined_source_key'      => [
            self::MANY_MANY         => Role::class,
            self::MIDDLE_ENTITY     => UserRole::class,
            self::TARGET_KEY        => 'id',
            self::MIDDLE_SOURCE_KEY => 'user_id',
            self::MIDDLE_TARGET_KEY => 'role_id',
        ],
        'role_not_defined_target_key'      => [
            self::MANY_MANY         => Role::class,
            self::MIDDLE_ENTITY     => UserRole::class,
            self::SOURCE_KEY        => 'id',
            self::MIDDLE_SOURCE_KEY => 'user_id',
            self::MIDDLE_TARGET_KEY => 'role_id',
        ],
        'role_not_defined_middle_source_key'      => [
            self::MANY_MANY         => Role::class,
            self::MIDDLE_ENTITY     => UserRole::class,
            self::SOURCE_KEY        => 'id',
            self::TARGET_KEY        => 'id',
            self::MIDDLE_TARGET_KEY => 'role_id',
        ],
        'role_not_defined_middle_target_key'      => [
            self::MANY_MANY         => Role::class,
            self::MIDDLE_ENTITY     => UserRole::class,
            self::SOURCE_KEY        => 'id',
            self::TARGET_KEY        => 'id',
            self::MIDDLE_SOURCE_KEY => 'user_id',
        ],
        'role_middle_field'      => [
            self::MANY_MANY         => Role::class,
            self::MIDDLE_ENTITY     => UserRole::class,
            self::SOURCE_KEY        => 'id',
            self::TARGET_KEY        => 'id',
            self::MIDDLE_SOURCE_KEY => 'user_id',
            self::MIDDLE_TARGET_KEY => 'role_id',
            self::RELATION_SCOPE    => 'middleField',
        ],
        'role_middle_only_soft_deleted_and_middle_field_and_other_table_condition' => [
            self::MANY_MANY         => RoleSoftDeleted::class,
            self::MIDDLE_ENTITY     => UserRoleSoftDeleted::class,
            self::SOURCE_KEY        => 'id',
            self::TARGET_KEY        => 'id',
            self::MIDDLE_SOURCE_KEY => 'user_id',
            self::MIDDLE_TARGET_KEY => 'role_id',
            self::RELATION_SCOPE    => 'middleOnlySoftDeletedAndMiddleFieldAndOtherTableCondition',
        ],
    ];

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
            ->where('id', '>', 3);
    }

    private function relationScopeFoundButPrivate(ManyMany $relation): void
    {
    }
}
