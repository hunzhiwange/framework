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

class UpdateTest extends TestCase
{
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

    public function testAutoFile(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Entity `Tests\\Database\\Ddd\\Entity\\TestUpdateAutoFillEntity` has no data need to be update.');

        $entity = new TestUpdateAutoFillEntity(['id' => 5], true);
        $entity->save();
    }

    public function testUpdateAutoFile(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Entity `Tests\\Database\\Ddd\\Entity\\TestUpdateAutoFillEntity` has no data need to be update.');

        $entity = new TestUpdateAutoFillEntity(['id' => 5], true);
        $entity->save();
    }

    public function testAutoFileWithAll(): void
    {
        $entity = new TestUpdateAutoFillEntity(['id' => 5], true);
        $entity->save([], []);

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

    public function testUpdateAutoFileWithAll(): void
    {
        $entity = new TestUpdateAutoFillEntity(['id' => 5], true);
        $entity->update([], []);

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

    public function testAutoFileWithCustomField(): void
    {
        $entity = new TestUpdateAutoFillEntity(['id' => 5], true);
        $entity->save([], ['address', 'hello']);

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

    public function testUpdateAutoFileWithCustomField(): void
    {
        $entity = new TestUpdateAutoFillEntity(['id' => 5], true);
        $entity->update([], ['address', 'hello']);

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

    public function testUpdateWithNoData(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Entity `Tests\\Database\\Ddd\\Entity\\TestDatabaseEntity` has no data need to be update.');

        $entity = new TestDatabaseEntity(['id' => 1]);
        $entity->update();
    }

    public function testSaveWithPrimaryKeyData(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Entity Tests\\Database\\Ddd\\Entity\\TestDatabaseEntity has no primary key data.');

        $entity = new TestDatabaseEntity();
        $entity->update();
    }

    public function testUpdateWithPrimaryKeyData(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Entity Tests\\Database\\Ddd\\Entity\\TestDatabaseEntity has no primary key data.');

        $entity = new TestDatabaseEntity();
        $entity->update();
    }

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

    public function testReplaceWithCompositeIdButNoDataToBeUpdate(): void
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
        $entity->replace(['id1' => 2, 'id2' => 3]);

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
        return ['composite_id'];
    }
}
