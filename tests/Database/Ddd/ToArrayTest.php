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

namespace Tests\Database\Ddd;

use Leevel\Database\Ddd\Entity;
use Tests\Database\Ddd\Entity\TestToArrayBlackEntity;
use Tests\Database\Ddd\Entity\TestToArrayEntity;
use Tests\Database\Ddd\Entity\TestToArrayWhiteEntity;
use Tests\TestCase;

/**
 * toArray test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.07.01
 *
 * @version 1.0
 */
class ToArrayTest extends TestCase
{
    public function testBaseUse()
    {
        $entity = $this->makeEntity();

        $data = <<<'eot'
{
    "name": "实体名字",
    "description": "goods name",
    "address": "四川成都",
    "foo_bar": "foo",
    "hello": "hello world"
}
eot;

        $this->assertSame(
            $data,
            $this->varJson(
                $entity->toArray()
            )
        );
    }

    public function testWithWhite()
    {
        $entity = $this->makeEntity();

        $data = <<<'eot'
{
    "name": "实体名字",
    "description": "goods name",
    "address": "四川成都",
    "foo_bar": "foo",
    "hello": "hello world"
}
eot;

        $this->assertSame(
            $data,
            $this->varJson(
                $entity->toArray()
            )
        );

        $data = <<<'eot'
{
    "name": "实体名字"
}
eot;

        $this->assertSame(
            $data,
            $this->varJson(
                $entity->toArray(['name']),
                1
            )
        );

        $data = <<<'eot'
{
    "name": "实体名字",
    "description": "goods name"
}
eot;

        $this->assertSame(
            $data,
            $this->varJson(
                $entity->toArray(['name', 'description']),
                2
            )
        );

        $data = <<<'eot'
{
    "name": "实体名字",
    "description": "goods name",
    "hello": "hello world"
}
eot;

        $this->assertSame(
            $data,
            $this->varJson(
                $entity->toArray(['name', 'description', 'hello']),
                3
            )
        );
    }

    public function testWithBlack()
    {
        $entity = $this->makeEntity();

        $data = <<<'eot'
{
    "name": "实体名字",
    "description": "goods name",
    "address": "四川成都",
    "foo_bar": "foo",
    "hello": "hello world"
}
eot;

        $this->assertSame(
            $data,
            $this->varJson(
                $entity->toArray()
            )
        );

        $data = <<<'eot'
{
    "description": "goods name",
    "address": "四川成都",
    "foo_bar": "foo",
    "hello": "hello world"
}
eot;

        $this->assertSame(
            $data,
            $this->varJson(
                $entity->toArray([], ['name']),
                1
            )
        );

        $data = <<<'eot'
{
    "address": "四川成都",
    "foo_bar": "foo",
    "hello": "hello world"
}
eot;

        $this->assertSame(
            $data,
            $this->varJson(
                $entity->toArray([], ['name', 'description']),
                2
            )
        );

        $data = <<<'eot'
{
    "description": "goods name",
    "hello": "hello world"
}
eot;

        $this->assertSame(
            $data,
            $this->varJson(
                $entity->toArray([], ['foo_bar', 'name', 'address']),
                3
            )
        );
    }

    public function testWithWhiteAndBlack()
    {
        $entity = $this->makeEntity();

        $data = <<<'eot'
{
    "name": "实体名字",
    "description": "goods name",
    "address": "四川成都",
    "foo_bar": "foo",
    "hello": "hello world"
}
eot;

        $this->assertSame(
            $data,
            $this->varJson(
                $entity->toArray()
            )
        );

        $data = <<<'eot'
{
    "hello": "hello world"
}
eot;

        $this->assertSame(
            $data,
            $this->varJson(
                $entity->toArray(['hello'], ['description']),
                1
            )
        );
    }

    public function testWithWhiteEntity()
    {
        $entity = $this->makeWhiteEntity();

        $data = <<<'eot'
{
    "description": "goods name",
    "foo_bar": "foo"
}
eot;

        $this->assertSame(
            $data,
            $this->varJson(
                $entity->toArray()
            )
        );
    }

    public function testWithBlackEntity()
    {
        $entity = $this->makeBlackEntity();

        $data = <<<'eot'
{
    "name": "实体名字",
    "address": "四川成都",
    "hello": "hello world"
}
eot;

        $this->assertSame(
            $data,
            $this->varJson(
                $entity->toArray()
            )
        );
    }

    protected function makeWhiteEntity()
    {
        $entity = new TestToArrayWhiteEntity();

        $this->assertInstanceof(Entity::class, $entity);

        $entity->name = '实体名字';

        $entity->description = 'goods name';

        $entity->address = '四川成都';

        $entity->foo_bar = 'foo';

        $entity->hello = 'hello world';

        return $entity;
    }

    protected function makeBlackEntity()
    {
        $entity = new TestToArrayBlackEntity();

        $this->assertInstanceof(Entity::class, $entity);

        $entity->name = '实体名字';

        $entity->description = 'goods name';

        $entity->address = '四川成都';

        $entity->foo_bar = 'foo';

        $entity->hello = 'hello world';

        return $entity;
    }

    protected function makeEntity()
    {
        $entity = new TestToArrayEntity();

        $this->assertInstanceof(Entity::class, $entity);

        $entity->name = '实体名字';

        $entity->description = 'goods name';

        $entity->address = '四川成都';

        $entity->foo_bar = 'foo';

        $entity->hello = 'hello world';

        return $entity;
    }
}
