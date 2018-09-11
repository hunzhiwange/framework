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

use Closure;
use Leevel\Database\Ddd\Entity;
use Tests\Database\Ddd\Entity\TestEntity;
use Tests\Database\Ddd\Entity\TestFillBlackEntity;
use Tests\Database\Ddd\Entity\TestFillWhiteEntity;
use Tests\Database\Ddd\Entity\TestReadonlyUpdateEntity;
use Tests\Database\Ddd\Entity\TestUpdateAutoFillEntity;
use Tests\Database\Ddd\Entity\TestUpdateFillWhiteEntity;
use Tests\TestCase;

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
        $entity = new TestEntity();

        $this->assertInstanceof(Entity::class, $entity);

        $entity->id = 123;
        $entity->name = 'foo';

        $this->assertSame(123, $entity->id);
        $this->assertSame('foo', $entity->name);

        $this->assertSame(['id', 'name'], $entity->getChanged());

        $this->assertNull($entity->getFlush());
        $this->assertNull($entity->getFlushData());

        // 此时暂未写入数据库
        $entity->save();

        $this->assertInstanceof(Closure::class, $entity->getFlush());

        $data = <<<'eot'
array (
  0 => 
  array (
    'id' => 123,
  ),
  1 => 
  array (
    'name' => 'foo',
  ),
)
eot;

        $this->assertSame(
            $data,
            $this->varExport(
                $entity->getFlushData()
            )
        );

        // 尝试写入，接管框架提供的数据库持久层方便测试
        $entity->setFlush(function (...$args) use ($data, $entity) {
            $this->assertSame(
                $data,
                $this->varExport(
                    $entity->getFlushData()
                )
            );

            // 此处应该将写入数据库了，做持久化存储实体
            // ...
        });

        $entity->flush();
    }

    public function testFillBlackAndWhite()
    {
        $entity = new TestFillWhiteEntity();

        $entity->id = 5;
        $entity->name = 'world';
        $entity->description = 'bar';

        $entity->save();

        $data = <<<'eot'
array (
  0 => 
  array (
    'id' => 5,
  ),
  1 => 
  array (
    'name' => 'world',
  ),
)
eot;

        $this->assertSame(
            $data,
            $this->varExport(
                $entity->getFlushData() ?: []
            )
        );

        $entity = new TestFillBlackEntity();

        $entity->id = 5;
        $entity->name = 'world';
        $entity->description = 'bar';

        $entity->save();

        $data = <<<'eot'
array (
  0 => 
  array (
    'id' => 5,
  ),
  1 => 
  array (
    'description' => 'bar',
  ),
)
eot;

        $this->assertSame(
            $data,
            $this->varExport(
                $entity->getFlushData()
            )
        );
    }

    public function testUpdateFillBlackAndWhite()
    {
        $entity = new TestUpdateFillWhiteEntity();

        $entity->id = 5;
        $entity->name = 'foo';
        $entity->description = 'hello description';

        $entity->save();

        $data = <<<'eot'
array (
  0 => 
  array (
    'id' => 5,
  ),
  1 => 
  array (
    'name' => 'foo',
  ),
)
eot;

        $this->assertSame(
            $data,
            $this->varExport(
                $entity->getFlushData()
            )
        );
    }

    public function testUpdateReadonly()
    {
        $entity = new TestReadonlyUpdateEntity();

        $entity->name = 'foo';
        $entity->description = 'bar';

        $this->assertSame(['description'], $entity->getChanged());
    }

    public function testAutoFile()
    {
        $entity = new TestUpdateAutoFillEntity();

        $entity->id = 5;

        $entity->save();

        $data = <<<'eot'
array (
  0 => 
  array (
    'id' => 5,
  ),
  1 => 
  array (
    'name' => 'name for update_fill',
    'description' => 'set description.',
    'address' => 'address is set now.',
    'foo_bar' => 'foo bar.',
    'hello' => 'hello field.',
  ),
)
eot;

        $this->assertSame(
            $data,
            $this->varExport(
                $entity->getFlushData()
            )
        );
    }

    public function testSetAutoFile()
    {
        $entity = new TestUpdateAutoFillEntity();

        $entity->id = 5;

        $entity->autoFill(false);

        $entity->save();

        $this->assertNull($entity->getFlushData());

        $entity = new TestUpdateAutoFillEntity();

        $entity->id = 5;

        $entity->UpdateFill(false);

        $entity->save();

        $this->assertNull($entity->getFlushData());
    }
}
