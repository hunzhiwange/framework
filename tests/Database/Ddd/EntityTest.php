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

namespace Tests\Database\Ddd;

use I18nMock;
use Leevel\Di\Container;
use Leevel\Leevel\App;
use Tests\Database\DatabaseTestCase as TestCase;
use Tests\Database\Ddd\Entity\EntityWithEnum;
use Tests\Database\Ddd\Entity\EntityWithEnum2;
use Tests\Database\Ddd\Entity\Relation\Post;
use Tests\Database\Ddd\Entity\TestPropErrorEntity;

/**
 * entity test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.11.02
 *
 * @version 1.0
 */
class EntityTest extends TestCase
{
    protected function tearDown()
    {
        App::singletons()->clear();
    }

    public function testPropNotDefined()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Prop `name` of entity `Tests\\Database\\Ddd\\Entity\\TestPropErrorEntity` was not defined.'
        );

        $entity = new TestPropErrorEntity();
        $entity->name = 5;
    }

    public function testSetPropManyTimesDoNothing()
    {
        $entity = new Post();
        $entity->title = 5;
        $entity->title = 5;
        $entity->title = 5;
        $entity->title = 5;

        $this->assertSame(5, $entity->title);
    }

    public function testSetPropButIsRelation()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Cannot set a relation prop `post_content` on entity `Tests\\Database\\Ddd\\Entity\\Relation\\Post`.'
        );

        $entity = new Post();
        $entity->postContent = 5;
    }

    public function testDatabaseResolverWasNotSet()
    {
        $this->metaWithoutDatabase();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Database resolver was not set.'
        );

        $container = new Container();
        $container->instance('app', $container);

        $entity = new Post(['title' => 'foo']);
        $entity->create()->flush();
    }

    public function testWithProps()
    {
        $entity = new Post();

        $entity->withProps([
            'title'   => 'foo',
            'summary' => 'bar',
        ]);

        $this->assertSame('foo', $entity->title);
        $this->assertSame('bar', $entity->summary);
        $this->assertSame(['title', 'summary'], $entity->changed());
    }

    public function testEntityWithEnum()
    {
        $this->initI18n();

        $entity = new EntityWithEnum([
            'title'   => 'foo',
            'status'  => '1',
        ]);

        $this->assertSame('foo', $entity->title);
        $this->assertSame('1', $entity->status);

        $data = <<<'eot'
            {
                "title": "foo"
            }
            eot;

        $this->assertSame(
            $data,
            $this->varJson(
                $entity->toArray(['title'])
            )
        );

        $data = <<<'eot'
            {
                "id": null,
                "title": "foo",
                "status": "1",
                "status_enum": "启用"
            }
            eot;

        $this->assertSame(
            $data,
            $this->varJson(
                $entity->toArray(),
                2
            )
        );

        $this->assertSame('启用', $entity->enum('status', '1'));
        $this->assertSame('禁用', $entity->enum('status', '0'));
        $this->assertFalse($entity->enum('not', '0'));
        $this->assertFalse($entity->enum('not'));

        $data = <<<'eot'
            [
                [
                    0,
                    "禁用"
                ],
                [
                    1,
                    "启用"
                ]
            ]
            eot;

        $this->assertSame(
            $data,
            $this->varJson(
                $entity->enum('status'),
                3
            )
        );
    }

    public function testEntityWithEnum2()
    {
        $this->initI18n();

        $entity = new EntityWithEnum2([
            'title'   => 'foo',
            'status'  => 't',
        ]);

        $data = <<<'eot'
            [
                [
                    "f",
                    "禁用"
                ],
                [
                    "t",
                    "启用"
                ]
            ]
            eot;

        $this->assertSame(
            $data,
            $this->varJson(
                $entity->enum('status')
            )
        );
    }

    public function testEntityWithEnumItemNotFound()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Value not a enum in the field `status` of entity `Tests\\Database\\Ddd\\Entity\\EntityWithEnum`.'
        );

        $entity = new EntityWithEnum([
            'title'   => 'foo',
            'status'  => '1',
        ]);

        $entity->enum('status', '5');
    }

    public function testEntityWithEnumItemNotFound2()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Value not a enum in the field `status` of entity `Tests\\Database\\Ddd\\Entity\\EntityWithEnum`.'
        );

        $entity = new EntityWithEnum([
            'title'   => 'foo',
            'status'  => '5',
        ]);

        $entity->toArray();
    }

    protected function initI18n()
    {
        $app = App::singletons();
        $app->clear();

        $app->singleton('i18n', function (): I18nMock {
            return new I18nMock();
        });
    }
}
