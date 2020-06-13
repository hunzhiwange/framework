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

namespace Tests\Database\Ddd\Update;

use Leevel\Database\Ddd\Entity;
use Tests\Database\DatabaseTestCase as TestCase;
use Tests\Database\Ddd\Entity\CompositeId;
use Tests\Database\Ddd\Entity\TestDatabaseEntity;
use Tests\Database\Ddd\Entity\TestEntity;
use Tests\Database\Ddd\Entity\TestReadonlyUpdateEntity;
use Tests\Database\Ddd\Entity\TestUpdateAutoFillEntity;
use Tests\Database\Ddd\Entity\TestUpdatePropWhiteEntity;

/**
 * @api(
 *     title="更新实体",
 *     path="orm/update",
 *     description="将实体变更持久化到数据库。",
 * )
 */
class UpdateTest extends TestCase
{
    /**
     * @api(
     *     title="save 更新一个实体",
     *     description="
     * 存在主键数据，则可以通过 `save` 方法更新一个实体。
     *
     * **完整例子**
     *
     * ``` php
     * $entity = new TestEntity(['id' => 1], true);
     * $entity->name = 'foo';
     * $entity->save()->flush();
     * ```
     *
     * 调用 `save` 方法并没有立刻真正持久化到数据库，这一个步骤计算好了待保存的数据。
     *
     * **完整模型**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Database\Ddd\Entity\TestEntity::class)]}
     * ```
     * ",
     *     note="通过 save 方法更新一个实体，并通过 flush 将实体持久化到数据库。",
     * )
     */
    public function testBaseUse(): void
    {
        $entity = new TestEntity(['id' => 1], true);
        $this->assertInstanceof(Entity::class, $entity);
        $entity->name = 'foo';
        $this->assertSame(1, $entity->id);
        $this->assertSame('foo', $entity->name);
        $this->assertSame(['name'], $entity->changed());
        $this->assertNull($entity->flushData());
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

        $this->assertSame(
            $data,
            $this->varJson(
                $entity->flushData()
            )
        );
    }

    /**
     * @api(
     *     title="update 更新一个实体",
     *     description="",
     *     note="通过 update 方法保存一个实体，并通过 flush 将实体持久化到数据库。",
     * )
     */
    public function testUpdateBaseUse(): void
    {
        $entity = new TestEntity(['id' => 1], true);
        $this->assertInstanceof(Entity::class, $entity);
        $entity->name = 'foo';
        $this->assertSame(1, $entity->id);
        $this->assertSame('foo', $entity->name);
        $this->assertSame(['name'], $entity->changed());
        $this->assertNull($entity->flushData());
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

        $this->assertSame(
            $data,
            $this->varJson(
                $entity->flushData()
            )
        );
    }

    public function testSavePropBlackAndWhite(): void
    {
        $entity = new TestUpdatePropWhiteEntity(['id' => 5], true);
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

        $this->assertSame(
            $data,
            $this->varJson(
                $entity->flushData()
            )
        );
    }

    /**
     * @api(
     *     title="更新一个实体支持更新属性白名单",
     *     description="
     * **完整模型**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Database\Ddd\Entity\TestUpdatePropWhiteEntity::class)]}
     * ```
     *
     * 调用 `\Leevel\Database\Ddd\Entity::UPDATE_PROP_WHITE => true` 来设置字段白名单，一旦设置了更新属性白名单只有通过了白名单的字段才能够更新模型属性。
     * ",
     *     note="",
     * )
     */
    public function testUpdatePropBlackAndWhite(): void
    {
        $entity = new TestUpdatePropWhiteEntity(['id' => 5], true);
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

        $this->assertSame(
            $data,
            $this->varJson(
                $entity->flushData()
            )
        );
    }

    public function testUpdateReadonly(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot set a read-only prop `name` on entity `Tests\\Database\\Ddd\\Entity\\TestReadonlyUpdateEntity`.');

        $entity = new TestReadonlyUpdateEntity();
        $entity->name = 'foo';
    }

    public function testAutoFill(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Entity `Tests\\Database\\Ddd\\Entity\\TestUpdateAutoFillEntity` has no data need to be update.');

        $entity = new TestUpdateAutoFillEntity(['id' => 5], true);
        $entity->save();
    }

    public function testUpdateAutoFill(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Entity `Tests\\Database\\Ddd\\Entity\\TestUpdateAutoFillEntity` has no data need to be update.');

        $entity = new TestUpdateAutoFillEntity(['id' => 5], true);
        $entity->save();
    }

    public function testAutoFillWithCustomField(): void
    {
        $entity = new TestUpdateAutoFillEntity(['id' => 5], true);
        $entity
            ->fill(['address', 'hello'])
            ->save();

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

        $this->assertSame(
            $data,
            $this->varJson(
                $entity->flushData()
            )
        );
    }

    /**
     * @api(
     *     title="fill 设置允许自动填充字段",
     *     description="
     * **完整模型**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Database\Ddd\Entity\TestUpdateAutoFillEntity::class)]}
     * ```
     * ",
     *     note="默认情况下，不会自动填充，除非指定允许填充字段。",
     * )
     */
    public function testUpdateAutoFillWithCustomField(): void
    {
        $entity = new TestUpdateAutoFillEntity(['id' => 5], true);
        $entity
            ->fill(['address', 'hello'])
            ->update();

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

        $this->assertSame(
            $data,
            $this->varJson(
                $entity->flushData()
            )
        );
    }

    public function testAutoFillWithAll(): void
    {
        $entity = new TestUpdateAutoFillEntity(['id' => 5], true);
        $entity
            ->fillAll()
            ->save();

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

        $this->assertSame(
            $data,
            $this->varJson(
                $entity->flushData()
            )
        );
    }

    /**
     * @api(
     *     title="fillAll 设置允许自动填充字段为所有字段",
     *     description="",
     *     note="",
     * )
     */
    public function testUpdateAutoFillWithAll(): void
    {
        $entity = new TestUpdateAutoFillEntity(['id' => 5], true);
        $entity
            ->fillAll()
            ->update();

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

        $this->assertSame(
            $data,
            $this->varJson(
                $entity->flushData()
            )
        );
    }

    /**
     * @api(
     *     title="save 自动判断操作快捷方式支持添加数据",
     *     description="
     * **完整模型**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Database\Ddd\Entity\TestDatabaseEntity::class)]}
     * ```
     * ",
     *     note="",
     * )
     */
    public function testSaveWithProp(): void
    {
        $entity = new TestDatabaseEntity(['id' => 1]);
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

        $this->assertSame(
            $data,
            $this->varJson(
                $entity->flushData()
            )
        );
    }

    /**
     * @api(
     *     title="update 更新快捷方式支持添加数据",
     *     description="",
     *     note="",
     * )
     */
    public function testUpdateWithProp(): void
    {
        $entity = new TestDatabaseEntity(['id' => 1]);
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

        $this->assertSame(
            $data,
            $this->varJson(
                $entity->flushData()
            )
        );
    }

    public function testSaveWithNoData(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Entity `Tests\\Database\\Ddd\\Entity\\TestDatabaseEntity` has no data need to be update.');

        $entity = new TestDatabaseEntity(['id' => 1]);
        $entity->save();
    }

    /**
     * @api(
     *     title="update 更新快捷方式存在更新数据才能够保存",
     *     description="",
     *     note="",
     * )
     */
    public function testUpdateWithNoData(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Entity `Tests\\Database\\Ddd\\Entity\\TestDatabaseEntity` has no data need to be update.');

        $entity = new TestDatabaseEntity(['id' => 1]);
        $entity->update();
    }

    /**
     * @api(
     *     title="update 更新快捷方式存在主键数据才能够保存",
     *     description="",
     *     note="",
     * )
     */
    public function testUpdateWithPrimaryKeyData(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Entity Tests\\Database\\Ddd\\Entity\\TestDatabaseEntity has no primary key data.');

        $entity = new TestDatabaseEntity();
        $entity->update();
    }

    /**
     * @api(
     *     title="save 自动判断操作快捷方式复合主键例子",
     *     description="
     * **完整模型**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Database\Ddd\Entity\CompositeId::class)]}
     * ```
     * ",
     *     note="",
     * )
     */
    public function testSaveWithCompositeId(): void
    {
        $connect = $this->createDatabaseConnect();

        $this->assertSame(
            1,
            $connect
                ->table('composite_id')
                ->insert([
                    'id1'     => 2,
                    'id2'     => 3,
                ]));

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

        $this->assertSame(
            $data,
            $this->varJson(
                $entity->flushData()
            )
        );

        $entity->flush();
    }

    public function testSaveWithCompositeIdButNoDataToBeUpdate(): void
    {
        $connect = $this->createDatabaseConnect();
        $this->assertSame(
            1,
            $connect
                ->table('composite_id')
                ->insert([
                    'id1'     => 2,
                    'id2'     => 3,
                ]));

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

        $this->assertSame(
            $data,
            $this->varJson(
                $entity->flushData()
            )
        );

        $entity->flush();

        $sql = '[FAILED] SQL: [125] INSERT INTO `composite_id` (`composite_id`.`id1`,`composite_id`.`id2`) VALUES (:pdonamedparameter_id1,:pdonamedparameter_id2) | Params:  2 | Key: Name: [22] :pdonamedparameter_id1 | paramno=0 | name=[22] ":pdonamedparameter_id1" | is_param=1 | param_type=1 | Key: Name: [22] :pdonamedparameter_id2 | paramno=1 | name=[22] ":pdonamedparameter_id2" | is_param=1 | param_type=1 (INSERT INTO `composite_id` (`composite_id`.`id1`,`composite_id`.`id2`) VALUES (2,3))';
        $this->assertSame($sql, $entity->select()->getLastSql());
    }

    public function testUpdateWithCompositeIdButNoDataToBeUpdate(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Entity `Tests\\Database\\Ddd\\Entity\\CompositeId` has no data need to be update.');

        $connect = $this->createDatabaseConnect();
        $this->assertSame(
            1,
            $connect
                ->table('composite_id')
                ->insert([
                    'id1'     => 2,
                    'id2'     => 3,
                ]));

        $entity = new CompositeId();
        $entity->update(['id1' => 2, 'id2' => 3]);

        $data = <<<'eot'
            [
                {
                    "id1": 2,
                    "id2": 3
                }
            ]
            eot;

        $this->assertSame(
            $data,
            $this->varJson(
                $entity->flushData()
            )
        );

        $entity->flush();
    }

    protected function getDatabaseTable(): array
    {
        return ['composite_id', 'test'];
    }
}
