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

namespace Tests\Database\Ddd\Replace;

use Leevel\Database\Ddd\Entity;
use Tests\Database\DatabaseTestCase as TestCase;
use Tests\Database\Ddd\Entity\CompositeId;
use Tests\Database\Ddd\Entity\TestEntity;

/**
 * @api(
 *     title="尝试更新（没有则新增）实体",
 *     path="orm/replace",
 *     description="将实体变更持久化到数据库。",
 * )
 */
class ReplaceTest extends TestCase
{
    /**
     * @api(
     *     title="replace 尝试更新（没有则新增）实体",
     *     description="
     * **完整例子**
     *
     * ``` php
     * $entity = new TestEntity(['id' => 1]);
     * $entity->name = 'foo';
     * $entity->replace()->flush();
     * ```
     *
     * 调用 `replace` 方法并没有立刻真正持久化到数据库，这一个步骤计算好了待保存的数据。
     *
     * **完整模型**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Database\Ddd\Entity\TestEntity::class)]}
     * ```
     * ",
     *     note="通过 replace 方法尝试更新（没有则新增）一个实体，并通过 flush 将实体持久化到数据库。",
     * )
     */
    public function testBaseUse(): void
    {
        $entity = new TestEntity(['id' => 1]);
        $this->assertInstanceof(Entity::class, $entity);
        $entity->name = 'foo';
        $this->assertSame(1, $entity->id);
        $this->assertSame('foo', $entity->name);
        $this->assertSame(['id', 'name'], $entity->changed());
        $this->assertNull($entity->flushData());
        $entity->replace();

        $data = <<<'eot'
            [
                {
                    "id": 1,
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
     *     title="replace 尝试更新（没有则新增）实体新增例子",
     *     description="",
     *     note="",
     * )
     */
    public function testReplaceBaseUseCreate(): void
    {
        $entity = new TestEntity(['id' => 1]);
        $this->assertInstanceof(Entity::class, $entity);
        $entity->name = 'foo';
        $this->assertSame(1, $entity->id);
        $this->assertSame('foo', $entity->name);
        $this->assertSame(['id', 'name'], $entity->changed());
        $this->assertNull($entity->flushData());
        $entity->replace();

        $data = <<<'eot'
            [
                {
                    "id": 1,
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

        $this->assertSame(1, $entity->flush());
        $entity->refresh();
        $this->assertSame(1, $entity->id);
        $this->assertSame('foo', $entity->name);
    }

    /**
     * @api(
     *     title="replace 尝试更新（没有则新增）实体更新例子",
     *     description="",
     *     note="",
     * )
     */
    public function testReplaceBaseUseUpdate(): void
    {
        $connect = $this->createDatabaseConnect();
        $this->assertSame(
            1,
            $connect
                ->table('test')
                ->insert([
                    'id'       => 1,
                    'name'     => 'old',
                ]));

        $entity = new TestEntity(['id' => 1]);
        $this->assertInstanceof(Entity::class, $entity);
        $entity->name = 'foo';
        $this->assertSame(1, $entity->id);
        $this->assertSame('foo', $entity->name);
        $this->assertSame(['id', 'name'], $entity->changed());
        $this->assertNull($entity->flushData());
        $entity->replace();

        $data = <<<'eot'
            [
                {
                    "id": 1,
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

        $this->assertSame(1, $entity->flush());
        $entity->refresh();
        $this->assertSame(1, $entity->id);
        $this->assertSame('foo', $entity->name);
    }

    /**
     * @api(
     *     title="replace 尝试更新（没有则新增）快捷方式，记录存在但是不存在更新数据不作任何处理",
     *     description="
     * 这里和单纯的更新不一样，单纯的更新不存在更新数据，则会抛出异常。
     * ",
     *     note="",
     * )
     */
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

    protected function getDatabaseTable(): array
    {
        return ['composite_id', 'test'];
    }
}
