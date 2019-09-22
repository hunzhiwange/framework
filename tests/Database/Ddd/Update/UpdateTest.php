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

namespace Tests\Database\Ddd\Update;

use Leevel\Database\Ddd\Entity;
use Tests\Database\DatabaseTestCase as TestCase;
use Tests\Database\Ddd\Entity\TestDatabaseEntity;
use Tests\Database\Ddd\Entity\TestEntity;
use Tests\Database\Ddd\Entity\TestReadonlyUpdateEntity;
use Tests\Database\Ddd\Entity\TestUpdateAutoFillEntity;
use Tests\Database\Ddd\Entity\TestUpdatePropWhiteEntity;

/**
 * update test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.06.29
 *
 * @version 1.0
 */
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
}
