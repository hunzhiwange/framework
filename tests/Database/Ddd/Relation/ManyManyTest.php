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
        $user = User::where('id', 1)->findOne();

        $this->assertInstanceof(User::class, $user);
        $this->assertNull($user->id);

        $connect = $this->createDatabaseConnect();

        $this->assertSame(
            '1',
            $connect
                ->table('user')
                ->insert([
                    'name' => 'niu',
                ]));

        $this->assertSame(
            '1',
            $connect
                ->table('role')
                ->insert([
                    'name' => '管理员',
                ]));

        $this->assertSame(
            '2',
            $connect
                ->table('role')
                ->insert([
                    'name' => '版主',
                ]));

        $this->assertSame(
            '3',
            $connect
                ->table('role')
                ->insert([
                    'name' => '会员',
                ]));

        $this->assertSame(
            '1',
            $connect
                ->table('user_role')
                ->insert([
                    'user_id' => 1,
                    'role_id' => 1,
                ]));

        $this->assertSame(
            '2',
            $connect
                ->table('user_role')
                ->insert([
                    'user_id' => 1,
                    'role_id' => 3,
                ]));

        $user = User::where('id', 1)->findOne();

        $this->assertSame('1', $user->id);
        $this->assertSame('1', $user['id']);
        $this->assertSame('1', $user->getterId());
        $this->assertSame('niu', $user->name);
        $this->assertSame('niu', $user['name']);
        $this->assertSame('niu', $user->getterName());

        $role = $user->role;

        $this->assertInstanceof(Collection::class, $role);

        $user1 = $role[0];

        $this->assertSame('1', $user1->id);
        $this->assertSame('1', $user1['id']);
        $this->assertSame('1', $user1->getterId());
        $this->assertSame('管理员', $user1->name);
        $this->assertSame('管理员', $user1['name']);
        $this->assertSame('管理员', $user1->getterName());

        $user2 = $role[1];

        $this->assertSame('3', $user2->id);
        $this->assertSame('3', $user2['id']);
        $this->assertSame('3', $user2->getterId());
        $this->assertSame('会员', $user2->name);
        $this->assertSame('会员', $user2['name']);
        $this->assertSame('会员', $user2->getterName());

        $this->assertCount(2, $role);
        $this->assertSame('1', $role[0]['id']);
        $this->assertSame('管理员', $role[0]['name']);
        $this->assertSame('3', $role[1]['id']);
        $this->assertSame('会员', $role[1]['name']);

        $middle = $role[0]->middle();
        $this->assertInstanceof(UserRole::class, $middle);
        $this->assertSame('1', $middle->userId);
        $this->assertSame('1', $middle->roleId);

        $middle = $role[1]->middle();
        $this->assertInstanceof(UserRole::class, $middle);
        $this->assertSame('1', $middle->userId);
        $this->assertSame('3', $middle->roleId);
    }

    public function testEager(): void
    {
        $user = User::where('id', 1)->findOne();

        $this->assertInstanceof(User::class, $user);
        $this->assertNull($user->id);

        $connect = $this->createDatabaseConnect();

        $this->assertSame(
            '1',
            $connect
                ->table('user')
                ->insert([
                    'name' => 'niu',
                ]));

        $this->assertSame(
            '1',
            $connect
                ->table('role')
                ->insert([
                    'name' => '管理员',
                ]));

        $this->assertSame(
            '2',
            $connect
                ->table('role')
                ->insert([
                    'name' => '版主',
                ]));

        $this->assertSame(
            '3',
            $connect
                ->table('role')
                ->insert([
                    'name' => '会员',
                ]));

        $this->assertSame(
            '1',
            $connect
                ->table('user_role')
                ->insert([
                    'user_id' => 1,
                    'role_id' => 1,
                ]));

        $this->assertSame(
            '2',
            $connect
                ->table('user_role')
                ->insert([
                    'user_id' => 1,
                    'role_id' => 3,
                ]));

        $user = User::eager(['role'])
            ->where('id', 1)
            ->findOne();

        $this->assertSame('1', $user->id);
        $this->assertSame('1', $user['id']);
        $this->assertSame('1', $user->getterId());
        $this->assertSame('niu', $user->name);
        $this->assertSame('niu', $user['name']);
        $this->assertSame('niu', $user->getterName());

        $role = $user->role;

        $this->assertInstanceof(Collection::class, $role);

        $user1 = $role[0];

        $this->assertSame('1', $user1->id);
        $this->assertSame('1', $user1['id']);
        $this->assertSame('1', $user1->getterId());
        $this->assertSame('管理员', $user1->name);
        $this->assertSame('管理员', $user1['name']);
        $this->assertSame('管理员', $user1->getterName());

        $user2 = $role[1];

        $this->assertSame('3', $user2->id);
        $this->assertSame('3', $user2['id']);
        $this->assertSame('3', $user2->getterId());
        $this->assertSame('会员', $user2->name);
        $this->assertSame('会员', $user2['name']);
        $this->assertSame('会员', $user2->getterName());

        $this->assertCount(2, $role);
        $this->assertSame('1', $role[0]['id']);
        $this->assertSame('管理员', $role[0]['name']);
        $this->assertSame('3', $role[1]['id']);
        $this->assertSame('会员', $role[1]['name']);

        $middle = $role[0]->middle();
        $this->assertSame('1', $middle->userId);
        $this->assertSame('1', $middle->roleId);

        $middle = $role[1]->middle();
        $this->assertSame('1', $middle->userId);
        $this->assertSame('3', $middle->roleId);
    }

    public function testEagerWithNoData(): void
    {
        $user = User::where('id', 1)->findOne();

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
            '1',
            $connect
                ->table('user')
                ->insert([
                    'name' => 'niu',
                ]));

        $this->assertSame(
            '1',
            $connect
                ->table('role')
                ->insert([
                    'name' => '管理员',
                ]));

        $this->assertSame(
            '2',
            $connect
                ->table('role')
                ->insert([
                    'name' => '版主',
                ]));

        $this->assertSame(
            '3',
            $connect
                ->table('role')
                ->insert([
                    'name' => '会员',
                ]));

        $this->assertSame(
            '1',
            $connect
                ->table('user_role')
                ->insert([
                    'user_id' => 1,
                    'role_id' => 1,
                ]));

        $this->assertSame(
            '2',
            $connect
                ->table('user_role')
                ->insert([
                    'user_id' => 1,
                    'role_id' => 3,
                ]));

        $roleRelation = User::role();

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
        $user = User::where('id', 1)->findOne();

        $this->assertInstanceof(User::class, $user);
        $this->assertNull($user->id);

        $connect = $this->createDatabaseConnect();

        $this->assertSame(
            '1',
            $connect
                ->table('user')
                ->insert([
                    'name' => 'niu',
                ]));

        $user = User::where('id', 1)->findOne();

        $this->assertSame('1', $user->id);
        $this->assertSame('1', $user['id']);
        $this->assertSame('1', $user->getterId());
        $this->assertSame('niu', $user->name);
        $this->assertSame('niu', $user['name']);
        $this->assertSame('niu', $user->getterName());

        $role = $user->role;

        $this->assertInstanceof(Collection::class, $role);
        $this->assertCount(0, $role);
    }

    protected function getDatabaseTable(): array
    {
        return ['user', 'user_role', 'role'];
    }
}
