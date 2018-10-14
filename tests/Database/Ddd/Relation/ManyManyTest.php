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
 * (c) 2010-2018 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Database\Ddd\Relation;

use Leevel\Collection\Collection;
use Leevel\Database\Ddd\Meta;
use Tests\Database\Ddd\Entity\Relation\Role;
use Tests\Database\Ddd\Entity\Relation\User;
use Tests\Database\Query\Query;
use Tests\TestCase;

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
    use Query;

    protected function setUp()
    {
        $this->truncate('user');
        $this->truncate('user_role');
        $this->truncate('role');

        Meta::setDatabaseManager($this->createManager());
    }

    protected function tearDown()
    {
        $this->truncate('user');
        $this->truncate('user_role');
        $this->truncate('role');

        Meta::setDatabaseManager(null);
    }

    public function testBaseUse()
    {
        $user = User::where('id', 1)->findOne();

        $this->assertInstanceof(User::class, $user);
        $this->assertNull($user->id);

        $connect = $this->createConnectTest();

        $this->assertSame('1', $connect->
        table('user')->
        insert([
            'name' => 'niu',
        ]));

        $this->assertSame('1', $connect->
        table('role')->
        insert([
            'name' => '管理员',
        ]));

        $this->assertSame('2', $connect->
        table('role')->
        insert([
            'name' => '版主',
        ]));

        $this->assertSame('3', $connect->
        table('role')->
        insert([
            'name' => '会员',
        ]));

        $this->assertSame('1', $connect->
        table('user_role')->
        insert([
            'user_id' => 1,
            'role_id' => 1,
        ]));

        $this->assertSame('2', $connect->
        table('user_role')->
        insert([
            'user_id' => 1,
            'role_id' => 3,
        ]));

        $user = User::where('id', 1)->findOne();

        $this->assertSame('1', $user->id);
        $this->assertSame('1', $user['id']);
        $this->assertSame('1', $user->getId());
        $this->assertSame('niu', $user->name);
        $this->assertSame('niu', $user['name']);
        $this->assertSame('niu', $user->getName());

        $role = $user->role;

        $this->assertInstanceof(Collection::class, $role);

        $user1 = $role[0];

        $this->assertSame('1', $user1->id);
        $this->assertSame('1', $user1['id']);
        $this->assertSame('1', $user1->getId());
        $this->assertSame('管理员', $user1->name);
        $this->assertSame('管理员', $user1['name']);
        $this->assertSame('管理员', $user1->getName());

        $user2 = $role[1];

        $this->assertSame('3', $user2->id);
        $this->assertSame('3', $user2['id']);
        $this->assertSame('3', $user2->getId());
        $this->assertSame('会员', $user2->name);
        $this->assertSame('会员', $user2['name']);
        $this->assertSame('会员', $user2->getName());

        $this->assertSame(2, count($role));

        $this->truncate('user');
        $this->truncate('user_role');
        $this->truncate('role');
    }
}
