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

namespace Tests\Database\Ddd;

use Leevel\Database\Ddd\Entity;
use Leevel\Database\Ddd\IEntity;
use Tests\Database\DatabaseTestCase as TestCase;
use Tests\Database\Ddd\Entity\TestEntity;

/**
 * define entity test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.06.29
 *
 * @version 1.0
 */
class DefineEntityTest extends TestCase
{
    public function testBaseUse()
    {
        $entity = new TestEntity();

        $this->assertInstanceof(Entity::class, $entity);

        $this->assertSame(TestEntity::STRUCT, $entity->fields());
        $this->assertSame(TestEntity::TABLE, $entity->table());
        $this->assertSame((array) TestEntity::ID, $entity->primaryKeys());
        $this->assertSame(TestEntity::AUTO, $entity->autoIncrement());
    }

    public function testConstDefined()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The entity const TABLE was not defined.');

        $entity = new Test1Entity();
    }

    public function testConstDefined2()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The entity const ID was not defined.');

        $entity = new Test2Entity();
    }

    public function testConstDefined3()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The entity const AUTO was not defined.');

        $entity = new Test3Entity();
    }

    public function testConstDefined4()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The entity const STRUCT was not defined.');

        $entity = new Test4Entity();
    }
}

class Test1Entity extends Entity
{
    public function setter(string $prop, $value): IEntity
    {
        $this->{$this->prop($prop)} = $value;

        return $this;
    }

    public function getter(string $prop)
    {
        return $this->{$this->prop($prop)};
    }
}

class Test2Entity extends Entity
{
    const TABLE = 'test2';

    public function setter(string $prop, $value): IEntity
    {
        $this->{$this->prop($prop)} = $value;

        return $this;
    }

    public function getter(string $prop)
    {
        return $this->{$this->prop($prop)};
    }
}

class Test3Entity extends Entity
{
    const TABLE = 'test2';

    const ID = [
        'id',
    ];

    public function setter(string $prop, $value): IEntity
    {
        $this->{$this->prop($prop)} = $value;

        return $this;
    }

    public function getter(string $prop)
    {
        return $this->{$this->prop($prop)};
    }
}

class Test4Entity extends Entity
{
    const TABLE = 'test2';

    const ID = [
        'id',
    ];

    const AUTO = 'id';

    public function setter(string $prop, $value): IEntity
    {
        $this->{$this->prop($prop)} = $value;

        return $this;
    }

    public function getter(string $prop)
    {
        return $this->{$this->prop($prop)};
    }
}
