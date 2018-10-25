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
 * (c) 2010-2018 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Database\Ddd\Create;

use Leevel\Database\Ddd\Entity;
use Tests\Database\Ddd\Entity\TestConstructPropBlackEntity;
use Tests\Database\Ddd\Entity\TestConstructPropWhiteEntity;
use Tests\Database\Ddd\Entity\TestCreateAutoFillEntity;
use Tests\Database\Ddd\Entity\TestCreatePropWhiteEntity;
use Tests\Database\Ddd\Entity\TestEntity;
use Tests\TestCase;

/**
 * create test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.06.29
 *
 * @version 1.0
 */
class CreateTest extends TestCase
{
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

    public function testConsturctPropBlackAndWhite()
    {
        $entity = new TestConstructPropWhiteEntity([
            'id'   => 5,
            'name' => 'foo',
        ]);

        $this->assertSame(5, $entity->getId());
        $this->assertNull($entity->getName());
    }

    public function testConsturctPropBlackAndWhite2()
    {
        $entity = new TestConstructPropBlackEntity([
            'id'   => 5,
            'name' => 'foo',
        ]);

        $this->assertNull($entity->getId());
        $this->assertSame('foo', $entity->getName());
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
