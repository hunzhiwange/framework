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

namespace Tests\Database\Ddd;

use Leevel\Database\Ddd\Entity;
use Tests\Database\DatabaseTestCase as TestCase;
use Tests\Database\Ddd\Entity\DemoEntity;

class EntityDefineTest extends TestCase
{
    public function testBaseUse(): void
    {
        $entity = new DemoEntity();

        $this->assertInstanceof(Entity::class, $entity);

        $this->assertSame(DemoEntity::STRUCT, $entity->fields());
        $this->assertSame(DemoEntity::TABLE, $entity->table());
        $this->assertSame((array) DemoEntity::ID, $entity->primaryKeys());
        $this->assertSame(DemoEntity::AUTO, $entity->autoIncrement());
    }

    public function testConstDefined(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The entity const TABLE was not defined.');

        $entity = new Test1Entity();
    }

    public function testConstDefined2(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The entity const ID was not defined.');

        $entity = new Test2Entity();
    }

    public function testConstDefined3(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The entity const AUTO was not defined.');

        $entity = new Test3Entity();
    }

    public function testConstDefined4(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The entity const STRUCT was not defined.');

        $entity = new Test4Entity();
    }
}

class Test1Entity extends Entity
{
    private static $leevelConnect;

    public function setter(string $prop, $value): self
    {
        $this->{$this->realProp($prop)} = $value;

        return $this;
    }

    public function getter(string $prop)
    {
        return $this->{$this->realProp($prop)};
    }

    public static function withConnect(?string $connect = null): void
    {
        static::$leevelConnect = $connect;
    }

    public static function connect(): ?string
    {
        return static::$leevelConnect;
    }
}

class Test2Entity extends Entity
{
    const TABLE = 'test2';

    private static $leevelConnect;

    public function setter(string $prop, $value): self
    {
        $this->{$this->realProp($prop)} = $value;

        return $this;
    }

    public function getter(string $prop)
    {
        return $this->{$this->realProp($prop)};
    }

    public static function withConnect(?string $connect = null): void
    {
        static::$leevelConnect = $connect;
    }

    public static function connect(): ?string
    {
        return static::$leevelConnect;
    }
}

class Test3Entity extends Entity
{
    const TABLE = 'test2';

    const ID = [
        'id',
    ];

    private static $leevelConnect;

    public function setter(string $prop, $value): self
    {
        $this->{$this->realProp($prop)} = $value;

        return $this;
    }

    public function getter(string $prop)
    {
        return $this->{$this->realProp($prop)};
    }

    public static function withConnect(?string $connect = null): void
    {
        static::$leevelConnect = $connect;
    }

    public static function connect(): ?string
    {
        return static::$leevelConnect;
    }
}

class Test4Entity extends Entity
{
    const TABLE = 'test2';

    const ID = [
        'id',
    ];

    const AUTO = 'id';

    private static $leevelConnect;

    public function setter(string $prop, $value): self
    {
        $this->{$this->realProp($prop)} = $value;

        return $this;
    }

    public function getter(string $prop)
    {
        return $this->{$this->realProp($prop)};
    }

    public static function withConnect(?string $connect = null): void
    {
        static::$leevelConnect = $connect;
    }

    public static function connect(): ?string
    {
        return static::$leevelConnect;
    }
}
