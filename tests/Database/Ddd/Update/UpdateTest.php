<?php

declare(strict_types=1);

namespace Tests\Database\Ddd\Update;

use Leevel\Database\Ddd\Entity;
use Tests\Database\DatabaseTestCase as TestCase;
use Tests\Database\Ddd\Entity\CompositeId;
use Tests\Database\Ddd\Entity\DemoDatabaseEntity;
use Tests\Database\Ddd\Entity\DemoEntity;
use Tests\Database\Ddd\Entity\DemoReadonlyUpdateEntity;
use Tests\Database\Ddd\Entity\DemoUpdateAutoFillEntity;
use Tests\Database\Ddd\Entity\DemoUpdatePropWhiteEntity;

/**
 * @api(
 *     zh-CN:title="更新实体",
 *     path="orm/update",
 *     zh-CN:description="将实体变更持久化到数据库。",
 * )
 *
 * @internal
 *
 * @coversNothing
 */
final class UpdateTest extends TestCase
{
    /**
     * @api(
     *     zh-CN:title="save 更新一个实体",
     *     zh-CN:description="
     * 存在主键数据，则可以通过 `save` 方法更新一个实体。
     *
     * **完整例子**
     *
     * ``` php
     * $entity = new DemoEntity(['id' => 1], true);
     * $entity->name = 'foo';
     * $entity->save()->flush();
     * ```
     *
     * 调用 `save` 方法并没有立刻真正持久化到数据库，这一个步骤计算好了待保存的数据。
     *
     * **完整模型**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Database\Ddd\Entity\DemoEntity::class)]}
     * ```
     * ",
     *     zh-CN:note="通过 save 方法更新一个实体，并通过 flush 将实体持久化到数据库。",
     * )
     */
    public function testBaseUse(): void
    {
        $entity = new DemoEntity(['id' => 1], true);
        $entity->name = 'foo';

        $this->assertInstanceof(Entity::class, $entity);
        static::assertSame(1, $entity->id);
        static::assertSame('foo', $entity->name);
        static::assertSame(['name'], $entity->changed());
        static::assertNull($entity->flushData());
        $entity->save();

        $data = <<<'eot'
            [
                {
                    "id": 1
                },
                {
                    "name": "foo"
                }
            ]
            eot;

        static::assertSame(
            $data,
            $this->varJson(
                $entity->flushData()
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="update 更新一个实体",
     *     zh-CN:description="",
     *     zh-CN:note="通过 update 方法保存一个实体，并通过 flush 将实体持久化到数据库。",
     * )
     */
    public function testUpdateBaseUse(): void
    {
        $entity = new DemoEntity(['id' => 1], true);
        $entity->name = 'foo';

        $this->assertInstanceof(Entity::class, $entity);
        static::assertSame(1, $entity->id);
        static::assertSame('foo', $entity->name);
        static::assertSame(['name'], $entity->changed());
        static::assertNull($entity->flushData());
        $entity->update();

        $data = <<<'eot'
            [
                {
                    "id": 1
                },
                {
                    "name": "foo"
                }
            ]
            eot;

        static::assertSame(
            $data,
            $this->varJson(
                $entity->flushData()
            )
        );
    }

    public function testSavePropBlackAndWhite(): void
    {
        $entity = new DemoUpdatePropWhiteEntity(['id' => 5], true);
        $entity->name = 'foo';
        $entity->description = 'hello description';
        $entity->save();

        $data = <<<'eot'
            [
                {
                    "id": 5
                },
                {
                    "name": "foo"
                }
            ]
            eot;

        static::assertSame(
            $data,
            $this->varJson(
                $entity->flushData()
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="更新一个实体支持更新属性白名单",
     *     zh-CN:description="
     * **完整模型**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Database\Ddd\Entity\DemoUpdatePropWhiteEntity::class)]}
     * ```
     *
     * 调用 `\Leevel\Database\Ddd\Entity::UPDATE_PROP_WHITE => true` 来设置字段白名单，一旦设置了更新属性白名单只有通过了白名单的字段才能够更新模型属性。
     * ",
     *     zh-CN:note="",
     * )
     */
    public function testUpdatePropBlackAndWhite(): void
    {
        $entity = new DemoUpdatePropWhiteEntity(['id' => 5], true);
        $entity->name = 'foo';
        $entity->description = 'hello description';
        $entity->update();

        $data = <<<'eot'
            [
                {
                    "id": 5
                },
                {
                    "name": "foo"
                }
            ]
            eot;

        static::assertSame(
            $data,
            $this->varJson(
                $entity->flushData()
            )
        );
    }

    public function testUpdateReadonly(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot set a read-only prop `name` on entity `Tests\\Database\\Ddd\\Entity\\DemoReadonlyUpdateEntity`.');

        $entity = new DemoReadonlyUpdateEntity();
        $entity->name = 'foo';
    }

    public function testAutoFillDoNothing(): void
    {
        $entity = new DemoUpdateAutoFillEntity(['id' => 5], true);
        $this->assertInstanceof(DemoUpdateAutoFillEntity::class, $entity->save());
        static::assertNull($entity->flushData());
    }

    public function testAutoFillWithCustomField(): void
    {
        $entity = new DemoUpdateAutoFillEntity(['id' => 5], true);
        $entity
            ->fill(['address', 'hello'])
            ->save()
        ;

        $data = <<<'eot'
            [
                {
                    "id": 5
                },
                {
                    "address": "address is set now.",
                    "hello": "hello field."
                }
            ]
            eot;

        static::assertSame(
            $data,
            $this->varJson(
                $entity->flushData()
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="fill 设置允许自动填充字段",
     *     zh-CN:description="
     * **完整模型**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Database\Ddd\Entity\DemoUpdateAutoFillEntity::class)]}
     * ```
     * ",
     *     zh-CN:note="默认情况下，不会自动填充，除非指定允许填充字段。",
     * )
     */
    public function testUpdateAutoFillWithCustomField(): void
    {
        $entity = new DemoUpdateAutoFillEntity(['id' => 5], true);
        $entity
            ->fill(['address', 'hello'])
            ->update()
        ;

        $data = <<<'eot'
            [
                {
                    "id": 5
                },
                {
                    "address": "address is set now.",
                    "hello": "hello field."
                }
            ]
            eot;

        static::assertSame(
            $data,
            $this->varJson(
                $entity->flushData()
            )
        );
    }

    public function testAutoFillWithAll(): void
    {
        $entity = new DemoUpdateAutoFillEntity(['id' => 5], true);
        $entity
            ->fillAll()
            ->save()
        ;

        $data = <<<'eot'
            [
                {
                    "id": 5
                },
                {
                    "name": "name for update_fill",
                    "description": "set description.",
                    "address": "address is set now.",
                    "foo_bar": "foo bar.",
                    "hello": "hello field."
                }
            ]
            eot;

        static::assertSame(
            $data,
            $this->varJson(
                $entity->flushData()
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="fillAll 设置允许自动填充字段为所有字段",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testUpdateAutoFillWithAll(): void
    {
        $entity = new DemoUpdateAutoFillEntity(['id' => 5], true);
        $entity
            ->fillAll()
            ->update()
        ;

        $data = <<<'eot'
            [
                {
                    "id": 5
                },
                {
                    "name": "name for update_fill",
                    "description": "set description.",
                    "address": "address is set now.",
                    "foo_bar": "foo bar.",
                    "hello": "hello field."
                }
            ]
            eot;

        static::assertSame(
            $data,
            $this->varJson(
                $entity->flushData()
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="save 自动判断操作快捷方式支持添加数据",
     *     zh-CN:description="
     * **完整模型**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Database\Ddd\Entity\DemoDatabaseEntity::class)]}
     * ```
     * ",
     *     zh-CN:note="",
     * )
     */
    public function testSaveWithProp(): void
    {
        $entity = new DemoDatabaseEntity(['id' => 1], true);
        $entity->save(['name' => 'hello']);

        $data = <<<'eot'
            [
                {
                    "id": 1
                },
                {
                    "name": "hello"
                }
            ]
            eot;

        static::assertSame(
            $data,
            $this->varJson(
                $entity->flushData()
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="update 更新快捷方式支持添加数据",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testUpdateWithProp(): void
    {
        $entity = new DemoDatabaseEntity(['id' => 1]);
        $entity->update(['name' => 'hello']);

        $data = <<<'eot'
            [
                {
                    "id": 1
                },
                {
                    "name": "hello"
                }
            ]
            eot;

        static::assertSame(
            $data,
            $this->varJson(
                $entity->flushData()
            )
        );
    }

    public function testSaveWithNoDataAndDoNothing(): void
    {
        $entity = new DemoDatabaseEntity(['id' => 1], true);
        $this->assertInstanceof(DemoDatabaseEntity::class, $entity->save());
        static::assertNull($entity->flushData());
    }

    /**
     * @api(
     *     zh-CN:title="update 更新快捷方式存在更新数据才能够保存",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testUpdateWithNoDataAndDoNothing(): void
    {
        $entity = new DemoDatabaseEntity(['id' => 1]);
        $this->assertInstanceof(DemoDatabaseEntity::class, $entity->update());
        static::assertNull($entity->flushData());
    }

    /**
     * @api(
     *     zh-CN:title="update 更新快捷方式存在主键数据才能够保存",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testUpdateWithPrimaryKeyData(): void
    {
        $this->expectException(\Leevel\Database\Ddd\EntityIdentifyConditionException::class);
        $this->expectExceptionMessage('Entity Tests\\Database\\Ddd\\Entity\\DemoDatabaseEntity has no identify condition data.');

        $entity = new DemoDatabaseEntity();
        $entity->update();
    }

    /**
     * @api(
     *     zh-CN:title="save 自动判断操作快捷方式复合主键例子",
     *     zh-CN:description="
     * **完整模型**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Database\Ddd\Entity\CompositeId::class)]}
     * ```
     * ",
     *     zh-CN:note="",
     * )
     */
    public function testSaveWithCompositeId(): void
    {
        $connect = $this->createDatabaseConnect();

        static::assertSame(
            1,
            $connect
                ->table('composite_id')
                ->insert([
                    'id1' => 2,
                    'id2' => 3,
                ])
        );

        $entity = new CompositeId();
        $entity->save(['id1' => 2, 'id2' => 3, 'name' => 'hello']);

        $data = <<<'eot'
            [
                {
                    "id1": 2,
                    "id2": 3,
                    "name": "hello"
                }
            ]
            eot;

        static::assertSame(
            $data,
            $this->varJson(
                $entity->flushData()
            )
        );

        $entity->flush();

        $sql = 'SQL: [167] UPDATE `composite_id` SET `composite_id`.`name` = :named_param_name WHERE `composite_id`.`id1` = :composite_id_id1 AND `composite_id`.`id2` = :composite_id_id2 LIMIT 1 | Params:  3 | Key: Name: [23] :named_param_name | paramno=0 | name=[23] ":named_param_name" | is_param=1 | param_type=2 | Key: Name: [17] :composite_id_id1 | paramno=1 | name=[17] ":composite_id_id1" | is_param=1 | param_type=1 | Key: Name: [17] :composite_id_id2 | paramno=2 | name=[17] ":composite_id_id2" | is_param=1 | param_type=1 (UPDATE `composite_id` SET `composite_id`.`name` = \'hello\' WHERE `composite_id`.`id1` = 2 AND `composite_id`.`id2` = 3 LIMIT 1)';
        static::assertSame($sql, $entity->select()->getLastSql());
    }

    public function testSaveWithCompositeIdButNoDataToBeUpdate(): void
    {
        $connect = $this->createDatabaseConnect();
        static::assertSame(
            1,
            $connect
                ->table('composite_id')
                ->insert([
                    'id1' => 2,
                    'id2' => 3,
                ])
        );

        $entity = new CompositeId();
        $entity->save(['id1' => 2, 'id2' => 3]);

        $data = <<<'eot'
            [
                {
                    "id1": 2,
                    "id2": 3
                }
            ]
            eot;

        static::assertSame(
            $data,
            $this->varJson(
                $entity->flushData()
            )
        );

        $entity->flush();

        $sql = '[FAILED] SQL: [125] INSERT INTO `composite_id` (`composite_id`.`id1`,`composite_id`.`id2`) VALUES (:named_param_id1,:named_param_id2) | Params:  2 | Key: Name: [22] :named_param_id1 | paramno=0 | name=[22] ":named_param_id1" | is_param=1 | param_type=1 | Key: Name: [22] :named_param_id2 | paramno=1 | name=[22] ":named_param_id2" | is_param=1 | param_type=1 (INSERT INTO `composite_id` (`composite_id`.`id1`,`composite_id`.`id2`) VALUES (2,3))';
        static::assertSame($sql, $entity->select()->getLastSql());
    }

    public function testUpdateWithCompositeIdButNoDataToBeUpdateAndDoNothing(): void
    {
        $connect = $this->createDatabaseConnect();
        static::assertSame(
            1,
            $connect
                ->table('composite_id')
                ->insert([
                    'id1' => 2,
                    'id2' => 3,
                ])
        );

        $entity = new CompositeId();
        $entity->update(['id1' => 2, 'id2' => 3]);
        static::assertNull($entity->flushData());
        static::assertNull($entity->flush());
    }

    protected function getDatabaseTable(): array
    {
        return ['composite_id', 'test'];
    }
}
