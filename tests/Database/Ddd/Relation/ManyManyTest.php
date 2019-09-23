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
 * (c) 2010-2019 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Database\Ddd\Relation;

use Leevel\Collection\Collection;
use Leevel\Database\Ddd\Relation\ManyMany;
use Leevel\Database\Ddd\Relation\Relation;
use Leevel\Database\Ddd\Select;
use Tests\Database\DatabaseTestCase as TestCase;
use Tests\Database\Ddd\Entity\Relation\Role;
use Tests\Database\Ddd\Entity\Relation\User;
use Tests\Database\Ddd\Entity\Relation\UserRole;

/**
 * manyMany test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.10.14
 *
 * @version 1.0
 */
class ManyManyTest extends TestCase
{
    public function testBaseUse(): void
    {
        $user = User::select()->where('id', 1)->findOne();

        $this->assertInstanceof(User::class, $user);
        $this->assertNull($user->id);

        $connect = $this->createDatabaseConnect();

        $this->assertSame(
            1,
            $connect
                ->table('user')
                ->insert([
                    'name' => 'niu',
                ]));

        $this->assertSame(
            1,
            $connect
                ->table('role')
                ->insert([
                    'name' => '管理员',
                ]));

        $this->assertSame(
            2,
            $connect
                ->table('role')
                ->insert([
                    'name' => '版主',
                ]));

        $this->assertSame(
            3,
            $connect
                ->table('role')
                ->insert([
                    'name' => '会员',
                ]));

        $this->assertSame(
            1,
            $connect
                ->table('user_role')
                ->insert([
                    'user_id' => 1,
                    'role_id' => 1,
                ]));

        $this->assertSame(
            2,
            $connect
                ->table('user_role')
                ->insert([
                    'user_id' => 1,
                    'role_id' => 3,
                ]));

        $user = User::select()->where('id', 1)->findOne();

        $this->assertSame(1, $user->id);
        $this->assertSame(1, $user['id']);
        $this->assertSame(1, $user->getId());
        $this->assertSame('niu', $user->name);
        $this->assertSame('niu', $user['name']);
        $this->assertSame('niu', $user->getName());

        $role = $user->role;

        $this->assertInstanceof(Collection::class, $role);

        $user1 = $role[0];

        $this->assertSame(1, $user1->id);
        $this->assertSame(1, $user1['id']);
        $this->assertSame(1, $user1->getId());
        $this->assertSame('管理员', $user1->name);
        $this->assertSame('管理员', $user1['name']);
        $this->assertSame('管理员', $user1->getName());

        $user2 = $role[1];

        $this->assertSame(3, $user2->id);
        $this->assertSame(3, $user2['id']);
        $this->assertSame(3, $user2->getId());
        $this->assertSame('会员', $user2->name);
        $this->assertSame('会员', $user2['name']);
        $this->assertSame('会员', $user2->getName());

        $this->assertCount(2, $role);
        $this->assertSame(1, $role[0]['id']);
        $this->assertSame('管理员', $role[0]['name']);
        $this->assertSame(3, $role[1]['id']);
        $this->assertSame('会员', $role[1]['name']);

        $middle = $role[0]->middle();
        $this->assertInstanceof(UserRole::class, $middle);
        $this->assertSame(1, $middle->userId);
        $this->assertSame(1, $middle->roleId);

        $middle = $role[1]->middle();
        $this->assertInstanceof(UserRole::class, $middle);
        $this->assertSame(1, $middle->userId);
        $this->assertSame(3, $middle->roleId);
    }

    public function testEager(): void
    {
        $user = User::select()->where('id', 1)->findOne();

        $this->assertInstanceof(User::class, $user);
        $this->assertNull($user->id);

        $connect = $this->createDatabaseConnect();

        $this->assertSame(
            1,
            $connect
                ->table('user')
                ->insert([
                    'name' => 'niu',
                ]));

        $this->assertSame(
            1,
            $connect
                ->table('role')
                ->insert([
                    'name' => '管理员',
                ]));

        $this->assertSame(
            2,
            $connect
                ->table('role')
                ->insert([
                    'name' => '版主',
                ]));

        $this->assertSame(
            3,
            $connect
                ->table('role')
                ->insert([
                    'name' => '会员',
                ]));

        $this->assertSame(
            1,
            $connect
                ->table('user_role')
                ->insert([
                    'user_id' => 1,
                    'role_id' => 1,
                ]));

        $this->assertSame(
            2,
            $connect
                ->table('user_role')
                ->insert([
                    'user_id' => 1,
                    'role_id' => 3,
                ]));

        $user = User::eager(['role'])
            ->where('id', 1)
            ->findOne();

        $this->assertSame(1, $user->id);
        $this->assertSame(1, $user['id']);
        $this->assertSame(1, $user->getId());
        $this->assertSame('niu', $user->name);
        $this->assertSame('niu', $user['name']);
        $this->assertSame('niu', $user->getName());

        $role = $user->role;

        $this->assertInstanceof(Collection::class, $role);

        $user1 = $role[0];

        $this->assertSame(1, $user1->id);
        $this->assertSame(1, $user1['id']);
        $this->assertSame(1, $user1->getId());
        $this->assertSame('管理员', $user1->name);
        $this->assertSame('管理员', $user1['name']);
        $this->assertSame('管理员', $user1->getName());

        $user2 = $role[1];

        $this->assertSame(3, $user2->id);
        $this->assertSame(3, $user2['id']);
        $this->assertSame(3, $user2->getId());
        $this->assertSame('会员', $user2->name);
        $this->assertSame('会员', $user2['name']);
        $this->assertSame('会员', $user2->getName());

        $this->assertCount(2, $role);
        $this->assertSame(1, $role[0]['id']);
        $this->assertSame('管理员', $role[0]['name']);
        $this->assertSame(3, $role[1]['id']);
        $this->assertSame('会员', $role[1]['name']);

        $middle = $role[0]->middle();
        $this->assertSame(1, $middle->userId);
        $this->assertSame(1, $middle->roleId);

        $middle = $role[1]->middle();
        $this->assertSame(1, $middle->userId);
        $this->assertSame(3, $middle->roleId);
    }

    public function testEagerWithNoData(): void
    {
        $user = User::select()->where('id', 1)->findOne();

        $this->assertInstanceof(User::class, $user);
        $this->assertNull($user->id);

        $user = User::eager(['role'])
            ->where('id', 1)
            ->findOne();

        $this->assertInstanceof(User::class, $user);
        $this->assertNull($user->id);

        $role = $user->role;
        $this->assertInstanceof(Collection::class, $role);
        $this->assertCount(0, $role);
    }

    public function testRelationAsMethod(): void
    {
        $connect = $this->createDatabaseConnect();

        $this->assertSame(
            1,
            $connect
                ->table('user')
                ->insert([
                    'name' => 'niu',
                ]));

        $this->assertSame(
            1,
            $connect
                ->table('role')
                ->insert([
                    'name' => '管理员',
                ]));

        $this->assertSame(
            2,
            $connect
                ->table('role')
                ->insert([
                    'name' => '版主',
                ]));

        $this->assertSame(
            3,
            $connect
                ->table('role')
                ->insert([
                    'name' => '会员',
                ]));

        $this->assertSame(
            1,
            $connect
                ->table('user_role')
                ->insert([
                    'user_id' => 1,
                    'role_id' => 1,
                ]));

        $this->assertSame(
            2,
            $connect
                ->table('user_role')
                ->insert([
                    'user_id' => 1,
                    'role_id' => 3,
                ]));

        $roleRelation = User::make()->loadRelation('role');

        $this->assertInstanceof(ManyMany::class, $roleRelation);
        $this->assertSame('id', $roleRelation->getSourceKey());
        $this->assertSame('id', $roleRelation->getTargetKey());
        $this->assertSame('user_id', $roleRelation->getMiddleSourceKey());
        $this->assertSame('role_id', $roleRelation->getMiddleTargetKey());
        $this->assertInstanceof(User::class, $roleRelation->getSourceEntity());
        $this->assertInstanceof(Role::class, $roleRelation->getTargetEntity());
        $this->assertInstanceof(UserRole::class, $roleRelation->getMiddleEntity());
        $this->assertInstanceof(Select::class, $roleRelation->getSelect());
    }

    public function testNotFoundData(): void
    {
        $user = User::select()->where('id', 1)->findOne();

        $this->assertInstanceof(User::class, $user);
        $this->assertNull($user->id);

        $connect = $this->createDatabaseConnect();

        $this->assertSame(
            1,
            $connect
                ->table('user')
                ->insert([
                    'name' => 'niu',
                ]));

        $user = User::select()->where('id', 1)->findOne();

        $this->assertSame(1, $user->id);
        $this->assertSame(1, $user['id']);
        $this->assertSame(1, $user->getId());
        $this->assertSame('niu', $user->name);
        $this->assertSame('niu', $user['name']);
        $this->assertSame('niu', $user->getName());

        $role = $user->role;

        $this->assertInstanceof(Collection::class, $role);
        $this->assertCount(0, $role);
    }

    public function testSourceDataIsEmtpy(): void
    {
        $user = User::select()->where('id', 1)->findOne();

        $this->assertInstanceof(User::class, $user);
        $this->assertNull($user->id);

        $connect = $this->createDatabaseConnect();

        $this->assertSame(
            1,
            $connect
                ->table('user')
                ->insert([
                    'name' => 'niu',
                ]));

        $this->assertSame(
            1,
            $connect
                ->table('role')
                ->insert([
                    'name' => '管理员',
                ]));

        $this->assertSame(
            2,
            $connect
                ->table('role')
                ->insert([
                    'name' => '版主',
                ]));

        $this->assertSame(
            3,
            $connect
                ->table('role')
                ->insert([
                    'name' => '会员',
                ]));

        $user = User::select()->where('id', 1)->findOne();

        $this->assertSame(1, $user->id);
        $this->assertSame(1, $user['id']);
        $this->assertSame(1, $user->getId());
        $this->assertSame('niu', $user->name);
        $this->assertSame('niu', $user['name']);
        $this->assertSame('niu', $user->getName());

        $role = $user->role;

        $this->assertInstanceof(Collection::class, $role);
        $this->assertCount(0, $role);
    }

    public function testEagerSourceDataIsEmtpy(): void
    {
        $user = User::select()->where('id', 1)->findOne();

        $this->assertInstanceof(User::class, $user);
        $this->assertNull($user->id);

        $connect = $this->createDatabaseConnect();

        $this->assertSame(
            1,
            $connect
                ->table('user')
                ->insert([
                    'name' => 'niu',
                ]));

        $this->assertSame(
            1,
            $connect
                ->table('role')
                ->insert([
                    'name' => '管理员',
                ]));

        $this->assertSame(
            2,
            $connect
                ->table('role')
                ->insert([
                    'name' => '版主',
                ]));

        $this->assertSame(
            3,
            $connect
                ->table('role')
                ->insert([
                    'name' => '会员',
                ]));

        $user = User::eager(['role'])
            ->where('id', 1)
            ->findOne();

        $this->assertSame(1, $user->id);
        $this->assertSame(1, $user['id']);
        $this->assertSame(1, $user->getId());
        $this->assertSame('niu', $user->name);
        $this->assertSame('niu', $user['name']);
        $this->assertSame('niu', $user->getName());

        $role = $user->role;

        $this->assertInstanceof(Collection::class, $role);
        $this->assertCount(0, $role);
    }

    public function testEagerWithCondition(): void
    {
        $user = User::select()->where('id', 1)->findOne();

        $this->assertInstanceof(User::class, $user);
        $this->assertNull($user->id);

        $connect = $this->createDatabaseConnect();

        $this->assertSame(
            1,
            $connect
                ->table('user')
                ->insert([
                    'name' => 'niu',
                ]));

        $this->assertSame(
            1,
            $connect
                ->table('role')
                ->insert([
                    'name' => '管理员',
                ]));

        $this->assertSame(
            2,
            $connect
                ->table('role')
                ->insert([
                    'name' => '版主',
                ]));

        $this->assertSame(
            3,
            $connect
                ->table('role')
                ->insert([
                    'name' => '会员',
                ]));

        $this->assertSame(
            1,
            $connect
                ->table('user_role')
                ->insert([
                    'user_id' => 1,
                    'role_id' => 1,
                ]));

        $this->assertSame(
            2,
            $connect
                ->table('user_role')
                ->insert([
                    'user_id' => 1,
                    'role_id' => 3,
                ]));

        $user = User::eager(['role' => function (Relation $select) {
            $select->where('id', '>', 99999);
        }])
            ->where('id', 1)
            ->findOne();

        $this->assertSame(1, $user->id);
        $this->assertSame(1, $user['id']);
        $this->assertSame(1, $user->getId());
        $this->assertSame('niu', $user->name);
        $this->assertSame('niu', $user['name']);
        $this->assertSame('niu', $user->getName());

        $role = $user->role;

        $this->assertInstanceof(Collection::class, $role);
        $this->assertCount(0, $role);
    }

    public function testValidateRelationKeyNotDefinedMiddleEntity(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Relation `middle_entity` field was not defined.'
        );

        $user = User::select()->where('id', 1)->findOne();

        $this->assertInstanceof(User::class, $user);
        $this->assertNull($user->id);
        $user->roleNotDefinedMiddleEntity;
    }

    public function testValidateRelationKeyNotDefinedSourceKey(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Relation `source_key` field was not defined.'
        );

        $user = User::select()->where('id', 1)->findOne();

        $this->assertInstanceof(User::class, $user);
        $this->assertNull($user->id);
        $user->roleNotDefinedSourceKey;
    }

    public function testValidateRelationKeyNotDefinedTargetKey(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Relation `target_key` field was not defined.'
        );

        $user = User::select()->where('id', 1)->findOne();

        $this->assertInstanceof(User::class, $user);
        $this->assertNull($user->id);
        $user->roleNotDefinedTargetKey;
    }

    public function testValidateRelationKeyNotDefinedMiddleSourceKey(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Relation `middle_source_key` field was not defined.'
        );

        $user = User::select()->where('id', 1)->findOne();

        $this->assertInstanceof(User::class, $user);
        $this->assertNull($user->id);
        $user->roleNotDefinedMiddleSourceKey;
    }

    public function testValidateRelationKeyNotDefinedMiddleTargetKey(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Relation `middle_target_key` field was not defined.'
        );

        $user = User::select()->where('id', 1)->findOne();

        $this->assertInstanceof(User::class, $user);
        $this->assertNull($user->id);
        $user->roleNotDefinedMiddleTargetKey;
    }

    public function testValidateRelationFieldSourceKey(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'The field `user`.`not_found_source_key` of entity `Tests\\Database\\Ddd\\Entity\\Relation\\User` was not defined.'
        );

        $user = User::select()->where('id', 1)->findOne();
        $this->assertInstanceof(User::class, $user);
        $this->assertNull($user->id);

        $user->manyMany(Role::class, UserRole::class, 'id', 'not_found_source_key', 'role_id', 'user_id');
    }

    public function testValidateRelationFieldTargetKey(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'The field `role`.`not_found_target_key` of entity `Tests\\Database\\Ddd\\Entity\\Relation\\Role` was not defined.'
        );

        $user = User::select()->where('id', 1)->findOne();
        $this->assertInstanceof(User::class, $user);
        $this->assertNull($user->id);

        $user->manyMany(Role::class, UserRole::class, 'not_found_target_key', 'id', 'role_id', 'user_id');
    }

    public function testValidateRelationFieldMiddleSourceKey(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'The field `user_role`.`not_found_middle_source_key` of entity `Tests\\Database\\Ddd\\Entity\\Relation\\UserRole` was not defined.'
        );

        $user = User::select()->where('id', 1)->findOne();
        $this->assertInstanceof(User::class, $user);
        $this->assertNull($user->id);

        $user->manyMany(Role::class, UserRole::class, 'id', 'id', 'role_id', 'not_found_middle_source_key');
    }

    public function testValidateRelationFieldMiddleTargetKey(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'The field `user_role`.`not_found_middle_target_key` of entity `Tests\\Database\\Ddd\\Entity\\Relation\\UserRole` was not defined.'
        );

        $user = User::select()->where('id', 1)->findOne();
        $this->assertInstanceof(User::class, $user);
        $this->assertNull($user->id);

        $user->manyMany(Role::class, UserRole::class, 'id', 'id', 'not_found_middle_target_key', 'user_id');
    }

    protected function getDatabaseTable(): array
    {
        return ['user', 'user_role', 'role'];
    }
}
