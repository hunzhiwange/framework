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

use Closure;
use Leevel\Database\Ddd\Entity;
use Tests\Database\Ddd\Entity\TestConstructBlackEntity;
use Tests\Database\Ddd\Entity\TestConstructWhiteEntity;
use Tests\Database\Ddd\Entity\TestCreateAutoFillEntity;
use Tests\Database\Ddd\Entity\TestCreateFillWhiteEntity;
use Tests\Database\Ddd\Entity\TestEntity;
use Tests\Database\Ddd\Entity\TestFillBlackEntity;
use Tests\Database\Ddd\Entity\TestFillWhiteEntity;
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
        $this->assertSame(['name'], $entity->getCreated());

        $this->assertNull($entity->getFlush());
        $this->assertNull($entity->getFlushData());

        // 此时暂未写入数据库
        $entity->save();

        $this->assertInstanceof(Closure::class, $entity->getFlush());

        $data = <<<'eot'
array (
  0 => 
  array (
    'name' => 'foo',
  ),
)
eot;

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

    public function testConsturctBlackAndWhite()
    {
        $entity = new TestConstructWhiteEntity([
            'id'   => 5,
            'name' => 'foo',
        ]);

        $this->assertSame(5, $entity->getProp('id'));
        $this->assertNull($entity->getProp('name'));

        $entity = new TestConstructBlackEntity([
            'id'   => 5,
            'name' => 'foo',
        ]);

        $this->assertNull($entity->getProp('id'));
        $this->assertSame('foo', $entity->getProp('name'));
    }

    public function testFillBlackAndWhite()
    {
        $entity = new TestFillWhiteEntity([
            'name'        => 'foo',
            'description' => 'hello description',
        ]);

        $this->assertNull($entity->getProp('id'));
        $this->assertSame('foo', $entity->getProp('name'));
        $this->assertSame('hello description', $entity->getProp('description'));

        $entity->save();

        $data = <<<'eot'
array (
  0 => 
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

        $entity = new TestFillBlackEntity([
            'name'        => 'foo',
            'description' => 'hello description',
        ]);

        $entity->save();

        $data = <<<'eot'
array (
  0 => 
  array (
    'description' => 'hello description',
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

    public function testCreateFillBlackAndWhite()
    {
        $entity = new TestCreateFillWhiteEntity([
            'name'        => 'foo',
            'description' => 'hello description',
        ]);

        $entity->save();

        $data = <<<'eot'
array (
  0 => 
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

    public function testPropNotExist()
    {
        $this->expectException(\BadMethodCallException::class);

        $entity = new TestEntity();

        $entity->notExists = 'hello';
    }

    public function testAutoFile()
    {
        $entity = new TestCreateAutoFillEntity();

        $entity->save();

        $data = <<<'eot'
array (
  0 => 
  array (
    'name' => 'name for create_fill',
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
        $entity = new TestCreateAutoFillEntity();

        $entity->autoFill(false);

        $entity->save();

        $data = <<<'eot'
array (
  0 => 
  array (
    'id' => NULL,
  ),
)
eot;

        $this->assertSame(
            $data,
            $this->varExport(
                $entity->getFlushData()
            )
        );

        $entity = new TestCreateAutoFillEntity();

        $entity->createFill(false);

        $entity->save();

        $data = <<<'eot'
array (
  0 => 
  array (
    'id' => NULL,
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
}
