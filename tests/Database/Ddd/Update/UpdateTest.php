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

namespace Tests\Database\Ddd\Update;

use Leevel\Database\Ddd\Entity;
use Tests\Database\DatabaseTestCase as TestCase;
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
    public function testBaseUse()
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

    public function testUpdatePropBlackAndWhite()
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

    public function testUpdateReadonly()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot set a read-only prop `name` on entity `Tests\\Database\\Ddd\\Entity\\TestReadonlyUpdateEntity`.');

        $entity = new TestReadonlyUpdateEntity();

        $entity->name = 'foo';
    }

    public function testAutoFile()
    {
        $entity = new TestUpdateAutoFillEntity(['id' => 5], true);

        $entity->save();

        $this->assertNull($entity->flushData());
    }

    public function testAutoFileWithAll()
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

    public function testAutoFileWithCustomField()
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
}
