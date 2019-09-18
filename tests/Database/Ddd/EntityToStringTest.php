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
use Tests\Database\DatabaseTestCase as TestCase;
use Tests\Database\Ddd\Entity\TestToArrayBlackEntity;
use Tests\Database\Ddd\Entity\TestToArrayEntity;
use Tests\Database\Ddd\Entity\TestToArrayWhiteEntity;

class EntityToStringTest extends TestCase
{
    public function testBaseUse(): void
    {
        $entity = $this->makeEntity();

        $data = <<<'eot'
            {"name":"实体名字","description":"goods name","address":"四川成都","foo_bar":"foo","hello":"hello world"}
            eot;

        $this->assertSame(
            $data,
            $entity->__toString(),
        );
    }

    public function testJsonEncodeWithCustomOptions(): void
    {
        $entity = $this->makeEntity();

        $data = <<<'eot'
            {"name":"\u5b9e\u4f53\u540d\u5b57","description":"goods name","address":"\u56db\u5ddd\u6210\u90fd","foo_bar":"foo","hello":"hello world"}
            eot;

        $this->assertSame(
            $data,
            $entity->__toString(0),
        );
    }

    public function testWithWhite(): void
    {
        $entity = $this->makeEntity();

        $data = <<<'eot'
            {"name":"实体名字","description":"goods name","address":"四川成都","foo_bar":"foo","hello":"hello world"}
            eot;

        $this->assertSame(
            $data,
            $entity->__toString()
        );

        $data = <<<'eot'
            {"name":"实体名字"}
            eot;

        $this->assertSame(
            $data,
            $entity->__toString(null, ['name']),
        );

        $data = <<<'eot'
            {"name":"实体名字","description":"goods name"}
            eot;

        $this->assertSame(
            $data,
            $entity->__toString(null, ['name', 'description']),
        );

        $data = <<<'eot'
            {"name":"实体名字","description":"goods name","hello":"hello world"}
            eot;

        $this->assertSame(
            $data,
            $entity->__toString(null, ['name', 'description', 'hello']),
        );
    }

    public function testWithBlack(): void
    {
        $entity = $this->makeEntity();

        $data = <<<'eot'
            {"name":"实体名字","description":"goods name","address":"四川成都","foo_bar":"foo","hello":"hello world"}
            eot;

        $this->assertSame(
            $data,
            $entity->__toString()
        );

        $data = <<<'eot'
            {"description":"goods name","address":"四川成都","foo_bar":"foo","hello":"hello world"}
            eot;

        $this->assertSame(
            $data,
            $entity->__toString(null, [], ['name']),
        );

        $data = <<<'eot'
            {"address":"四川成都","foo_bar":"foo","hello":"hello world"}
            eot;

        $this->assertSame(
            $data,
            $entity->__toString(null, [], ['name', 'description']),
        );

        $data = <<<'eot'
            {"description":"goods name","hello":"hello world"}
            eot;

        $this->assertSame(
            $data,
            $entity->__toString(null, [], ['foo_bar', 'name', 'address']),
        );
    }

    public function testWithWhiteAndBlack(): void
    {
        $entity = $this->makeEntity();

        $data = <<<'eot'
            {"name":"实体名字","description":"goods name","address":"四川成都","foo_bar":"foo","hello":"hello world"}
            eot;

        $this->assertSame(
            $data,
            $entity->__toString(),
        );

        $data = <<<'eot'
            {"hello":"hello world"}
            eot;

        $this->assertSame(
            $data,
            $entity->__toString(null, ['hello'], ['description']),
        );
    }

    public function testWithWhiteEntity(): void
    {
        $entity = $this->makeWhiteEntity();

        $data = <<<'eot'
            {"description":"goods name","foo_bar":"foo"}
            eot;

        $this->assertSame(
            $data,
            $entity->__toString(),
        );
    }

    public function testWithBlackEntity(): void
    {
        $entity = $this->makeBlackEntity();

        $data = <<<'eot'
            {"name":"实体名字","address":"四川成都","hello":"hello world"}
            eot;

        $this->assertSame(
            $data,
            $entity->__toString(),
        );
    }

    public function testConvertEntityToString(): void
    {
        $entity = $this->makeEntity();

        $data = <<<'eot'
            {"name":"实体名字","description":"goods name","address":"四川成都","foo_bar":"foo","hello":"hello world"}
            eot;

        $this->assertSame(
            $data,
            (string) $entity,
        );
    }

    protected function makeWhiteEntity(): TestToArrayWhiteEntity
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

    protected function makeBlackEntity(): TestToArrayBlackEntity
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

    protected function makeEntity(): TestToArrayEntity
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
