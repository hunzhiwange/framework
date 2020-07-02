<?php

declare(strict_types=1);

/*
 * This file is part of the ************************ package.
 * _____________                           _______________
 *  ______/     \__  _____  ____  ______  / /_  _________
 *   ____/ __   / / / / _ \/ __`\/ / __ \/ __ \/ __ \___
 *    __/ / /  / /_/ /  __/ /  \  / /_/ / / / / /_/ /__
 *      \_\ \_/\____/\___/_/   / / .___/_/ /_/ .___/
 *         \_\                /_/_/         /_/
 *
 * The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
 * (c) 2010-2020 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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

    private function relationScopeFoundButPrivate(ManyMany $relation): void
    {
    }
}
