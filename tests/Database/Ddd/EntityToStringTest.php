<?php

declare(strict_types=1);

namespace Tests\Database\Ddd;

use Leevel\Database\Ddd\Entity;
use Tests\Database\DatabaseTestCase as TestCase;
use Tests\Database\Ddd\Entity\DemoToArrayBlackEntity;
use Tests\Database\Ddd\Entity\DemoToArrayEntity;
use Tests\Database\Ddd\Entity\DemoToArrayShowPropNullEntity;
use Tests\Database\Ddd\Entity\DemoToArrayShowPropNullRelationEntity;
use Tests\Database\Ddd\Entity\DemoToArrayShowPropNullRelationTargetEntity;
use Tests\Database\Ddd\Entity\DemoToArrayWhiteEntity;
use Tests\Database\Ddd\Entity\Relation\Post;
use Tests\Database\Ddd\Entity\Relation\User;

final class EntityToStringTest extends TestCase
{
    public function testBaseUse(): void
    {
        $entity = $this->makeEntity();

        $data = <<<'eot'
            {"name":"实体名字","description":"goods name","address":"四川成都","foo_bar":"foo","hello":"hello world"}
            eot;

        static::assertSame(
            $data,
            $entity->__toString(),
        );
    }

    public function testWithWhite(): void
    {
        $entity = $this->makeEntity();

        $data = <<<'eot'
            {"name":"实体名字","description":"goods name","address":"四川成都","foo_bar":"foo","hello":"hello world"}
            eot;

        static::assertSame(
            $data,
            $entity->__toString()
        );

        $data = <<<'eot'
            {"name":"实体名字"}
            eot;

        static::assertSame(
            $data,
            $entity
                ->only(['name'])
                ->__toString(),
        );

        $data = <<<'eot'
            {"name":"实体名字","description":"goods name"}
            eot;

        static::assertSame(
            $data,
            $entity
                ->only(['name', 'description'])
                ->__toString(),
        );

        $data = <<<'eot'
            {"name":"实体名字","description":"goods name","hello":"hello world"}
            eot;

        static::assertSame(
            $data,
            $entity
                ->only(['name', 'description', 'hello'])
                ->__toString(),
        );
    }

    public function testWithBlack(): void
    {
        $entity = $this->makeEntity();

        $data = <<<'eot'
            {"name":"实体名字","description":"goods name","address":"四川成都","foo_bar":"foo","hello":"hello world"}
            eot;

        static::assertSame(
            $data,
            $entity->__toString()
        );

        $data = <<<'eot'
            {"description":"goods name","address":"四川成都","foo_bar":"foo","hello":"hello world"}
            eot;

        static::assertSame(
            $data,
            $entity
                ->except(['name'])
                ->__toString(),
        );

        $data = <<<'eot'
            {"address":"四川成都","foo_bar":"foo","hello":"hello world"}
            eot;

        static::assertSame(
            $data,
            $entity
                ->except(['name', 'description'])
                ->__toString(),
        );

        $data = <<<'eot'
            {"description":"goods name","hello":"hello world"}
            eot;

        static::assertSame(
            $data,
            $entity
                ->except(['foo_bar', 'name', 'address'])
                ->__toString(),
        );
    }

    public function testWithWhiteAndBlack(): void
    {
        $entity = $this->makeEntity();

        $data = <<<'eot'
            {"name":"实体名字","description":"goods name","address":"四川成都","foo_bar":"foo","hello":"hello world"}
            eot;

        static::assertSame(
            $data,
            $entity->__toString(),
        );

        $data = <<<'eot'
            {"hello":"hello world"}
            eot;

        static::assertSame(
            $data,
            $entity
                ->only(['hello'])
                ->except(['description'])
                ->__toString(),
        );
    }

    public function testWithWhiteEntity(): void
    {
        $entity = $this->makeWhiteEntity();

        $data = <<<'eot'
            {"description":"goods name","foo_bar":"foo"}
            eot;

        static::assertSame(
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

        static::assertSame(
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

        static::assertSame(
            $data,
            (string) $entity,
        );
    }

    public function testWithRelation(): void
    {
        $entity = $this->makeRelationEntity();

        $data = <<<'eot'
            {"id":5,"title":"I am title","user_id":7,"summary":"I am summary","user":{"id":7,"name":"xiaoniuge"}}
            eot;

        static::assertSame(
            $data,
            $entity->__toString(),
        );
    }

    public function testWithRelationWhiteAndBlack(): void
    {
        $entity = $this->makeRelationEntity();

        $data = <<<'eot'
            {"id":5,"title":"I am title","user_id":7,"summary":"I am summary","user":{"name":"xiaoniuge"}}
            eot;

        static::assertSame(
            $data,
            $entity
                ->each(function ($value, $k) {
                    if ('user' === $k) {
                        $value = $value->only(['name']);
                    }

                    return $value;
                })
                ->__toString(),
        );
    }

    public function testWithShowPropNull(): void
    {
        $entity = $this->makeShowPropNullEntity();

        $data = <<<'eot'
            {"name":"实体名字","description":"goods name","address":"","foo_bar":null,"hello":"default_value"}
            eot;

        static::assertSame(
            $data,
            $entity->__toString(),
        );
    }

    public function testWithShowPropNullForRelation(): void
    {
        $entity = $this->makeRelationShowPropNullEntity();

        $data = <<<'eot'
            {"id":5,"name":"I am name","description":"I am description","address":"","foo_bar":null,"hello":"default_value","target":{"id":5,"name":"xiaoniuge"}}
            eot;

        static::assertSame(
            $data,
            $entity->__toString(),
        );
    }

    protected function makeWhiteEntity(): DemoToArrayWhiteEntity
    {
        $entity = new DemoToArrayWhiteEntity();
        $this->assertInstanceof(Entity::class, $entity);
        $entity->name = '实体名字';
        $entity->description = 'goods name';
        $entity->address = '四川成都';
        $entity->foo_bar = 'foo';
        $entity->hello = 'hello world';

        return $entity;
    }

    protected function makeBlackEntity(): DemoToArrayBlackEntity
    {
        $entity = new DemoToArrayBlackEntity();
        $this->assertInstanceof(Entity::class, $entity);
        $entity->name = '实体名字';
        $entity->description = 'goods name';
        $entity->address = '四川成都';
        $entity->foo_bar = 'foo';
        $entity->hello = 'hello world';

        return $entity;
    }

    protected function makeEntity(): DemoToArrayEntity
    {
        $entity = new DemoToArrayEntity();
        $this->assertInstanceof(Entity::class, $entity);
        $entity->name = '实体名字';
        $entity->description = 'goods name';
        $entity->address = '四川成都';
        $entity->foo_bar = 'foo';
        $entity->hello = 'hello world';

        return $entity;
    }

    protected function makeRelationEntity(): Post
    {
        $user = new User(['id' => 7]);
        $user->name = 'xiaoniuge';

        $entity = new Post(['id' => 5]);
        $this->assertInstanceof(Post::class, $entity);
        $entity->title = 'I am title';
        $entity->summary = 'I am summary';
        $entity->userId = 7;
        $entity->withRelationProp('user', $user);

        return $entity;
    }

    protected function makeShowPropNullEntity(): DemoToArrayShowPropNullEntity
    {
        $entity = new DemoToArrayShowPropNullEntity();
        $this->assertInstanceof(Entity::class, $entity);
        $entity->name = '实体名字';
        $entity->description = 'goods name';

        return $entity;
    }

    protected function makeRelationShowPropNullEntity(): DemoToArrayShowPropNullRelationEntity
    {
        $target = new DemoToArrayShowPropNullRelationTargetEntity(['id' => 5]);
        $target->name = 'xiaoniuge';

        $entity = new DemoToArrayShowPropNullRelationEntity(['id' => 5]);
        $this->assertInstanceof(DemoToArrayShowPropNullRelationEntity::class, $entity);
        $entity->name = 'I am name';
        $entity->description = 'I am description';
        $entity->withRelationProp('target', $target);

        return $entity;
    }
}
