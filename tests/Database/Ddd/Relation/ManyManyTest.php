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

namespace Tests\Database\Ddd\Relation;

use Leevel\Collection\Collection;
use Leevel\Database\Ddd\Relation\ManyMany;
use Leevel\Database\Ddd\Relation\Relation;
use Leevel\Database\Ddd\Select;
use Tests\Database\DatabaseTestCase as TestCase;
use Tests\Database\Ddd\Entity\Relation\Role;
use Tests\Database\Ddd\Entity\Relation\RoleSoftDeleted;
use Tests\Database\Ddd\Entity\Relation\User;
use Tests\Database\Ddd\Entity\Relation\UserRole;
use Tests\Database\Ddd\Entity\Relation\UserRoleSoftDeleted;

/**
 * @api(
 *     title="manyMany 多对多关联",
 *     path="orm/manymany",
 *     description="
 * 多对多的关联是一种常用的关联，比如用户与角色属于多对多的关系。
 *
 * **多对多关联支持类型关联项**
 *
 * |  关联项   | 说明  |    例子   |
 * |  ----  | ----  | ----  |
 * | \Leevel\Database\Ddd\Entity::MANY_MANY  | 多对多关联实体 |  \Tests\Database\Ddd\Entity\Relation\Role::class  |
 * | \Leevel\Database\Ddd\Entity::MIDDLE_ENTITY  | 关联查询中间实体 |  \Tests\Database\Ddd\Entity\Relation\UserRole::class  |
 * | \Leevel\Database\Ddd\Entity::SOURCE_KEY  | 关联查询源键字段 | user_id |
 * | \Leevel\Database\Ddd\Entity::TARGET_KEY  | 关联目标键字段 | id |
 * | \Leevel\Database\Ddd\Entity::MIDDLE_SOURCE_KEY  | 关联查询中间实体源键字段 | id |
 * | \Leevel\Database\Ddd\Entity::MIDDLE_TARGET_KEY  | 关联查询中间实体目标键字段 | id |
 * | \Leevel\Database\Ddd\Entity::RELATION_SCOPE  | 关联查询作用域 | middleField |
 * ",
 * )
 */
class ManyManyTest extends TestCase
{
    /**
     * @api(
     *     title="基本使用方法",
     *     description="
     * **fixture 定义**
     *
     * **Tests\Database\Ddd\Entity\Relation\User**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Database\Ddd\Entity\Relation\User::class)]}
     * ```
     *
     * **Tests\Database\Ddd\Entity\Relation\UserRole**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Database\Ddd\Entity\Relation\UserRole::class)]}
     * ```
     *
     * **Tests\Database\Ddd\Entity\Relation\Role**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Database\Ddd\Entity\Relation\Role::class)]}
     * ```
     * ",
     *     note="",
     * )
     */
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

        $role1 = $role[0];

        $this->assertInstanceof(Role::class, $role1);
        $this->assertSame(1, $role1->id);
        $this->assertSame(1, $role1['id']);
        $this->assertSame(1, $role1->getId());
        $this->assertSame('管理员', $role1->name);
        $this->assertSame('管理员', $role1['name']);
        $this->assertSame('管理员', $role1->getName());

        $role2 = $role[1];

        $this->assertSame(3, $role2->id);
        $this->assertSame(3, $role2['id']);
        $this->assertSame(3, $role2->getId());
        $this->assertSame('会员', $role2->name);
        $this->assertSame('会员', $role2['name']);
        $this->assertSame('会员', $role2->getName());

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

    /**
     * @api(
     *     title="eager 预加载关联",
     *     description="",
     *     note="",
     * )
     */
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

        $role1 = $role[0];

        $this->assertInstanceof(Role::class, $role1);
        $this->assertSame(1, $role1->id);
        $this->assertSame(1, $role1['id']);
        $this->assertSame(1, $role1->getId());
        $this->assertSame('管理员', $role1->name);
        $this->assertSame('管理员', $role1['name']);
        $this->assertSame('管理员', $role1->getName());

        $role2 = $role[1];

        $this->assertInstanceof(Role::class, $role2);
        $this->assertSame(3, $role2->id);
        $this->assertSame(3, $role2['id']);
        $this->assertSame(3, $role2->getId());
        $this->assertSame('会员', $role2->name);
        $this->assertSame('会员', $role2['name']);
        $this->assertSame('会员', $role2->getName());

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

    /**
     * @api(
     *     title="eager 预加载关联支持查询条件过滤",
     *     description="",
     *     note="",
     * )
     */
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

    /**
     * @api(
     *     title="relation 读取关联",
     *     description="",
     *     note="",
     * )
     */
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

        $roleRelation = User::make()->relation('role');

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
        $role = $user->role;

        $this->assertInstanceof(Collection::class, $role);
        $this->assertCount(0, $role);
    }

    /**
     * @api(
     *     title="relation 关联模型数据不存在返回空集合",
     *     description="",
     *     note="",
     * )
     */
    public function testRelationDataWasNotFound(): void
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

    public function testEagerRelationWasNotFound(): void
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

    /**
     * @api(
     *     title="关联软删除",
     *     description="
     * **fixture 定义**
     *
     * **Tests\Database\Ddd\Entity\Relation\User**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Database\Ddd\Entity\Relation\User::class)]}
     * ```
     *
     * **Tests\Database\Ddd\Entity\Relation\UserRoleSoftDeleted**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Database\Ddd\Entity\Relation\UserRoleSoftDeleted::class)]}
     * ```
     *
     * **Tests\Database\Ddd\Entity\Relation\RoleSoftDeleted**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Database\Ddd\Entity\Relation\RoleSoftDeleted::class)]}
     * ```
     * ",
     *     note="",
     * )
     */
    public function testSoftDeleted(): void
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
                ->table('role_soft_deleted')
                ->insert([
                    'name' => '管理员',
                ]));

        $this->assertSame(
            2,
            $connect
                ->table('role_soft_deleted')
                ->insert([
                    'name' => '版主',
                ]));

        $this->assertSame(
            3,
            $connect
                ->table('role_soft_deleted')
                ->insert([
                    'name' => '会员',
                ]));

        $this->assertSame(
            1,
            $connect
                ->table('user_role_soft_deleted')
                ->insert([
                    'user_id' => 1,
                    'role_id' => 1,
                ]));

        $this->assertSame(
            2,
            $connect
                ->table('user_role_soft_deleted')
                ->insert([
                    'user_id' => 1,
                    'role_id' => 3,
                ]));

        $user = User::select()->where('id', 1)->findOne();

        $sql = <<<'eot'
            SQL: [64] SELECT `user`.* FROM `user` WHERE `user`.`id` = :user_id LIMIT 1 | Params:  1 | Key: Name: [8] :user_id | paramno=0 | name=[8] ":user_id" | is_param=1 | param_type=1 (SELECT `user`.* FROM `user` WHERE `user`.`id` = 1 LIMIT 1)
            eot;
        $this->assertSame(
            $sql,
            User::select()->getLastSql(),
        );

        $this->assertSame(1, $user->id);
        $this->assertSame(1, $user['id']);
        $this->assertSame(1, $user->getId());
        $this->assertSame('niu', $user->name);
        $this->assertSame('niu', $user['name']);
        $this->assertSame('niu', $user->getName());

        $role = $user->roleSoftDeleted;

        $sql = <<<'eot'
            SQL: [490] SELECT `role_soft_deleted`.*,`user_role_soft_deleted`.`role_id` AS `middle_role_id`,`user_role_soft_deleted`.`user_id` AS `middle_user_id` FROM `role_soft_deleted` INNER JOIN `user_role_soft_deleted` ON `user_role_soft_deleted`.`role_id` = `role_soft_deleted`.`id` AND `user_role_soft_deleted`.`delete_at` = :user_role_soft_deleted_delete_at WHERE `role_soft_deleted`.`delete_at` = :role_soft_deleted_delete_at AND `user_role_soft_deleted`.`user_id` IN (:user_role_soft_deleted_user_id_in0) | Params:  3 | Key: Name: [33] :user_role_soft_deleted_delete_at | paramno=0 | name=[33] ":user_role_soft_deleted_delete_at" | is_param=1 | param_type=1 | Key: Name: [28] :role_soft_deleted_delete_at | paramno=1 | name=[28] ":role_soft_deleted_delete_at" | is_param=1 | param_type=1 | Key: Name: [35] :user_role_soft_deleted_user_id_in0 | paramno=2 | name=[35] ":user_role_soft_deleted_user_id_in0" | is_param=1 | param_type=1 (SELECT `role_soft_deleted`.*,`user_role_soft_deleted`.`role_id` AS `middle_role_id`,`user_role_soft_deleted`.`user_id` AS `middle_user_id` FROM `role_soft_deleted` INNER JOIN `user_role_soft_deleted` ON `user_role_soft_deleted`.`role_id` = `role_soft_deleted`.`id` AND `user_role_soft_deleted`.`delete_at` = 0 WHERE `role_soft_deleted`.`delete_at` = 0 AND `user_role_soft_deleted`.`user_id` IN (1))
            eot;
        $this->assertSame(
            $sql,
            User::select()->getLastSql(),
        );

        $this->assertInstanceof(Collection::class, $role);

        $role1 = $role[0];

        $this->assertInstanceof(RoleSoftDeleted::class, $role1);
        $this->assertSame(1, $role1->id);
        $this->assertSame(1, $role1['id']);
        $this->assertSame(1, $role1->getId());
        $this->assertSame('管理员', $role1->name);
        $this->assertSame('管理员', $role1['name']);
        $this->assertSame('管理员', $role1->getName());

        $role2 = $role[1];

        $this->assertInstanceof(RoleSoftDeleted::class, $role2);
        $this->assertSame(3, $role2->id);
        $this->assertSame(3, $role2['id']);
        $this->assertSame(3, $role2->getId());
        $this->assertSame('会员', $role2->name);
        $this->assertSame('会员', $role2['name']);
        $this->assertSame('会员', $role2->getName());

        $this->assertCount(2, $role);
        $this->assertSame(1, $role[0]['id']);
        $this->assertSame('管理员', $role[0]['name']);
        $this->assertSame(3, $role[1]['id']);
        $this->assertSame('会员', $role[1]['name']);

        $middle = $role[0]->middle();
        $this->assertInstanceof(UserRoleSoftDeleted::class, $middle);
        $this->assertSame(1, $middle->userId);
        $this->assertSame(1, $middle->roleId);

        $middle = $role[1]->middle();
        $this->assertInstanceof(UserRoleSoftDeleted::class, $middle);
        $this->assertSame(1, $middle->userId);
        $this->assertSame(3, $middle->roleId);
    }

    public function testSoftDeletedAndMiddleEntityHasSoftDeleted(): void
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
                ->table('role_soft_deleted')
                ->insert([
                    'name' => '管理员',
                ]));

        $this->assertSame(
            2,
            $connect
                ->table('role_soft_deleted')
                ->insert([
                    'name' => '版主',
                ]));

        $this->assertSame(
            3,
            $connect
                ->table('role_soft_deleted')
                ->insert([
                    'name' => '会员',
                ]));

        $this->assertSame(
            1,
            $connect
                ->table('user_role_soft_deleted')
                ->insert([
                    'user_id' => 1,
                    'role_id' => 1,
                ]));

        $this->assertSame(
            2,
            $connect
                ->table('user_role_soft_deleted')
                ->insert([
                    'user_id'   => 1,
                    'role_id'   => 3,
                    'delete_at' => time(),
                ]));

        $user = User::select()->where('id', 1)->findOne();

        $sql = <<<'eot'
            SQL: [64] SELECT `user`.* FROM `user` WHERE `user`.`id` = :user_id LIMIT 1 | Params:  1 | Key: Name: [8] :user_id | paramno=0 | name=[8] ":user_id" | is_param=1 | param_type=1 (SELECT `user`.* FROM `user` WHERE `user`.`id` = 1 LIMIT 1)
            eot;
        $this->assertSame(
            $sql,
            User::select()->getLastSql(),
        );

        $this->assertSame(1, $user->id);
        $this->assertSame(1, $user['id']);
        $this->assertSame(1, $user->getId());
        $this->assertSame('niu', $user->name);
        $this->assertSame('niu', $user['name']);
        $this->assertSame('niu', $user->getName());

        $role = $user->roleSoftDeleted;

        $sql = <<<'eot'
            SQL: [490] SELECT `role_soft_deleted`.*,`user_role_soft_deleted`.`role_id` AS `middle_role_id`,`user_role_soft_deleted`.`user_id` AS `middle_user_id` FROM `role_soft_deleted` INNER JOIN `user_role_soft_deleted` ON `user_role_soft_deleted`.`role_id` = `role_soft_deleted`.`id` AND `user_role_soft_deleted`.`delete_at` = :user_role_soft_deleted_delete_at WHERE `role_soft_deleted`.`delete_at` = :role_soft_deleted_delete_at AND `user_role_soft_deleted`.`user_id` IN (:user_role_soft_deleted_user_id_in0) | Params:  3 | Key: Name: [33] :user_role_soft_deleted_delete_at | paramno=0 | name=[33] ":user_role_soft_deleted_delete_at" | is_param=1 | param_type=1 | Key: Name: [28] :role_soft_deleted_delete_at | paramno=1 | name=[28] ":role_soft_deleted_delete_at" | is_param=1 | param_type=1 | Key: Name: [35] :user_role_soft_deleted_user_id_in0 | paramno=2 | name=[35] ":user_role_soft_deleted_user_id_in0" | is_param=1 | param_type=1 (SELECT `role_soft_deleted`.*,`user_role_soft_deleted`.`role_id` AS `middle_role_id`,`user_role_soft_deleted`.`user_id` AS `middle_user_id` FROM `role_soft_deleted` INNER JOIN `user_role_soft_deleted` ON `user_role_soft_deleted`.`role_id` = `role_soft_deleted`.`id` AND `user_role_soft_deleted`.`delete_at` = 0 WHERE `role_soft_deleted`.`delete_at` = 0 AND `user_role_soft_deleted`.`user_id` IN (1))
            eot;
        $this->assertSame(
            $sql,
            User::select()->getLastSql(),
        );

        $this->assertInstanceof(Collection::class, $role);

        $role1 = $role[0];

        $this->assertInstanceof(RoleSoftDeleted::class, $role1);
        $this->assertSame(1, $role1->id);
        $this->assertSame(1, $role1['id']);
        $this->assertSame(1, $role1->getId());
        $this->assertSame('管理员', $role1->name);
        $this->assertSame('管理员', $role1['name']);
        $this->assertSame('管理员', $role1->getName());

        $role2 = $role[1] ?? null;
        $this->assertNull($role2);

        $this->assertCount(1, $role);
        $this->assertSame(1, $role[0]['id']);
        $this->assertSame('管理员', $role[0]['name']);

        $middle = $role[0]->middle();
        $this->assertInstanceof(UserRoleSoftDeleted::class, $middle);
        $this->assertSame(1, $middle->userId);
        $this->assertSame(1, $middle->roleId);
    }

    /**
     * @api(
     *     title="middleWithSoftDeleted 中间实体包含软删除数据的数据库查询集合对象",
     *     description="
     * 通过关联作用域来设置中间实体包含软删除数据的数据库查询集合对象。
     *
     * **fixture 定义**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getMethodBody(\Tests\Database\Ddd\Entity\Relation\User::class, 'relationScopeWithSoftDeleted')]}
     * ```
     * ",
     *     note="",
     * )
     */
    public function testWithMiddleSoftDeletedAndMiddleEntityHasSoftDeleted(): void
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
                ->table('role_soft_deleted')
                ->insert([
                    'name' => '管理员',
                ]));

        $this->assertSame(
            2,
            $connect
                ->table('role_soft_deleted')
                ->insert([
                    'name' => '版主',
                ]));

        $this->assertSame(
            3,
            $connect
                ->table('role_soft_deleted')
                ->insert([
                    'name' => '会员',
                ]));

        $this->assertSame(
            1,
            $connect
                ->table('user_role_soft_deleted')
                ->insert([
                    'user_id' => 1,
                    'role_id' => 1,
                ]));

        $this->assertSame(
            2,
            $connect
                ->table('user_role_soft_deleted')
                ->insert([
                    'user_id'   => 1,
                    'role_id'   => 3,
                    'delete_at' => time(),
                ]));

        $user = User::select()->where('id', 1)->findOne();

        $sql = <<<'eot'
            SQL: [64] SELECT `user`.* FROM `user` WHERE `user`.`id` = :user_id LIMIT 1 | Params:  1 | Key: Name: [8] :user_id | paramno=0 | name=[8] ":user_id" | is_param=1 | param_type=1 (SELECT `user`.* FROM `user` WHERE `user`.`id` = 1 LIMIT 1)
            eot;
        $this->assertSame(
            $sql,
            User::select()->getLastSql(),
        );

        $this->assertSame(1, $user->id);
        $this->assertSame(1, $user['id']);
        $this->assertSame(1, $user->getId());
        $this->assertSame('niu', $user->name);
        $this->assertSame('niu', $user['name']);
        $this->assertSame('niu', $user->getName());

        $role = $user->roleMiddleWithSoftDeleted;

        $sql = <<<'eot'
            SQL: [413] SELECT `role_soft_deleted`.*,`user_role_soft_deleted`.`role_id` AS `middle_role_id`,`user_role_soft_deleted`.`user_id` AS `middle_user_id` FROM `role_soft_deleted` INNER JOIN `user_role_soft_deleted` ON `user_role_soft_deleted`.`role_id` = `role_soft_deleted`.`id` WHERE `role_soft_deleted`.`delete_at` = :role_soft_deleted_delete_at AND `user_role_soft_deleted`.`user_id` IN (:user_role_soft_deleted_user_id_in0) | Params:  2 | Key: Name: [28] :role_soft_deleted_delete_at | paramno=0 | name=[28] ":role_soft_deleted_delete_at" | is_param=1 | param_type=1 | Key: Name: [35] :user_role_soft_deleted_user_id_in0 | paramno=1 | name=[35] ":user_role_soft_deleted_user_id_in0" | is_param=1 | param_type=1 (SELECT `role_soft_deleted`.*,`user_role_soft_deleted`.`role_id` AS `middle_role_id`,`user_role_soft_deleted`.`user_id` AS `middle_user_id` FROM `role_soft_deleted` INNER JOIN `user_role_soft_deleted` ON `user_role_soft_deleted`.`role_id` = `role_soft_deleted`.`id` WHERE `role_soft_deleted`.`delete_at` = 0 AND `user_role_soft_deleted`.`user_id` IN (1))
            eot;
        $this->assertSame(
            $sql,
            User::select()->getLastSql(),
        );

        $this->assertInstanceof(Collection::class, $role);

        $role1 = $role[0];

        $this->assertInstanceof(RoleSoftDeleted::class, $role1);
        $this->assertSame(1, $role1->id);
        $this->assertSame(1, $role1['id']);
        $this->assertSame(1, $role1->getId());
        $this->assertSame('管理员', $role1->name);
        $this->assertSame('管理员', $role1['name']);
        $this->assertSame('管理员', $role1->getName());

        $role2 = $role[1];

        $this->assertInstanceof(RoleSoftDeleted::class, $role2);
        $this->assertSame(3, $role2->id);
        $this->assertSame(3, $role2['id']);
        $this->assertSame(3, $role2->getId());
        $this->assertSame('会员', $role2->name);
        $this->assertSame('会员', $role2['name']);
        $this->assertSame('会员', $role2->getName());

        $this->assertCount(2, $role);
        $this->assertSame(1, $role[0]['id']);
        $this->assertSame('管理员', $role[0]['name']);
        $this->assertSame(3, $role[1]['id']);
        $this->assertSame('会员', $role[1]['name']);

        $middle = $role[0]->middle();
        $this->assertInstanceof(UserRoleSoftDeleted::class, $middle);
        $this->assertSame(1, $middle->userId);
        $this->assertSame(1, $middle->roleId);

        $middle = $role[1]->middle();
        $this->assertInstanceof(UserRoleSoftDeleted::class, $middle);
        $this->assertSame(1, $middle->userId);
        $this->assertSame(3, $middle->roleId);
    }

    /**
     * @api(
     *     title="middleOnlySoftDeleted 中间实体仅仅包含软删除数据的数据库查询集合对象",
     *     description="
     * 通过关联作用域来设置中间实体仅仅包含软删除数据的数据库查询集合对象。
     *
     * **fixture 定义**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getMethodBody(\Tests\Database\Ddd\Entity\Relation\User::class, 'relationScopeOnlySoftDeleted')]}
     * ```
     * ",
     *     note="",
     * )
     */
    public function testOnlyMiddleSoftDeletedAndMiddleEntityHasSoftDeleted(): void
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
                ->table('role_soft_deleted')
                ->insert([
                    'name' => '管理员',
                ]));

        $this->assertSame(
            2,
            $connect
                ->table('role_soft_deleted')
                ->insert([
                    'name' => '版主',
                ]));

        $this->assertSame(
            3,
            $connect
                ->table('role_soft_deleted')
                ->insert([
                    'name' => '会员',
                ]));

        $this->assertSame(
            1,
            $connect
                ->table('user_role_soft_deleted')
                ->insert([
                    'user_id' => 1,
                    'role_id' => 1,
                ]));

        $this->assertSame(
            2,
            $connect
                ->table('user_role_soft_deleted')
                ->insert([
                    'user_id'   => 1,
                    'role_id'   => 3,
                    'delete_at' => time(),
                ]));

        $user = User::select()->where('id', 1)->findOne();

        $sql = <<<'eot'
            SQL: [64] SELECT `user`.* FROM `user` WHERE `user`.`id` = :user_id LIMIT 1 | Params:  1 | Key: Name: [8] :user_id | paramno=0 | name=[8] ":user_id" | is_param=1 | param_type=1 (SELECT `user`.* FROM `user` WHERE `user`.`id` = 1 LIMIT 1)
            eot;
        $this->assertSame(
            $sql,
            User::select()->getLastSql(),
        );

        $this->assertSame(1, $user->id);
        $this->assertSame(1, $user['id']);
        $this->assertSame(1, $user->getId());
        $this->assertSame('niu', $user->name);
        $this->assertSame('niu', $user['name']);
        $this->assertSame('niu', $user->getName());

        $role = $user->roleMiddleOnlySoftDeleted;

        $sql = <<<'eot'
            SQL: [490] SELECT `role_soft_deleted`.*,`user_role_soft_deleted`.`role_id` AS `middle_role_id`,`user_role_soft_deleted`.`user_id` AS `middle_user_id` FROM `role_soft_deleted` INNER JOIN `user_role_soft_deleted` ON `user_role_soft_deleted`.`role_id` = `role_soft_deleted`.`id` AND `user_role_soft_deleted`.`delete_at` > :user_role_soft_deleted_delete_at WHERE `role_soft_deleted`.`delete_at` = :role_soft_deleted_delete_at AND `user_role_soft_deleted`.`user_id` IN (:user_role_soft_deleted_user_id_in0) | Params:  3 | Key: Name: [33] :user_role_soft_deleted_delete_at | paramno=0 | name=[33] ":user_role_soft_deleted_delete_at" | is_param=1 | param_type=1 | Key: Name: [28] :role_soft_deleted_delete_at | paramno=1 | name=[28] ":role_soft_deleted_delete_at" | is_param=1 | param_type=1 | Key: Name: [35] :user_role_soft_deleted_user_id_in0 | paramno=2 | name=[35] ":user_role_soft_deleted_user_id_in0" | is_param=1 | param_type=1 (SELECT `role_soft_deleted`.*,`user_role_soft_deleted`.`role_id` AS `middle_role_id`,`user_role_soft_deleted`.`user_id` AS `middle_user_id` FROM `role_soft_deleted` INNER JOIN `user_role_soft_deleted` ON `user_role_soft_deleted`.`role_id` = `role_soft_deleted`.`id` AND `user_role_soft_deleted`.`delete_at` > 0 WHERE `role_soft_deleted`.`delete_at` = 0 AND `user_role_soft_deleted`.`user_id` IN (1))
            eot;
        $this->assertSame(
            $sql,
            User::select()->getLastSql(),
        );

        $this->assertInstanceof(Collection::class, $role);

        $role1 = $role[0];

        $this->assertInstanceof(RoleSoftDeleted::class, $role1);
        $this->assertSame(3, $role1->id);
        $this->assertSame(3, $role1['id']);
        $this->assertSame(3, $role1->getId());
        $this->assertSame('会员', $role1->name);
        $this->assertSame('会员', $role1['name']);
        $this->assertSame('会员', $role1->getName());

        $role2 = $role[1];

        $this->assertNull($role2);

        $this->assertCount(1, $role);
        $this->assertSame(3, $role[0]['id']);
        $this->assertSame('会员', $role[0]['name']);
        $this->assertNull($role[1]['id'] ?? null);
        $this->assertNull($role[1]['name'] ?? null);

        $middle = $role[0]->middle();
        $this->assertInstanceof(UserRoleSoftDeleted::class, $middle);
        $this->assertSame(1, $middle->userId);
        $this->assertSame(3, $middle->roleId);
    }

    public function testRelationScopeIsNotFound(): void
    {
        $this->expectException(\BadMethodCallException::class);
        $this->expectExceptionMessage(
            'Relation scope `relationScopeNotFound` of entity `Tests\\Database\\Ddd\\Entity\\Relation\\User` is not exits.'
        );

        $user = User::select()->where('id', 1)->findOne();
        $this->assertInstanceof(User::class, $user);
        $this->assertNull($user->id);

        $user->roleRelationScopeNotFound;
    }

    public function testRelationScopeFoundButIsPrivate(): void
    {
        $this->expectException(\BadMethodCallException::class);
        $this->expectExceptionMessage(
            'Method `relationScopeFoundButPrivate` is not exits,maybe you can try `Tests\\Database\\Ddd\\Entity\\Relation\\User::select|make()->relationScopeFoundButPrivate(...)`.'
        );

        $user = User::select()->where('id', 1)->findOne();
        $this->assertInstanceof(User::class, $user);
        $this->assertNull($user->id);

        $user->roleRelationScopeFoundButPrivate;
    }

    /**
     * @api(
     *     title="middleField 中间实体查询字段",
     *     description="
     * 通过关联作用域来设置中间实体查询字段。
     *
     * **fixture 定义**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getMethodBody(\Tests\Database\Ddd\Entity\Relation\User::class, 'relationScopeMiddleField')]}
     * ```
     * ",
     *     note="",
     * )
     */
    public function testMiddleField(): void
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
            1,
            $connect
                ->table('user_role')
                ->insert([
                    'user_id' => 1,
                    'role_id' => 2,
                ]));

        $user = User::select()->where('id', 1)->findOne();

        $sql = <<<'eot'
            SQL: [64] SELECT `user`.* FROM `user` WHERE `user`.`id` = :user_id LIMIT 1 | Params:  1 | Key: Name: [8] :user_id | paramno=0 | name=[8] ":user_id" | is_param=1 | param_type=1 (SELECT `user`.* FROM `user` WHERE `user`.`id` = 1 LIMIT 1)
            eot;
        $this->assertSame(
            $sql,
            User::select()->getLastSql(),
        );

        $this->assertSame(1, $user->id);
        $this->assertSame(1, $user['id']);
        $this->assertSame(1, $user->getId());
        $this->assertSame('niu', $user->name);
        $this->assertSame('niu', $user['name']);
        $this->assertSame('niu', $user->getName());

        $role = $user->roleMiddleField;

        $sql = <<<'eot'
            SQL: [285] SELECT `role`.*,`user_role`.`create_at`,`user_role`.`id` AS `middle_id`,`user_role`.`role_id` AS `middle_role_id`,`user_role`.`user_id` AS `middle_user_id` FROM `role` INNER JOIN `user_role` ON `user_role`.`role_id` = `role`.`id` WHERE `user_role`.`user_id` IN (:user_role_user_id_in0) | Params:  1 | Key: Name: [22] :user_role_user_id_in0 | paramno=0 | name=[22] ":user_role_user_id_in0" | is_param=1 | param_type=1 (SELECT `role`.*,`user_role`.`create_at`,`user_role`.`id` AS `middle_id`,`user_role`.`role_id` AS `middle_role_id`,`user_role`.`user_id` AS `middle_user_id` FROM `role` INNER JOIN `user_role` ON `user_role`.`role_id` = `role`.`id` WHERE `user_role`.`user_id` IN (1))
            eot;
        $this->assertSame(
            $sql,
            User::select()->getLastSql(),
        );

        $this->assertInstanceof(Collection::class, $role);
        $this->assertCount(1, $role);

        $role1 = $role[0];

        $this->assertInstanceof(Role::class, $role1);
        $this->assertSame(2, $role1->id);
        $this->assertSame(2, $role1['id']);
        $this->assertSame(2, $role1->getId());
        $this->assertSame('版主', $role1->name);
        $this->assertSame('版主', $role1['name']);
        $this->assertSame('版主', $role1->getName());

        $middle = $role[0]->middle();
        $this->assertInstanceof(UserRole::class, $middle);
        $this->assertSame(1, $middle->userId);
        $this->assertSame(2, $middle->roleId);
    }

    protected function getDatabaseTable(): array
    {
        return ['user', 'user_role', 'role', 'user_role_soft_deleted', 'role_soft_deleted'];
    }
}
