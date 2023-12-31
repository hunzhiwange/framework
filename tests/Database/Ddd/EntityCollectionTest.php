<?php

declare(strict_types=1);

namespace Tests\Database\Ddd;

use Leevel\Database\Ddd\EntityCollection;
use Tests\Database\DatabaseTestCase as TestCase;
use Tests\Database\Ddd\Entity\DemoEntity;

final class EntityCollectionTest extends TestCase
{
    public function test1(): void
    {
        $data = [
            new DemoEntity(['id' => 1]),
            new DemoEntity(['id' => 2]),
        ];
        $collection = new EntityCollection($data, DemoEntity::class);
        static::assertCount(2, $collection);
    }

    public function test2(): void
    {
        $data = [
            new DemoEntity(['id' => 1]),
            new DemoEntity(['id' => 2]),
        ];
        $collection = new EntityCollection($data, DemoEntity::class);
        $entity = $collection->get(0);
        static::assertSame(1, $entity->id);
    }

    public function test3(): void
    {
        $data = [
            new DemoEntity(['id' => 1]),
            new DemoEntity(['id' => 2]),
        ];
        $collection = new EntityCollection($data, DemoEntity::class);
        $collection->remove(1);
        static::assertCount(1, $collection);
    }

    public function test4(): void
    {
        $data = [
            new DemoEntity(['id' => 1]),
            new DemoEntity(['id' => 2]),
        ];
        $collection = new EntityCollection($data, DemoEntity::class);
        static::assertTrue($collection->has(0));
    }

    public function test5(): void
    {
        $data = [];
        $collection = new EntityCollection($data, DemoEntity::class);
        $collection->set(0, new DemoEntity(['id' => 2]));
        static::assertCount(1, $collection);
    }

    public function test6(): void
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage(
            'The value of a collection value type requires the following types `Tests\\Database\\Ddd\\Entity\\DemoEntity`.'
        );

        $data = [
            new \stdClass(),
        ];
        new EntityCollection($data, DemoEntity::class);
    }

    public function test7(): void
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage(
            'The value of a collection value type requires the following types `Leevel\\Database\\Ddd\\Entity`.'
        );

        $data = [
            new \stdClass(),
        ];
        new EntityCollection($data);
    }

    public function test8(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Value types `stdClass` must be a subclass of `Leevel\\Database\\Ddd\\Entity`.'
        );

        $data = [
            new \stdClass(),
        ];
        new EntityCollection($data, \stdClass::class);
    }

    public function test9(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage(
            'Entity 11 was not found.'
        );

        $data = [
            new DemoEntity(['id' => 1]),
            new DemoEntity(['id' => 2]),
        ];
        $collection = new EntityCollection($data, DemoEntity::class);
        $collection->get(11);
    }
}
