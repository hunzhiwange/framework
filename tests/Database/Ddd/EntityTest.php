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
use Tests\Database\DatabaseTestCase as TestCase;
use Tests\Database\Ddd\Entity\CompositeId;
use Tests\Database\Ddd\Entity\EntityWithEnum;
use Tests\Database\Ddd\Entity\EntityWithEnum2;
use Tests\Database\Ddd\Entity\EntityWithInvalidEnum;
use Tests\Database\Ddd\Entity\EntityWithoutPrimaryKey;
use Tests\Database\Ddd\Entity\Relation\Post;
use Tests\Database\Ddd\Entity\Relation\PostContent;
use Tests\Database\Ddd\Entity\SoftDeleteNotFoundDeleteAtField;
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
    protected function tearDown(): void
    {
        parent::tearDown();
        Container::singletons()->clear();
    }

    public function testPropNotDefined(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Prop `name` of entity `Tests\\Database\\Ddd\\Entity\\TestPropErrorEntity` was not defined.'
        );

        $entity = new TestPropErrorEntity();
        $entity->name = 5;
    }

    public function testSetPropManyTimesDoNothing(): void
    {
        $entity = new Post();
        $entity->title = 5;
        $entity->title = 5;
        $entity->title = 5;
        $entity->title = 5;

        $this->assertSame(5, $entity->title);
    }

    public function testSetPropButIsRelation(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Cannot set a relation prop `post_content` on entity `Tests\\Database\\Ddd\\Entity\\Relation\\Post`.'
        );

        $entity = new Post();
        $entity->postContent = 5;
    }

    public function testDatabaseResolverWasNotSet(): void
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

    public function testWithProps(): void
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

    public function testEntityWithEnum(): void
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

    public function testEntityWithEnum2(): void
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

    public function testEntityWithEnumItemNotFound(): void
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

    public function testEntityWithEnumItemNotFound2(): void
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

    public function testSoftDelete(): void
    {
        $connect = $this->createDatabaseConnect();

        $this->assertSame(
            1,
            $connect
                ->table('post')
                ->insert([
                    'title'     => 'hello world',
                    'user_id'   => 1,
                    'summary'   => 'post summary',
                    'delete_at' => 0,
                ]));

        $this->assertSame(
            2,
            $connect
                ->table('post')
                ->insert([
                    'title'     => 'hello world',
                    'user_id'   => 1,
                    'summary'   => 'post summary',
                    'delete_at' => 0,
                ]));

        $post = Post::select()->findEntity(1);

        $this->assertInstanceof(Post::class, $post);
        $this->assertSame(1, $post->userId);
        $this->assertSame('hello world', $post->title);
        $this->assertSame('post summary', $post->summary);
        $this->assertSame(0, $post->delete_at);

        $this->assertFalse($post->softDeleted());
        $post->softDelete()->flush();
        $this->assertTrue($post->softDeleted());

        $post1 = Post::withSoftDeleted()->findEntity(1);
        $this->assertInstanceof(Post::class, $post1);
        $this->assertSame(1, $post1->userId);
        $this->assertSame('hello world', $post1->title);
        $this->assertSame('post summary', $post1->summary);
        $this->assertSame(date('Y-m'), date('Y-m', $post1->delete_at));

        $post2 = Post::select()->findEntity(2);
        $this->assertInstanceof(Post::class, $post2);
        $this->assertSame(1, $post2->userId);
        $this->assertSame('hello world', $post2->title);
        $this->assertSame('post summary', $post2->summary);
        $this->assertSame(0, $post2->delete_at);

        $post1 = Post::select()->findEntity(1);
        $this->assertInstanceof(Post::class, $post1);
        $this->assertNull($post1->userId);
        $this->assertNull($post1->title);
        $this->assertNull($post1->summary);
        $this->assertNull($post1->delete_at);
    }

    public function testSoftDestroy(): void
    {
        $connect = $this->createDatabaseConnect();

        $this->assertSame(
            1,
            $connect
                ->table('post')
                ->insert([
                    'title'     => 'hello world',
                    'user_id'   => 1,
                    'summary'   => 'post summary',
                    'delete_at' => 0,
                ]));

        $this->assertSame(
            2,
            $connect
                ->table('post')
                ->insert([
                    'title'     => 'hello world',
                    'user_id'   => 1,
                    'summary'   => 'post summary',
                    'delete_at' => 0,
                ]));

        $post = Post::select()->findEntity(1);

        $this->assertInstanceof(Post::class, $post);
        $this->assertSame(1, $post->userId);
        $this->assertSame('hello world', $post->title);
        $this->assertSame('post summary', $post->summary);
        $this->assertSame(0, $post->delete_at);

        $this->assertFalse($post->softDeleted());
        $this->assertSame(1, Post::softDestroy([1]));
        $this->assertFalse($post->softDeleted());

        $post1 = Post::withSoftDeleted()->findEntity(1);
        $this->assertInstanceof(Post::class, $post1);
        $this->assertSame(1, $post1->userId);
        $this->assertSame('hello world', $post1->title);
        $this->assertSame('post summary', $post1->summary);
        $this->assertSame(date('Y-m'), date('Y-m', $post1->delete_at));

        $post2 = Post::select()->findEntity(2);
        $this->assertInstanceof(Post::class, $post2);
        $this->assertSame(1, $post2->userId);
        $this->assertSame('hello world', $post2->title);
        $this->assertSame('post summary', $post2->summary);
        $this->assertSame(0, $post2->delete_at);

        $post1 = Post::select()->findEntity(1);
        $this->assertInstanceof(Post::class, $post1);
        $this->assertNull($post1->userId);
        $this->assertNull($post1->title);
        $this->assertNull($post1->summary);
        $this->assertNull($post1->delete_at);
    }

    public function testSoftRestore(): void
    {
        $connect = $this->createDatabaseConnect();

        $this->assertSame(
            1,
            $connect
                ->table('post')
                ->insert([
                    'title'     => 'hello world',
                    'user_id'   => 1,
                    'summary'   => 'post summary',
                    'delete_at' => 0,
                ]));

        $this->assertSame(
            2,
            $connect
                ->table('post')
                ->insert([
                    'title'     => 'hello world',
                    'user_id'   => 1,
                    'summary'   => 'post summary',
                    'delete_at' => 0,
                ]));

        $post = Post::select()->findEntity(1);

        $this->assertInstanceof(Post::class, $post);
        $this->assertSame(1, $post->userId);
        $this->assertSame('hello world', $post->title);
        $this->assertSame('post summary', $post->summary);
        $this->assertSame(0, $post->delete_at);

        $this->assertFalse($post->softDeleted());
        $post->softDelete()->flush();
        $this->assertTrue($post->softDeleted());

        $post1 = Post::select()->findEntity(1);
        $this->assertInstanceof(Post::class, $post1);
        $this->assertNull($post1->userId);
        $this->assertNull($post1->title);
        $this->assertNull($post1->summary);
        $this->assertNull($post1->delete_at);

        $post1 = Post::withSoftDeleted()->findEntity(1);
        $this->assertInstanceof(Post::class, $post1);
        $this->assertSame(1, $post1->userId);
        $this->assertSame('hello world', $post1->title);
        $this->assertSame('post summary', $post1->summary);
        $this->assertSame(date('Y-m'), date('Y-m', $post1->delete_at));

        $post2 = Post::select()->findEntity(2);
        $this->assertInstanceof(Post::class, $post2);
        $this->assertSame(1, $post2->userId);
        $this->assertSame('hello world', $post2->title);
        $this->assertSame('post summary', $post2->summary);
        $this->assertSame(0, $post2->delete_at);

        $newPost = Post::withSoftDeleted()->findEntity(1);
        $this->assertTrue($newPost->softDeleted());
        $newPost->softRestore()->flush();
        $this->assertFalse($newPost->softDeleted());

        $restorePost1 = Post::select()->findEntity(1);
        $this->assertSame(0, $restorePost1->delete_at);

        $post1 = Post::withSoftDeleted()->findEntity(1);
        $this->assertInstanceof(Post::class, $post1);
        $this->assertSame(1, $post1->userId);
        $this->assertSame('hello world', $post1->title);
        $this->assertSame('post summary', $post1->summary);
        $this->assertSame(0, $post1->delete_at);
    }

    public function testDeleteAtColumnNotDefined(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Entity `Tests\\Database\\Ddd\\Entity\\Relation\\PostContent` soft delete field was not defined.'
        );

        $entity = new PostContent();
        $entity->softDeleted();
    }

    public function testDeleteAtColumnNotFound(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Entity `Tests\\Database\\Ddd\\Entity\\SoftDeleteNotFoundDeleteAtField` soft delete field `delete_at` was not found.'
        );

        $entity = new SoftDeleteNotFoundDeleteAtField();
        $entity->softDeleted();
    }

    public function testHasChanged(): void
    {
        $entity = new Post();
        $this->assertFalse($entity->hasChanged('title'));
        $entity->title = 'change';
        $this->assertTrue($entity->hasChanged('title'));
    }

    public function testAddChanged(): void
    {
        $entity = new Post();
        $data = <<<'eot'
            []
            eot;

        $this->assertSame(
            $data,
            $this->varJson(
                $entity->changed()
            )
        );

        $entity->addChanged(['user_id', 'title']);

        $data = <<<'eot'
            [
                "user_id",
                "title"
            ]
            eot;

        $this->assertSame(
            $data,
            $this->varJson(
                $entity->changed(),
                1
            )
        );
    }

    public function testDeleteChanged(): void
    {
        $entity = new Post();
        $data = <<<'eot'
            []
            eot;

        $this->assertSame(
            $data,
            $this->varJson(
                $entity->changed()
            )
        );

        $entity->addChanged(['user_id', 'title']);

        $data = <<<'eot'
            [
                "user_id",
                "title"
            ]
            eot;

        $this->assertSame(
            $data,
            $this->varJson(
                $entity->changed(),
                1,
            )
        );

        $entity->deleteChanged(['user_id']);

        $data = <<<'eot'
            [
                "title"
            ]
            eot;

        $this->assertSame(
            $data,
            $this->varJson(
                $entity->changed(),
                2,
            )
        );
    }

    public function testClearChanged(): void
    {
        $entity = new Post();
        $data = <<<'eot'
            []
            eot;

        $this->assertSame(
            $data,
            $this->varJson(
                $entity->changed()
            )
        );

        $entity->addChanged(['user_id', 'title']);

        $data = <<<'eot'
            [
                "user_id",
                "title"
            ]
            eot;

        $this->assertSame(
            $data,
            $this->varJson(
                $entity->changed(),
                1,
            )
        );

        $entity->clearChanged(['user_id']);

        $data = <<<'eot'
            []
            eot;

        $this->assertSame(
            $data,
            $this->varJson(
                $entity->changed(),
                2,
            )
        );
    }

    public function testSinglePrimaryKeyNotFound(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Entity Tests\\Database\\Ddd\\Entity\\EntityWithoutPrimaryKey do not have primary key or composite id not supported.'
        );

        $entity = new EntityWithoutPrimaryKey();
        $entity->singlePrimaryKey();
    }

    public function testSinglePrimaryKeyNotSupportComposite(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Entity Tests\\Database\\Ddd\\Entity\\CompositeId do not have primary key or composite id not supported.'
        );

        $entity = new CompositeId();
        $entity->singlePrimaryKey();
    }

    public function testSingleId(): void
    {
        $entity = new Post();
        $this->assertNull($entity->singleId());

        $entity = new Post(['id' => 5]);
        $this->assertSame(5, $entity->singleId());
    }

    public function testEntityWithInvalidEnum(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Invalid enum in the field `status` of entity `Tests\\Database\\Ddd\\Entity\\EntityWithInvalidEnum`.'
        );

        $this->initI18n();

        $entity = new EntityWithInvalidEnum([
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

        $entity->toArray();
    }

    public function testIdCondition(): void
    {
        $entity = new Post(['id' => 5]);
        $this->assertSame(['id' => 5], $entity->idCondition());
    }

    public function testIdConditionHasNoPrimaryKeyData(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Entity Tests\\Database\\Ddd\\Entity\\Relation\\Post has no primary key data.'
        );

        $entity = new Post();
        $this->assertNull($entity->idCondition());
    }

    public function testArrayAccessOffsetExists(): void
    {
        $entity = new Post(['id' => 5, 'title' => 'hello']);
        $this->assertTrue(isset($entity['title']));
        $this->assertFalse(isset($entity['user_id']));
    }

    public function testArrayAccessOffsetSet(): void
    {
        $entity = new Post(['id' => 5]);
        $this->assertFalse(isset($entity['title']));
        $this->assertNull($entity->title);
        $entity['title'] = 'world';
        $this->assertTrue(isset($entity['title']));
        $this->assertSame('world', $entity->title);
    }

    public function testArrayAccessOffsetGet(): void
    {
        $entity = new Post(['id' => 5]);
        $this->assertNull($entity['title']);
        $entity['title'] = 'world';
        $this->assertSame('world', $entity['title']);
    }

    public function testArrayAccessOffsetUnset(): void
    {
        $entity = new Post(['id' => 5]);
        $this->assertNull($entity['title']);
        $entity['title'] = 'world';
        $this->assertSame('world', $entity['title']);
        unset($entity['title']);
        $this->assertNull($entity['title']);
    }

    public function testSelectWithNotSupportSoftDeletedType(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Invalid soft deleted type 9999.'
        );

        Post::select(9999);
    }

    protected function initI18n(): void
    {
        $container = Container::singletons();
        $container->clear();

        $container->singleton('i18n', function (): I18nMock {
            return new I18nMock();
        });
    }

    protected function getDatabaseTable(): array
    {
        return ['post'];
    }
}
