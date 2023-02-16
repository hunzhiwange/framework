<?php

declare(strict_types=1);

namespace Tests\Database\Ddd\Entity\Relation;

use Leevel\Database\Ddd\Entity;
use Leevel\Database\Ddd\Relation\ManyMany;
use Leevel\Database\Ddd\Struct;

class User extends Entity
{
    public const TABLE = 'user';

    public const ID = 'id';

    public const AUTO = 'id';

    public const STRUCT = [
        'id' => [],
        'name' => [],
        'create_at' => [],
        'role' => [
            self::MANY_MANY => Role::class,
            self::MIDDLE_ENTITY => UserRole::class,
            self::SOURCE_KEY => 'id',
            self::TARGET_KEY => 'id',
            self::MIDDLE_SOURCE_KEY => 'user_id',
            self::MIDDLE_TARGET_KEY => 'role_id',
        ],
        'role_soft_deleted' => [
            self::MANY_MANY => RoleSoftDeleted::class,
            self::MIDDLE_ENTITY => UserRoleSoftDeleted::class,
            self::SOURCE_KEY => 'id',
            self::TARGET_KEY => 'id',
            self::MIDDLE_SOURCE_KEY => 'user_id',
            self::MIDDLE_TARGET_KEY => 'role_id',
        ],
        'role_middle_with_soft_deleted' => [
            self::MANY_MANY => RoleSoftDeleted::class,
            self::MIDDLE_ENTITY => UserRoleSoftDeleted::class,
            self::SOURCE_KEY => 'id',
            self::TARGET_KEY => 'id',
            self::MIDDLE_SOURCE_KEY => 'user_id',
            self::MIDDLE_TARGET_KEY => 'role_id',
            self::RELATION_SCOPE => 'withSoftDeleted',
        ],
        'role_middle_only_soft_deleted' => [
            self::MANY_MANY => RoleSoftDeleted::class,
            self::MIDDLE_ENTITY => UserRoleSoftDeleted::class,
            self::SOURCE_KEY => 'id',
            self::TARGET_KEY => 'id',
            self::MIDDLE_SOURCE_KEY => 'user_id',
            self::MIDDLE_TARGET_KEY => 'role_id',
            self::RELATION_SCOPE => 'onlySoftDeleted',
        ],
        'role_relation_scope_not_found' => [
            self::MANY_MANY => RoleSoftDeleted::class,
            self::MIDDLE_ENTITY => UserRoleSoftDeleted::class,
            self::SOURCE_KEY => 'id',
            self::TARGET_KEY => 'id',
            self::MIDDLE_SOURCE_KEY => 'user_id',
            self::MIDDLE_TARGET_KEY => 'role_id',
            self::RELATION_SCOPE => 'notFound',
        ],
        'role_relation_scope_found_but_private' => [
            self::MANY_MANY => RoleSoftDeleted::class,
            self::MIDDLE_ENTITY => UserRoleSoftDeleted::class,
            self::SOURCE_KEY => 'id',
            self::TARGET_KEY => 'id',
            self::MIDDLE_SOURCE_KEY => 'user_id',
            self::MIDDLE_TARGET_KEY => 'role_id',
            self::RELATION_SCOPE => 'foundButPrivate',
        ],
        'role_not_defined_middle_entity' => [
            self::MANY_MANY => Role::class,
            self::SOURCE_KEY => 'id',
            self::TARGET_KEY => 'id',
            self::MIDDLE_SOURCE_KEY => 'user_id',
            self::MIDDLE_TARGET_KEY => 'role_id',
        ],
        'role_not_defined_source_key' => [
            self::MANY_MANY => Role::class,
            self::MIDDLE_ENTITY => UserRole::class,
            self::TARGET_KEY => 'id',
            self::MIDDLE_SOURCE_KEY => 'user_id',
            self::MIDDLE_TARGET_KEY => 'role_id',
        ],
        'role_not_defined_target_key' => [
            self::MANY_MANY => Role::class,
            self::MIDDLE_ENTITY => UserRole::class,
            self::SOURCE_KEY => 'id',
            self::MIDDLE_SOURCE_KEY => 'user_id',
            self::MIDDLE_TARGET_KEY => 'role_id',
        ],
        'role_not_defined_middle_source_key' => [
            self::MANY_MANY => Role::class,
            self::MIDDLE_ENTITY => UserRole::class,
            self::SOURCE_KEY => 'id',
            self::TARGET_KEY => 'id',
            self::MIDDLE_TARGET_KEY => 'role_id',
        ],
        'role_not_defined_middle_target_key' => [
            self::MANY_MANY => Role::class,
            self::MIDDLE_ENTITY => UserRole::class,
            self::SOURCE_KEY => 'id',
            self::TARGET_KEY => 'id',
            self::MIDDLE_SOURCE_KEY => 'user_id',
        ],
        'role_middle_field' => [
            self::MANY_MANY => Role::class,
            self::MIDDLE_ENTITY => UserRole::class,
            self::SOURCE_KEY => 'id',
            self::TARGET_KEY => 'id',
            self::MIDDLE_SOURCE_KEY => 'user_id',
            self::MIDDLE_TARGET_KEY => 'role_id',
            self::RELATION_SCOPE => 'middleField',
        ],
        'role_middle_only_soft_deleted_and_middle_field_and_other_table_condition' => [
            self::MANY_MANY => RoleSoftDeleted::class,
            self::MIDDLE_ENTITY => UserRoleSoftDeleted::class,
            self::SOURCE_KEY => 'id',
            self::TARGET_KEY => 'id',
            self::MIDDLE_SOURCE_KEY => 'user_id',
            self::MIDDLE_TARGET_KEY => 'role_id',
            self::RELATION_SCOPE => 'middleOnlySoftDeletedAndMiddleFieldAndOtherTableCondition',
        ],
    ];

    #[Struct([
    ])]
    protected ?int $id = null;

    #[Struct([
    ])]
    protected ?string $name = null;

    #[Struct([
    ])]
    protected ?string $createAt = null;

    #[Struct([
        self::MANY_MANY => Role::class,
        self::MIDDLE_ENTITY => UserRole::class,
        self::SOURCE_KEY => 'id',
        self::TARGET_KEY => 'id',
        self::MIDDLE_SOURCE_KEY => 'user_id',
        self::MIDDLE_TARGET_KEY => 'role_id',
    ])]
    protected ?string $role = null;

    #[Struct([
        self::MANY_MANY => RoleSoftDeleted::class,
        self::MIDDLE_ENTITY => UserRoleSoftDeleted::class,
        self::SOURCE_KEY => 'id',
        self::TARGET_KEY => 'id',
        self::MIDDLE_SOURCE_KEY => 'user_id',
        self::MIDDLE_TARGET_KEY => 'role_id',
    ])]
    protected ?int $roleSoftDeleted = null;

    #[Struct([
        self::MANY_MANY => RoleSoftDeleted::class,
        self::MIDDLE_ENTITY => UserRoleSoftDeleted::class,
        self::SOURCE_KEY => 'id',
        self::TARGET_KEY => 'id',
        self::MIDDLE_SOURCE_KEY => 'user_id',
        self::MIDDLE_TARGET_KEY => 'role_id',
        self::RELATION_SCOPE => 'withSoftDeleted',
    ])]
    protected ?int $roleMiddleWithSoftDeleted = null;

    #[Struct([
        self::MANY_MANY => RoleSoftDeleted::class,
        self::MIDDLE_ENTITY => UserRoleSoftDeleted::class,
        self::SOURCE_KEY => 'id',
        self::TARGET_KEY => 'id',
        self::MIDDLE_SOURCE_KEY => 'user_id',
        self::MIDDLE_TARGET_KEY => 'role_id',
        self::RELATION_SCOPE => 'onlySoftDeleted',
    ])]
    protected ?int $roleMiddleOnlySoftDeleted = null;

    #[Struct([
        self::MANY_MANY => RoleSoftDeleted::class,
        self::MIDDLE_ENTITY => UserRoleSoftDeleted::class,
        self::SOURCE_KEY => 'id',
        self::TARGET_KEY => 'id',
        self::MIDDLE_SOURCE_KEY => 'user_id',
        self::MIDDLE_TARGET_KEY => 'role_id',
        self::RELATION_SCOPE => 'notFound',
    ])]
    protected ?int $roleRelationScopeNotFound = null;

    #[Struct([
        self::MANY_MANY => RoleSoftDeleted::class,
        self::MIDDLE_ENTITY => UserRoleSoftDeleted::class,
        self::SOURCE_KEY => 'id',
        self::TARGET_KEY => 'id',
        self::MIDDLE_SOURCE_KEY => 'user_id',
        self::MIDDLE_TARGET_KEY => 'role_id',
        self::RELATION_SCOPE => 'foundButPrivate',
    ])]
    protected ?int $roleRelationScopeFoundButPrivate = null;

    #[Struct([
        self::MANY_MANY => Role::class,
        self::SOURCE_KEY => 'id',
        self::TARGET_KEY => 'id',
        self::MIDDLE_SOURCE_KEY => 'user_id',
        self::MIDDLE_TARGET_KEY => 'role_id',
    ])]
    protected ?int $roleNotDefinedMiddleEntity = null;

    #[Struct([
        self::MANY_MANY => Role::class,
        self::MIDDLE_ENTITY => UserRole::class,
        self::TARGET_KEY => 'id',
        self::MIDDLE_SOURCE_KEY => 'user_id',
        self::MIDDLE_TARGET_KEY => 'role_id',
    ])]
    protected ?int $roleNotDefinedSourceKey = null;

    #[Struct([
        self::MANY_MANY => Role::class,
        self::MIDDLE_ENTITY => UserRole::class,
        self::SOURCE_KEY => 'id',
        self::MIDDLE_SOURCE_KEY => 'user_id',
        self::MIDDLE_TARGET_KEY => 'role_id',
    ])]
    protected ?int $roleNotDefinedTargetKey = null;

    #[Struct([
        self::MANY_MANY => Role::class,
        self::MIDDLE_ENTITY => UserRole::class,
        self::SOURCE_KEY => 'id',
        self::TARGET_KEY => 'id',
        self::MIDDLE_TARGET_KEY => 'role_id',
    ])]
    protected ?int $roleNotDefinedMiddleSourceKey = null;

    #[Struct([
        self::MANY_MANY => Role::class,
        self::MIDDLE_ENTITY => UserRole::class,
        self::SOURCE_KEY => 'id',
        self::TARGET_KEY => 'id',
        self::MIDDLE_SOURCE_KEY => 'user_id',
    ])]
    protected ?int $roleNotDefinedMiddleTargetKey = null;

    #[Struct([
        self::MANY_MANY => Role::class,
        self::MIDDLE_ENTITY => UserRole::class,
        self::SOURCE_KEY => 'id',
        self::TARGET_KEY => 'id',
        self::MIDDLE_SOURCE_KEY => 'user_id',
        self::MIDDLE_TARGET_KEY => 'role_id',
        self::RELATION_SCOPE => 'middleField',
    ])]
    protected ?int $roleMiddleField = null;

    #[Struct([
        self::MANY_MANY => RoleSoftDeleted::class,
        self::MIDDLE_ENTITY => UserRoleSoftDeleted::class,
        self::SOURCE_KEY => 'id',
        self::TARGET_KEY => 'id',
        self::MIDDLE_SOURCE_KEY => 'user_id',
        self::MIDDLE_TARGET_KEY => 'role_id',
        self::RELATION_SCOPE => 'middleOnlySoftDeletedAndMiddleFieldAndOtherTableCondition',
    ])]
    protected ?int $roleMiddleOnlySoftDeletedAndMiddleFieldAndOtherTableCondition = null;

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

    protected function relationScopeFoundButPrivate(ManyMany $relation): void
    {
    }
}
