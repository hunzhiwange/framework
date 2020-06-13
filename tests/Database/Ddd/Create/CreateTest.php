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

namespace Tests\Database\Ddd\Create;

use Leevel\Database\Ddd\Entity;
use Tests\Database\DatabaseTestCase as TestCase;
use Tests\Database\Ddd\Entity\CompositeId;
use Tests\Database\Ddd\Entity\DemoConstructPropBlackEntity;
use Tests\Database\Ddd\Entity\DemoConstructPropWhiteEntity;
use Tests\Database\Ddd\Entity\DemoCreateAutoFillEntity;
use Tests\Database\Ddd\Entity\DemoCreatePropWhiteEntity;
use Tests\Database\Ddd\Entity\DemoDatabaseEntity;
use Tests\Database\Ddd\Entity\DemoEntity;

/**
 * @api(
 *     title="保存实体",
 *     path="orm/create",
 *     description="将实体持久化到数据库。",
 * )
 */
class CreateTest extends TestCase
{
    /**
     * @api(
     *     title="save 创建一个实体",
     *     description="
     * 没有主键数据，则可以通过 `save` 方法创建一个实体。
     *
     * **完整例子**
     *
     * ``` php
     * $entity = new DemoEntity();
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
     *     note="通过 save 方法保存一个实体，并通过 flush 将实体持久化到数据库。",
     * )
     */
    public function testBaseUse(): void
    {
        $entity = new DemoEntity();
        $this->assertInstanceof(Entity::class, $entity);

        $entity->name = 'foo';

        $this->assertSame('foo', $entity->name);
        $this->assertSame(['name'], $entity->changed());

        $this->assertNull($entity->flushData());

        $entity->save();

        $data = <<<'eot'
            [
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
     *     title="create 创建一个实体",
     *     description="",
     *     note="通过 create 方法保存一个实体，并通过 flush 将实体持久化到数据库。",
     * )
     */
    public function testCreateBaseUse(): void
    {
        $entity = new DemoEntity();
        $this->assertInstanceof(Entity::class, $entity);

        $entity->name = 'foo';

        $this->assertSame('foo', $entity->name);
        $this->assertSame(['name'], $entity->changed());

        $this->assertNull($entity->flushData());

        $entity->create();

        $data = <<<'eot'
            [
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
     *     title="创建一个实体支持构造器白名单",
     *     description="
     * **完整模型**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Database\Ddd\Entity\DemoConstructPropWhiteEntity::class)]}
     * ```
     *
     * 调用 `\Leevel\Database\Ddd\Entity::CONSTRUCT_PROP_WHITE => true` 来设置字段白名单，一旦设置了构造器白名单只有通过了白名单的字段才能够更新模型属性。
     * ",
     *     note="",
     * )
     */
    public function testConsturctPropWhite(): void
    {
        $entity = new DemoConstructPropWhiteEntity([
            'id'   => 5,
            'name' => 'foo',
        ]);

        $this->assertSame(5, $entity->getId());
        $this->assertNull($entity->getName());
    }

    /**
     * @api(
     *     title="创建一个实体支持构造器黑名单",
     *     description="
     * **完整模型**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Database\Ddd\Entity\DemoConstructPropBlackEntity::class)]}
     * ```
     *
     * 调用 `\Leevel\Database\Ddd\Entity::CONSTRUCT_PROP_BLACK => true` 来设置字段黑名单，一旦设置了构造器黑名单处于黑名单的字段无法更新模型属性。
     * ",
     *     note="",
     * )
     */
    public function testConsturctPropBlack(): void
    {
        $entity = new DemoConstructPropBlackEntity([
            'id'   => 5,
            'name' => 'foo',
        ]);

        $this->assertNull($entity->getId());
        $this->assertSame('foo', $entity->getName());
    }

    /**
     * @api(
     *     title="创建一个实体支持创建属性白名单",
     *     description="
     * **完整模型**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Database\Ddd\Entity\DemoCreatePropWhiteEntity::class)]}
     * ```
     *
     * 调用 `\Leevel\Database\Ddd\Entity::CREATE_PROP_WHITE => true` 来设置字段白名单，一旦设置了创建属性白名单只有通过了白名单的字段才能够更新模型属性。
     * ",
     *     note="",
     * )
     */
    public function testSavePropBlackAndWhite(): void
    {
        $entity = new DemoCreatePropWhiteEntity([
            'name'        => 'foo',
            'description' => 'hello description',
        ]);
        $entity->save();

        $data = <<<'eot'
            [
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

    public function testCreatePropBlackAndWhite(): void
    {
        $entity = new DemoCreatePropWhiteEntity([
            'name'        => 'foo',
            'description' => 'hello description',
        ]);
        $entity->create();

        $data = <<<'eot'
            [
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

    public function testPropNotExist(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Entity `Tests\\Database\\Ddd\\Entity\\DemoEntity` prop or field of struct `not_exists` was not defined.');

        $entity = new DemoEntity();
        $entity->notExists = 'hello';
    }

    public function testAutoFill(): void
    {
        $entity = new DemoCreateAutoFillEntity();
        $entity->save();

        $data = <<<'eot'
            [
                []
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
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Database\Ddd\Entity\DemoCreateAutoFillEntity::class)]}
     * ```
     * ",
     *     note="默认情况下，不会自动填充，除非指定允许填充字段。",
     * )
     */
    public function testCreateAutoFill(): void
    {
        $entity = new DemoCreateAutoFillEntity();
        $entity
            ->fill()
            ->create();

        $data = <<<'eot'
            [
                []
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
    public function testAutoFillWithAll(): void
    {
        $entity = new DemoCreateAutoFillEntity();
        $entity
            ->fillAll()
            ->save();

        $data = <<<'eot'
            [
                {
                    "name": "name for create_fill",
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

    public function testCreateAutoFillWithAll(): void
    {
        $entity = new DemoCreateAutoFillEntity();
        $entity
            ->fillAll()
            ->create();

        $data = <<<'eot'
            [
                {
                    "name": "name for create_fill",
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
     *     title="fill 设置允许自动填充字段指定字段例子",
     *     description="",
     *     note="",
     * )
     */
    public function testAutoFillWithCustomField(): void
    {
        $entity = new DemoCreateAutoFillEntity();
        $entity
            ->fill(['address'])
            ->save();

        $data = <<<'eot'
            [
                {
                    "address": "address is set now."
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

    public function testCreateAutoFillWithCustomField(): void
    {
        $entity = new DemoCreateAutoFillEntity();
        $entity
            ->fill(['address'])
            ->create();

        $data = <<<'eot'
            [
                {
                    "address": "address is set now."
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
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Database\Ddd\Entity\DemoDatabaseEntity::class)]}
     * ```
     * ",
     *     note="",
     * )
     */
    public function testSaveWithProp(): void
    {
        $entity = new DemoDatabaseEntity();
        $entity->save(['name' => 'hello']);

        $data = <<<'eot'
            [
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
     *     title="create 新增快捷方式支持添加数据",
     *     description="",
     *     note="",
     * )
     */
    public function testCreateWithProp(): void
    {
        $entity = new DemoDatabaseEntity();
        $entity->create(['name' => 'hello']);

        $data = <<<'eot'
            [
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

    public function testSaveWithCompositeId(): void
    {
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
    }

    protected function getDatabaseTable(): array
    {
        return ['composite_id', 'test'];
    }
}
