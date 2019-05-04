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

namespace Tests\Database\Ddd\Create;

use Leevel\Database\Ddd\Entity;
use Tests\Database\DatabaseTestCase as TestCase;
use Tests\Database\Ddd\Entity\TestConstructPropBlackEntity;
use Tests\Database\Ddd\Entity\TestConstructPropWhiteEntity;
use Tests\Database\Ddd\Entity\TestCreateAutoFillEntity;
use Tests\Database\Ddd\Entity\TestCreatePropWhiteEntity;
use Tests\Database\Ddd\Entity\TestEntity;

/**
 * create test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.06.29
 *
 * @version 1.0
 *
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
     *     title="创建一个实体",
     *     description="
     * _**完整例子**_
     *
     * ``` php
     * $entity = new TestEntity();
     * $entity->name = 'foo';
     * $entity->save()->flush();
     * ```
     *
     * 调用 `save` 方法并没有立刻真正持久化到数据库，这一个步骤计算好了待保存的数据。
     * ",
     *     note="通过 save 方法保存一个实体，并通过 flush 将实体持久化到数据库。",
     * )
     */
    public function testBaseUse()
    {
        $entity = new TestEntity();

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
     *     title="创建一个实体支持构造器白名单",
     *     description="
     * _**完整模型**_
     *
     * ``` php
     * ".\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Database\Ddd\Entity\TestConstructPropWhiteEntity::class)."
     * ```
     *
     * 调用 `construct_prop_white => true` 来设置字段白名单，一旦设置了白名单只有通过了白名单的数据才能够通过构造器更新模型属性。
     * ",
     *     note="",
     * )
     */
    public function testConsturctPropWhite()
    {
        $entity = new TestConstructPropWhiteEntity([
            'id'   => 5,
            'name' => 'foo',
        ]);

        $this->assertSame(5, $entity->getterId());
        $this->assertNull($entity->getterName());
    }

    /**
     * @api(
     *     title="创建一个实体支持构造器黑名单",
     *     description="
     * _**完整模型**_
     *
     * ``` php
     * ".\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Database\Ddd\Entity\TestConstructPropBlackEntity::class)."
     * ```
     *
     * 调用 `construct_prop_black => true` 来设置字段黑名单，一旦设置了黑名单处于黑名单的数据无法通过构造器更新模型属性。
     * ",
     *     note="",
     * )
     */
    public function testConsturctPropBlack()
    {
        $entity = new TestConstructPropBlackEntity([
            'id'   => 5,
            'name' => 'foo',
        ]);

        $this->assertNull($entity->getterId());
        $this->assertSame('foo', $entity->getterName());
    }

    public function testCreatePropBlackAndWhite()
    {
        $entity = new TestCreatePropWhiteEntity([
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

    public function testPropNotExist()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Entity `Tests\\Database\\Ddd\\Entity\\TestEntity` prop or field of struct `not_exists` was not defined.');

        $entity = new TestEntity();

        $entity->notExists = 'hello';
    }

    public function testAutoFile()
    {
        $entity = new TestCreateAutoFillEntity();

        $entity->save();

        $data = <<<'eot'
            [
                {
                    "id": null
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

    public function testAutoFileWithAll()
    {
        $entity = new TestCreateAutoFillEntity();

        $entity->save([], []);

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

    public function testAutoFileWithCustomField()
    {
        $entity = new TestCreateAutoFillEntity();

        $entity->save([], ['address']);

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
}
