<?php

declare(strict_types=1);

namespace Tests\Database\Ddd;

use Leevel\Database\Ddd\Entity;
use Tests\Database\DatabaseTestCase as TestCase;
use Tests\Database\Ddd\Entity\DemoEntity;

/**
 * @api(
 *     zh-CN:title="实体常量",
 *     path="orm/define",
 *     zh-CN:description="
 * 实体初始化会校验一些必须定义的常量 `const`，这是实体对应的数据库表的一些映射，这简化了 ORM 底层后续处理逻辑。
 * ",
 * )
 */
final class EntityDefineTest extends TestCase
{
    /**
     * @api(
     *     zh-CN:title="基本常量",
     *     zh-CN:description="
     * **基础常量**
     *
     * 常量 `TABLE`,`ID`,`AUTO` 和 `STRUCT` 是每一个实体必须要定义的，否则会抛出异常。
     *
     *  * TABLE 数据库表名，例如 `test`
     *  * ID 主键字段，例如 `null`,`id` 和 `['id1', 'id2']`
     *  * AUTO 自增字段，例如 `null` 和 `id`
     *  * STRUCT 数据库字段 `['id' => [self::READONLY => true], 'name' => []]`
     *
     * **测试模型**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Database\Ddd\Entity\DemoEntity::class)]}
     * ```
     * ",
     *     zh-CN:note="",
     * )
     */
    public function testBaseUse(): void
    {
        $entity = new DemoEntity();

        $this->assertInstanceof(Entity::class, $entity);

        static::assertSame(DemoEntity::TABLE, $entity->table());
        static::assertSame([DemoEntity::ID], $entity->primaryKey());
        static::assertSame(DemoEntity::AUTO, $entity->autoIncrement());
    }

    /**
     * @api(
     *     zh-CN:title="基本常量未定义将会抛出异常",
     *     zh-CN:description="
     * **测试模型**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Database\Ddd\Test1Entity::class)]}
     * ```
     * ",
     *     zh-CN:note="",
     * )
     */
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
}

class Test1Entity extends Entity
{
}

class Test2Entity extends Entity
{
    public const TABLE = 'test2';
}

class Test3Entity extends Entity
{
    public const TABLE = 'test2';

    public const ID = [
        'id',
    ];
}

class Test4Entity extends Entity
{
    public const TABLE = 'test2';

    public const ID = [
        'id',
    ];

    public const AUTO = 'id';
}
