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
use Tests\Database\Ddd\Entity\TestToArrayEntity;
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

    public function testWithAppend()
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

        $entity->append('append1');

        $this->assertSame(
            ['append1'],
            $entity->getAppend()
        );

        $data = <<<'eot'
{
    "name": "实体名字",
    "description": "goods name",
    "address": "四川成都",
    "foo_bar": "foo",
    "hello": "hello world",
    "append1": "append 1"
}
eot;

        $this->assertSame(
            $data,
            $this->varJson(
                $entity->toArray(),
                1
            )
        );

        // 第二种方式
        $entity->append('append1', 'append2');

        $this->assertSame(
            ['append1', 'append2'],
            $entity->getAppend()
        );

        $data = <<<'eot'
{
    "name": "实体名字",
    "description": "goods name",
    "address": "四川成都",
    "foo_bar": "foo",
    "hello": "hello world",
    "append1": "append 1",
    "append2": "append 2"
}
eot;

        $this->assertSame(
            $data,
            $this->varJson(
                $entity->toArray(),
                2
            )
        );

        // 数组方式
        $entity->append(['append1', 'append2']);

        $this->assertSame(
            ['append1', 'append2'],
            $entity->getAppend()
        );

        $this->assertSame(
            $data,
            $this->varJson(
                $entity->toArray(),
                3
            )
        );
    }

    public function testWithAddAppend()
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

        $entity->addAppend('append1');

        $data = <<<'eot'
{
    "name": "实体名字",
    "description": "goods name",
    "address": "四川成都",
    "foo_bar": "foo",
    "hello": "hello world",
    "append1": "append 1"
}
eot;

        $this->assertSame(
            $data,
            $this->varJson(
                $entity->toArray(),
                1
            )
        );

        $entity->addAppend(['append2']);

        $data = <<<'eot'
{
    "name": "实体名字",
    "description": "goods name",
    "address": "四川成都",
    "foo_bar": "foo",
    "hello": "hello world",
    "append1": "append 1",
    "append2": "append 2"
}
eot;

        $this->assertSame(
            $data,
            $this->varJson(
                $entity->toArray(),
                2
            )
        );

        $entity = $this->makeEntity();

        $entity->addAppend('append1', 'append2');

        $data = <<<'eot'
{
    "name": "实体名字",
    "description": "goods name",
    "address": "四川成都",
    "foo_bar": "foo",
    "hello": "hello world",
    "append1": "append 1",
    "append2": "append 2"
}
eot;

        $this->assertSame(
            $data,
            $this->varJson(
                $entity->toArray(),
                3
            )
        );
    }

    public function testWithRemoveAppend()
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

        $entity->addAppend(['append2', 'append1']);

        $data = <<<'eot'
{
    "name": "实体名字",
    "description": "goods name",
    "address": "四川成都",
    "foo_bar": "foo",
    "hello": "hello world",
    "append2": "append 2",
    "append1": "append 1"
}
eot;

        $this->assertSame(
            $data,
            $this->varJson(
                $entity->toArray(),
                1
            )
        );

        $entity->removeAppend('append2');

        $data = <<<'eot'
{
    "name": "实体名字",
    "description": "goods name",
    "address": "四川成都",
    "foo_bar": "foo",
    "hello": "hello world",
    "append1": "append 1"
}
eot;

        $this->assertSame(
            $data,
            $this->varJson(
                $entity->toArray(),
                2
            )
        );

        $entity->removeAppend('append1');

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
                $entity->toArray(),
                3
            )
        );

        $entity->addAppend(['append2', 'append1']);

        $data = <<<'eot'
{
    "name": "实体名字",
    "description": "goods name",
    "address": "四川成都",
    "foo_bar": "foo",
    "hello": "hello world",
    "append2": "append 2",
    "append1": "append 1"
}
eot;

        $this->assertSame(
            $data,
            $this->varJson(
                $entity->toArray(),
                4
            )
        );

        $entity->removeAppend(['append1', 'append2']);

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
                $entity->toArray(),
                5
            )
        );
    }

    public function testWithVisible()
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

        $entity->visible('name');

        $this->assertSame(
            ['name'],
            $entity->getVisible()
        );

        $data = <<<'eot'
{
    "name": "实体名字"
}
eot;

        $this->assertSame(
            $data,
            $this->varJson(
                $entity->toArray(),
                1
            )
        );

        // 第二种方式
        $entity->visible('description', 'address');

        $this->assertSame(
            ['description', 'address'],
            $entity->getVisible()
        );

        $data = <<<'eot'
{
    "description": "goods name",
    "address": "四川成都"
}
eot;

        $this->assertSame(
            $data,
            $this->varJson(
                $entity->toArray(),
                2
            )
        );

        // 数组方式
        $entity->visible(['foo_bar', 'name']);

        $this->assertSame(
            ['foo_bar', 'name'],
            $entity->getVisible()
        );

        $data = <<<'eot'
{
    "name": "实体名字",
    "foo_bar": "foo"
}
eot;

        $this->assertSame(
            $data,
            $this->varJson(
                $entity->toArray(),
                3
            )
        );
    }

    public function testWithAddVisible()
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

        $entity->addVisible('name');

        $data = <<<'eot'
{
    "name": "实体名字"
}
eot;

        $this->assertSame(
            $data,
            $this->varJson(
                $entity->toArray(),
                1
            )
        );

        $entity->addVisible(['description']);

        $data = <<<'eot'
{
    "name": "实体名字",
    "description": "goods name"
}
eot;

        $this->assertSame(
            $data,
            $this->varJson(
                $entity->toArray(),
                2
            )
        );

        $entity = $this->makeEntity();

        $entity->addVisible('name', 'description', 'hello');

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
                $entity->toArray(),
                3
            )
        );
    }

    public function testWithRemoveVisible()
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

        $entity->addVisible(['name', 'description', 'hello']);

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
                $entity->toArray(),
                1
            )
        );

        $entity->removeVisible('description');

        $data = <<<'eot'
{
    "name": "实体名字",
    "hello": "hello world"
}
eot;

        $this->assertSame(
            $data,
            $this->varJson(
                $entity->toArray(),
                2
            )
        );

        $entity->removeVisible('hello');

        $data = <<<'eot'
{
    "name": "实体名字"
}
eot;

        $this->assertSame(
            $data,
            $this->varJson(
                $entity->toArray(),
                3
            )
        );

        $entity->addVisible(['description', 'hello']);

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
                $entity->toArray(),
                4
            )
        );

        $entity->removeVisible(['description']);

        $data = <<<'eot'
{
    "name": "实体名字",
    "hello": "hello world"
}
eot;

        $this->assertSame(
            $data,
            $this->varJson(
                $entity->toArray(),
                5
            )
        );
    }

    public function testWithHidden()
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

        $entity->hidden('description');

        $this->assertSame(
            ['description'],
            $entity->getHidden()
        );

        $data = <<<'eot'
{
    "name": "实体名字",
    "address": "四川成都",
    "foo_bar": "foo",
    "hello": "hello world"
}
eot;

        $this->assertSame(
            $data,
            $this->varJson(
                $entity->toArray(),
                1
            )
        );

        // 第二种方式
        $entity->hidden('foo_bar', 'hello');

        $this->assertSame(
            ['foo_bar', 'hello'],
            $entity->getHidden()
        );

        $data = <<<'eot'
{
    "name": "实体名字",
    "description": "goods name",
    "address": "四川成都"
}
eot;

        $this->assertSame(
            $data,
            $this->varJson(
                $entity->toArray(),
                2
            )
        );

        // 数组方式
        $entity->hidden(['name', 'address']);

        $this->assertSame(
            ['name', 'address'],
            $entity->getHidden()
        );

        $data = <<<'eot'
{
    "description": "goods name",
    "foo_bar": "foo",
    "hello": "hello world"
}
eot;

        $this->assertSame(
            $data,
            $this->varJson(
                $entity->toArray(),
                3
            )
        );
    }

    public function testWithAddHidden()
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

        $entity->addHidden('name');

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
                $entity->toArray(),
                1
            )
        );

        $entity->addHidden(['description']);

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
                $entity->toArray(),
                2
            )
        );

        $entity = $this->makeEntity();

        $entity->addHidden('foo_bar', 'name', 'address');

        $data = <<<'eot'
{
    "description": "goods name",
    "hello": "hello world"
}
eot;

        $this->assertSame(
            $data,
            $this->varJson(
                $entity->toArray(),
                3
            )
        );
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
