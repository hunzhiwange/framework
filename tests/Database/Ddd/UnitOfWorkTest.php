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

namespace Tests\Database\Ddd;

use Leevel\Database\Ddd\Entity;
use Leevel\Database\Ddd\IUnitOfWork;
use Leevel\Database\Ddd\Meta;
use Leevel\Database\Ddd\UnitOfWork;
use Tests\Database\Ddd\Entity\CompositeId;
use Tests\Database\Ddd\Entity\Guestbook;
use Tests\Database\Ddd\Entity\GuestbookRepository;
use Tests\Database\Ddd\Entity\Relation\Post;
use Tests\Database\Query\Query;
use Tests\TestCase;
use Throwable;

/**
 * UnitOfWork test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.10.25
 *
 * @version 1.0
 */
class UnitOfWorkTest extends TestCase
{
    use Query;

    protected function setUp()
    {
        $this->clear();

        Meta::setDatabaseResolver(function () {
            return $this->createManager();
        });
    }

    protected function tearDown()
    {
        $this->clear();

        Meta::setDatabaseResolver(null);
    }

    public function testBaseUse()
    {
        $work = UnitOfWork::make();

        $this->assertInstanceof(UnitOfWork::class, $work);
        $this->assertInstanceof(IUnitOfWork::class, $work);

        $post = new Post([
            'title'   => 'hello world',
            'user_id' => 1,
            'summary' => 'post summary',
        ]);

        $this->assertNull($post->id);

        $work->persist($post);

        $work->flush();

        $this->assertSame('1', $post->id);
        $this->assertSame('1', $post['id']);
        $this->assertSame('1', $post->getId());
        $this->assertSame(1, $post->userId);
        $this->assertSame('post summary', $post->summary);

        $this->clear();
    }

    public function testPersist()
    {
        $work = UnitOfWork::make();

        $this->assertInstanceof(UnitOfWork::class, $work);
        $this->assertInstanceof(IUnitOfWork::class, $work);

        $post = new Post([
            'title'   => 'hello world',
            'user_id' => 1,
            'summary' => 'post summary',
        ]);

        $this->assertNull($post->id);

        $post2 = new Post([
            'title'   => 'hello world',
            'user_id' => 2,
            'summary' => 'foo bar',
        ]);

        $this->assertNull($post2->id);

        $work->persist($post);
        $work->persist($post2);

        $work->flush();

        $this->assertSame('1', $post->id);
        $this->assertSame('1', $post['id']);
        $this->assertSame('1', $post->getId());
        $this->assertSame(1, $post->userId);
        $this->assertSame('post summary', $post->summary);

        $this->assertSame('2', $post2->id);
        $this->assertSame('2', $post2['id']);
        $this->assertSame('2', $post2->getId());
        $this->assertSame(2, $post2->userId);
        $this->assertSame('foo bar', $post2->summary);

        $this->clear();
    }

    public function testCreate()
    {
        $work = UnitOfWork::make();

        $this->assertInstanceof(UnitOfWork::class, $work);
        $this->assertInstanceof(IUnitOfWork::class, $work);

        $post = new Post([
            'title'   => 'hello world',
            'user_id' => 1,
            'summary' => 'post summary',
        ]);

        $post2 = new Post([
            'title'   => 'hello world',
            'user_id' => 2,
            'summary' => 'foo bar',
        ]);

        $this->assertNull($post->id);
        $this->assertNull($post2->id);
        $this->assertFalse($work->created($post));
        $this->assertFalse($work->created($post2));
        $this->assertFalse($work->registered($post));
        $this->assertFalse($work->registered($post2));

        $work->create($post);
        $work->create($post2);

        $this->assertTrue($work->created($post));
        $this->assertTrue($work->created($post2));
        $this->assertTrue($work->registered($post));
        $this->assertTrue($work->registered($post2));

        $work->flush();

        $this->assertFalse($work->created($post));
        $this->assertFalse($work->created($post2));
        $this->assertFalse($work->registered($post));
        $this->assertFalse($work->registered($post2));

        $this->assertSame('1', $post->id);
        $this->assertSame('1', $post['id']);
        $this->assertSame('1', $post->getId());
        $this->assertSame(1, $post->userId);
        $this->assertSame('post summary', $post->summary);

        $this->assertSame('2', $post2->id);
        $this->assertSame('2', $post2['id']);
        $this->assertSame('2', $post2->getId());
        $this->assertSame(2, $post2->userId);
        $this->assertSame('foo bar', $post2->summary);

        $this->clear();
    }

    public function testUpdate()
    {
        $work = UnitOfWork::make();

        $this->assertInstanceof(UnitOfWork::class, $work);
        $this->assertInstanceof(IUnitOfWork::class, $work);

        $connect = $this->createConnectTest();

        $this->assertSame('1', $connect->
        table('post')->
        insert([
            'title'   => 'hello world',
            'user_id' => 1,
            'summary' => 'post summary',
        ]));

        $this->assertSame('2', $connect->
        table('post')->
        insert([
            'title'   => 'hello world',
            'user_id' => 2,
            'summary' => 'foo bar',
        ]));

        $post = Post::find(1);

        $post2 = Post::find(2);

        $this->assertInstanceof(Entity::class, $post);
        $this->assertInstanceof(Entity::class, $post2);
        $this->assertInstanceof(Post::class, $post);
        $this->assertInstanceof(Post::class, $post2);

        $this->assertSame('1', $post->id);
        $this->assertSame('1', $post['id']);
        $this->assertSame('1', $post->getId());
        $this->assertSame('1', $post->userId);
        $this->assertSame('post summary', $post->summary);
        $this->assertSame('hello world', $post->title);

        $this->assertSame('2', $post2->id);
        $this->assertSame('2', $post2['id']);
        $this->assertSame('2', $post2->getId());
        $this->assertSame('2', $post2->userId);
        $this->assertSame('foo bar', $post2->summary);
        $this->assertSame('hello world', $post2->title);

        $this->assertFalse($work->updated($post));
        $this->assertFalse($work->updated($post2));
        $this->assertFalse($work->registered($post));
        $this->assertFalse($work->registered($post2));

        $post->title = 'new post title';
        $post->summary = 'new post summary';

        $post2->title = 'new post2 title';
        $post2->summary = 'new post2 summary';

        $work->update($post);
        $work->update($post2);

        $this->assertTrue($work->updated($post));
        $this->assertTrue($work->updated($post2));
        $this->assertTrue($work->registered($post));
        $this->assertTrue($work->registered($post2));

        $work->flush();

        $this->assertFalse($work->updated($post));
        $this->assertFalse($work->updated($post2));
        $this->assertFalse($work->registered($post));
        $this->assertFalse($work->registered($post2));

        $this->assertSame('1', $post->id);
        $this->assertSame('1', $post['id']);
        $this->assertSame('1', $post->getId());
        $this->assertSame('1', $post->userId);
        $this->assertSame('new post title', $post->title);
        $this->assertSame('new post summary', $post->summary);

        $this->assertSame('2', $post2->id);
        $this->assertSame('2', $post2['id']);
        $this->assertSame('2', $post2->getId());
        $this->assertSame('2', $post2->userId);
        $this->assertSame('new post2 title', $post2->title);
        $this->assertSame('new post2 summary', $post2->summary);

        $this->clear();
    }

    public function testDelete()
    {
        $work = UnitOfWork::make();

        $this->assertInstanceof(UnitOfWork::class, $work);
        $this->assertInstanceof(IUnitOfWork::class, $work);

        $connect = $this->createConnectTest();

        $this->assertSame('1', $connect->
        table('post')->
        insert([
            'title'   => 'hello world',
            'user_id' => 1,
            'summary' => 'post summary',
        ]));

        $this->assertSame('2', $connect->
        table('post')->
        insert([
            'title'   => 'hello world',
            'user_id' => 2,
            'summary' => 'foo bar',
        ]));

        $post = Post::find(1);

        $post2 = Post::find(2);

        $this->assertInstanceof(Entity::class, $post);
        $this->assertInstanceof(Entity::class, $post2);
        $this->assertInstanceof(Post::class, $post);
        $this->assertInstanceof(Post::class, $post2);

        $this->assertSame('1', $post->id);
        $this->assertSame('1', $post['id']);
        $this->assertSame('1', $post->getId());
        $this->assertSame('1', $post->userId);
        $this->assertSame('post summary', $post->summary);
        $this->assertSame('hello world', $post->title);

        $this->assertSame('2', $post2->id);
        $this->assertSame('2', $post2['id']);
        $this->assertSame('2', $post2->getId());
        $this->assertSame('2', $post2->userId);
        $this->assertSame('foo bar', $post2->summary);
        $this->assertSame('hello world', $post2->title);

        $this->assertFalse($work->deleted($post));
        $this->assertFalse($work->deleted($post2));
        $this->assertFalse($work->registered($post));
        $this->assertFalse($work->registered($post2));

        $work->delete($post);
        $work->delete($post2);

        $this->assertTrue($work->deleted($post));
        $this->assertTrue($work->deleted($post2));
        $this->assertTrue($work->registered($post));
        $this->assertTrue($work->registered($post2));

        $work->flush();

        $this->assertFalse($work->deleted($post));
        $this->assertFalse($work->deleted($post2));
        $this->assertFalse($work->registered($post));
        $this->assertFalse($work->registered($post2));

        $postAfter = Post::find(1);

        $post2After = Post::find(2);

        $this->assertNull($postAfter->id);
        $this->assertNull($postAfter['id']);
        $this->assertNull($postAfter->getId());
        $this->assertNull($postAfter->userId);
        $this->assertNull($postAfter->title);
        $this->assertNull($postAfter->summary);

        $this->assertNull($post2After->id);
        $this->assertNull($post2After['id']);
        $this->assertNull($post2After->getId());
        $this->assertNull($post2After->userId);
        $this->assertNull($post2After->title);
        $this->assertNull($post2After->summary);

        $this->clear();
    }

    public function testRefresh()
    {
        $work = UnitOfWork::make();

        $this->assertInstanceof(UnitOfWork::class, $work);
        $this->assertInstanceof(IUnitOfWork::class, $work);

        $connect = $this->createConnectTest();

        $this->assertSame('1', $connect->
        table('post')->
        insert([
            'title'   => 'hello world',
            'user_id' => 1,
            'summary' => 'post summary',
        ]));

        $post = new Post([
            'id'      => 1,
            'title'   => 'old',
            'summary' => 'old',
        ], true);

        $this->assertSame(1, $post->getId());
        $this->assertSame('old', $post->getSummary());
        $this->assertSame('old', $post->getTitle());

        $work->persist($post);
        $work->refresh($post);

        $this->assertSame('1', $post->getId());
        $this->assertSame('post summary', $post->getSummary());
        $this->assertSame('hello world', $post->getTitle());

        $work->flush();

        $post = Post::find(1);

        $this->assertInstanceof(Entity::class, $post);
        $this->assertInstanceof(Post::class, $post);

        $this->assertSame('1', $post->id);
        $this->assertSame('1', $post['id']);
        $this->assertSame('1', $post->getId());
        $this->assertSame('1', $post->userId);
        $this->assertSame('post summary', $post->summary);
        $this->assertSame('hello world', $post->title);

        $this->clear();
    }

    public function testBeginTransaction()
    {
        $work = UnitOfWork::make();

        $this->assertInstanceof(UnitOfWork::class, $work);
        $this->assertInstanceof(IUnitOfWork::class, $work);

        $connect = $this->createConnectTest();

        $this->assertSame('1', $connect->
        table('post')->
        insert([
            'title'   => 'hello world',
            'user_id' => 1,
            'summary' => 'post summary',
        ]));

        $work->beginTransaction();

        $post = Post::find(1);
        $work->update($post);

        try {
            $post->title = 'new title';

            $work->flush();
            $work->commit();
        } catch (Throwable $e) {
            $work->close();
            $work->rollBack();
        }

        $this->assertSame('1', $post->getId());
        $this->assertSame('new title', $post->getTitle());

        $this->clear();
    }

    public function testFlushButRollBack()
    {
        $this->expectException(\PDOException::class);
        $this->expectExceptionMessage(
            '(1062)Duplicate entry \'1\' for key \'PRIMARY\''
        );

        $work = UnitOfWork::make();

        $post = new Post([
            'id'      => 1,
            'title'   => 'old',
            'summary' => 'old',
        ]);

        $post2 = new Post([
            'id'      => 1,
            'title'   => 'old',
            'summary' => 'old',
        ]);

        $work->create($post);
        $work->create($post2);

        $work->flush();
    }

    public function testTransaction()
    {
        $work = UnitOfWork::make();

        $this->assertInstanceof(UnitOfWork::class, $work);
        $this->assertInstanceof(IUnitOfWork::class, $work);

        $connect = $this->createConnectTest();

        $this->assertSame('1', $connect->
        table('post')->
        insert([
            'title'   => 'hello world',
            'user_id' => 1,
            'summary' => 'post summary',
        ]));

        $work->transaction(function ($w) {
            $post = Post::find(1);
            $w->update($post);

            $post->title = 'new title';
        });

        $newPost = Post::find(1);

        $this->assertSame('1', $newPost->getId());
        $this->assertSame('new title', $newPost->getTitle());

        $this->clear();
    }

    public function testTransactionAndRollBack()
    {
        $this->expectException(\PDOException::class);
        $this->expectExceptionMessage(
            '(1062)Duplicate entry \'1\' for key \'PRIMARY\''
        );

        $work = UnitOfWork::make();

        $this->assertInstanceof(UnitOfWork::class, $work);
        $this->assertInstanceof(IUnitOfWork::class, $work);

        $connect = $this->createConnectTest();

        $work->transaction(function ($w) {
            $post = new Post([
                'id'      => 1,
                'title'   => 'old',
                'summary' => 'old',
            ]);

            $post2 = new Post([
                'id'      => 1,
                'title'   => 'old',
                'summary' => 'old',
            ]);

            $w->create($post);
            $w->create($post2);
        });

        $this->assertSame(0, $connect->table('post')->findCount());

        $this->clear();
    }

    public function testSetRootEntity()
    {
        $work = UnitOfWork::make();

        $this->assertInstanceof(UnitOfWork::class, $work);
        $this->assertInstanceof(IUnitOfWork::class, $work);

        $connect = $this->createConnectTest();

        $this->assertSame('1', $connect->
        table('post')->
        insert([
            'title'   => 'hello world',
            'user_id' => 1,
            'summary' => 'post summary',
        ]));

        $post = Post::find(1);

        $work->setRootEntity($post);

        $work->update($post);

        $post->title = 'new title';

        $work->flush();

        $this->assertSame('1', $post->getId());
        $this->assertSame('new title', $post->getTitle());

        $newPost = Post::find(1);

        $this->assertSame('1', $newPost->getId());
        $this->assertSame('new title', $newPost->getTitle());

        $this->clear();
    }

    public function testFlushButNotFoundAny()
    {
        $work = UnitOfWork::make(new Post());

        $work->flush();

        $this->clear();
    }

    public function testPersistStageManagedEntityDoNothing()
    {
        $work = UnitOfWork::make();

        $connect = $this->createConnectTest();

        $post = new Post([
            'id'      => 1,
            'title'   => 'old',
            'summary' => 'old',
        ]);

        $work->persist($post, 'create');
        $work->persist($post, 'create');

        $work->flush();

        $this->assertSame(1, $connect->table('post')->findCount());

        $this->clear();
    }

    public function testPersistStageRemovedEntityBefore()
    {
        $work = UnitOfWork::make();

        $connect = $this->createConnectTest();

        $this->assertSame('1', $connect->
        table('post')->
        insert([
            'title'   => 'hello world',
            'user_id' => 1,
            'summary' => 'post summary',
        ]));

        $post = Post::find(1);

        $this->assertSame('1', $post->getId());
        $this->assertSame('hello world', $post->getTitle());
        $this->assertSame('post summary', $post->getSummary());

        $work->delete($post);

        $work->flush();

        $this->assertSame(0, $connect->table('post')->findCount());

        $this->clear();
    }

    public function testPersistStageRemovedEntity()
    {
        $work = UnitOfWork::make();

        $connect = $this->createConnectTest();

        $this->assertSame('1', $connect->
        table('post')->
        insert([
            'title'   => 'hello world',
            'user_id' => 1,
            'summary' => 'post summary',
        ]));

        $post = Post::find(1);

        $this->assertSame('1', $post->getId());
        $this->assertSame('hello world', $post->getTitle());
        $this->assertSame('post summary', $post->getSummary());

        $work->delete($post);

        $work->persist($post);

        $work->flush();

        $this->assertSame(1, $connect->table('post')->findCount());

        $this->clear();
    }

    public function testCreateButAlreadyInUpdates()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Updated entity `Tests\\Database\\Ddd\\Entity\\Relation\\Post` cannot be added for create.'
        );

        $work = UnitOfWork::make();

        $post = new Post(['id' => 5, 'title' => 'foo']);

        $work->update($post);

        $work->create($post);
    }

    public function testCreateButAlreadyInDeletes()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Deleted entity `Tests\\Database\\Ddd\\Entity\\Relation\\Post` cannot be added for create.'
        );

        $work = UnitOfWork::make();

        $post = new Post(['id' => 5]);

        $work->delete($post);

        $work->create($post);
    }

    public function testCreateButAlreadyInReplaces()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Replaced entity `Tests\\Database\\Ddd\\Entity\\Relation\\Post` cannot be added for create.'
        );

        $work = UnitOfWork::make();

        $post = new Post(['id' => 5]);

        $work->replace($post);

        $work->create($post);
    }

    public function testCreateManyTimes()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Entity `Tests\\Database\\Ddd\\Entity\\Relation\\Post` cannot be added for twice.'
        );

        $work = UnitOfWork::make();

        $connect = $this->createConnectTest();

        $post = new Post(['title' => 'foo']);

        $work->create($post);
        $work->create($post);
    }

    public function testUpdateButAlreadyInDeletes()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Deleted entity `Tests\\Database\\Ddd\\Entity\\Relation\\Post` cannot be added for update.'
        );

        $work = UnitOfWork::make();

        $post = new Post(['id' => 5, 'title' => 'new']);

        $work->delete($post);

        $work->update($post);
    }

    public function testUpdateButAlreadyInCreates()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Created entity `Tests\\Database\\Ddd\\Entity\\Relation\\Post` cannot be added for update.'
        );

        $work = UnitOfWork::make();

        $post = new Post(['id' => 5, 'title' => 'new']);

        $work->create($post);

        $work->update($post);
    }

    public function testUpdateButAlreadyInReplaces()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Replaced entity `Tests\\Database\\Ddd\\Entity\\Relation\\Post` cannot be added for update.'
        );

        $work = UnitOfWork::make();

        $post = new Post(['id' => 5, 'title' => 'new']);

        $work->replace($post);

        $work->update($post);
    }

    public function testUpdateManyTimes()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Entity `Tests\\Database\\Ddd\\Entity\\Relation\\Post` cannot be updated for twice.'
        );

        $work = UnitOfWork::make();

        $connect = $this->createConnectTest();

        $post = new Post(['id' => 1, 'title' => 'foo']);

        $work->update($post);
        $work->update($post);
    }

    public function testUpdateButHasNoPrimaryData()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Entity `Tests\\Database\\Ddd\\Entity\\Relation\\Post` has no identity for update.'
        );

        $work = UnitOfWork::make();

        $connect = $this->createConnectTest();

        $post = new Post(['title' => 'foo']);

        $work->update($post);
    }

    public function testDeleteCreated()
    {
        $work = UnitOfWork::make();

        $connect = $this->createConnectTest();

        $post = new Post(['title' => 'foo']);

        $work->create($post);
        $work->delete($post);

        $work->flush();

        $this->assertSame(0, $connect->table('post')->findCount());

        $this->clear();
    }

    public function testDeleteUpdated()
    {
        $work = UnitOfWork::make();

        $connect = $this->createConnectTest();

        $this->assertSame('1', $connect->
        table('post')->
        insert([
            'title'   => 'hello world',
            'user_id' => 1,
            'summary' => 'post summary',
        ]));

        $post = Post::find(1);

        $work->update($post);
        $work->delete($post);

        $post->title = 'new';

        $work->flush();

        $postNew = Post::find(1);

        $this->assertSame(0, $connect->table('post')->findCount());
        $this->assertNull($postNew->id);
        $this->assertNull($postNew->title);

        $this->clear();
    }

    public function testDeleteReplaced()
    {
        $work = UnitOfWork::make();

        $connect = $this->createConnectTest();

        $this->assertSame('1', $connect->
        table('post')->
        insert([
            'title'   => 'hello world',
            'user_id' => 1,
            'summary' => 'post summary',
        ]));

        $post = Post::find(1);

        $work->replace($post);
        $work->delete($post);

        $post->title = 'new';

        $work->flush();

        $postNew = Post::find(1);

        $this->assertSame(0, $connect->table('post')->findCount());
        $this->assertNull($postNew->id);
        $this->assertNull($postNew->title);

        $this->clear();
    }

    public function testRefreshButNotIsStageManaged()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Entity `Tests\\Database\\Ddd\\Entity\\Relation\\Post` was not managed.'
        );

        $work = UnitOfWork::make();

        $post = new Post(['id' => 5, 'title' => 'new']);

        $work->delete($post);

        $work->refresh($post);
    }

    public function testPersistButUnitOfWorkWasClosed()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Unit of work has closed.'
        );

        $work = UnitOfWork::make();

        $work->close();

        $post = new Post(['id' => 5, 'title' => 'new']);

        $work->persist($post);
    }

    public function testRepository()
    {
        $work = UnitOfWork::make();

        $repository = $work->repository(Guestbook::class);

        $this->assertInstanceof(GuestbookRepository::class, $repository);
    }

    public function testRepository2()
    {
        $work = UnitOfWork::make();

        $repository = $work->repository(new Guestbook());

        $this->assertInstanceof(GuestbookRepository::class, $repository);
    }

    public function testRemoveStageNewDoNothing()
    {
        $work = UnitOfWork::make();

        $this->assertInstanceof(UnitOfWork::class, $work);
        $this->assertInstanceof(IUnitOfWork::class, $work);

        $work->remove($post = new Post());

        $this->assertSame(IUnitOfWork::STATE_NEW, $work->getEntityState($post));

        $this->clear();
    }

    public function testRemoveStageRemovedDoNothing()
    {
        $work = UnitOfWork::make();

        $this->assertInstanceof(UnitOfWork::class, $work);
        $this->assertInstanceof(IUnitOfWork::class, $work);

        $work->delete($post = new Post(['id' => 5]));
        $work->remove($post);

        $this->assertSame(IUnitOfWork::STATE_REMOVED, $work->getEntityState($post));

        $this->clear();
    }

    public function testRemoveStageManagedWillDelete()
    {
        $work = UnitOfWork::make();

        $this->assertInstanceof(UnitOfWork::class, $work);
        $this->assertInstanceof(IUnitOfWork::class, $work);

        $post = new Post();

        $this->assertSame(IUnitOfWork::STATE_NEW, $work->getEntityState($post));

        $work->persist($post);

        $this->assertSame(IUnitOfWork::STATE_MANAGED, $work->getEntityState($post));

        $work->remove($post);

        $this->assertSame(IUnitOfWork::STATE_NEW, $work->getEntityState($post));

        $this->clear();
    }

    public function testPersistAsSaveUpdate()
    {
        $work = UnitOfWork::make();

        $connect = $this->createConnectTest();

        $post = new Post([
            'id'      => 1,
            'title'   => 'old',
            'summary' => 'old',
        ]);

        $work->persist($post);

        $work->flush();

        $this->assertSame(0, $connect->table('post')->findCount());

        $this->clear();
    }

    public function testPersistAsUpdate()
    {
        $work = UnitOfWork::make();

        $connect = $this->createConnectTest();

        $post = new Post([
            'id'      => 1,
            'title'   => 'old',
            'summary' => 'old',
        ]);

        $work->persist($post, 'update');

        $work->flush();

        $this->assertSame(0, $connect->table('post')->findCount());

        $this->clear();
    }

    public function testPersistAsReplace()
    {
        // phpunit 不支持 try catch
        $this->expectException(\PDOException::class);
        $this->expectExceptionMessage(
            '(1062)Duplicate entry \'1\' for key \'PRIMARY\''
        );

        $work = UnitOfWork::make();

        $connect = $this->createConnectTest();

        $this->assertSame('1', $connect->
        table('post')->
        insert([
            'title'   => 'hello world',
            'user_id' => 1,
            'summary' => 'post summary',
        ]));

        $post = new Post([
            'id'      => 1,
            'title'   => 'old',
            'summary' => 'old',
        ]);

        $work->persist($post, 'replace');

        $work->flush();

        // 非 phpunit 模式下面系统会更新 post 的数据
    }

    public function testPersistStageDetachedEntity()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Detached entity `Tests\\Database\\Ddd\\Entity\\Relation\\Post` cannot be persist.'
        );

        $work = UnitOfWork::make();

        $post = new Post(['id' => 5, 'title' => 'new']);

        $work->persist($post);

        $work->flush($post);

        $work->persist($post);
    }

    public function testRemoveStageDetachedEntity()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Detached entity `Tests\\Database\\Ddd\\Entity\\Relation\\Post` cannot be remove.'
        );

        $work = UnitOfWork::make();

        $post = new Post(['id' => 5, 'title' => 'new']);

        $work->persist($post);

        $work->flush($post);

        $work->remove($post);
    }

    public function testOnCallbacks()
    {
        $work = UnitOfWork::make();

        $post = new Post(['title' => 'new']);
        $guest_book = new Guestbook([]);

        $work->persist($post);
        $work->persist($guest_book);

        $work->on($post, function ($p) use ($guest_book) {
            $guest_book->content = 'guest_book content was post id is '.$p->id;
        });

        $work->flush($post);

        $newGuestbook = Guestbook::find(1);

        $this->assertSame('guest_book content was post id is 1', $newGuestbook->content);

        $work->clear();
    }

    public function testOnCallbacksForReplace()
    {
        $work = UnitOfWork::make();

        $post = new Post(['title' => 'new']);
        $guest_book = new Guestbook([]);

        $work->replace($post);
        $work->replace($guest_book);

        $work->on($post, function ($p) use ($guest_book) {
            $guest_book->content = 'guest_book content was post id is '.$p->id;
        });

        $work->flush($post);

        $newGuestbook = Guestbook::find(1);

        $this->assertSame('guest_book content was post id is 1', $newGuestbook->content);

        $work->clear();
    }

    public function testOnCallbacksForUpdate()
    {
        $work = UnitOfWork::make();

        $connect = $this->createConnectTest();

        $this->assertSame('1', $connect->
        table('post')->
        insert([
            'title'   => 'hello world',
            'user_id' => 1,
            'summary' => 'post summary',
        ]));

        $this->assertSame('1', $connect->
        table('guest_book')->
        insert([
            'content'   => 'hello world',
        ]));

        $post = new Post(['id' => 1, 'title' => 'new'], true);
        $guest_book = new Guestbook(['id' => 1], true);

        $work->update($post);
        $work->update($guest_book);

        $work->on($post, function ($p) use ($guest_book) {
            $guest_book->content = 'guest_book content was post id is '.$p->id;
        });

        $work->flush($post);

        $newGuestbook = Guestbook::find(1);

        $this->assertSame('guest_book content was post id is 1', $newGuestbook->content);

        $work->clear();
    }

    public function testReplaceButAlreadyInDeletes()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Deleted entity `Tests\\Database\\Ddd\\Entity\\Relation\\Post` cannot be added for replace.'
        );

        $work = UnitOfWork::make();

        $post = new Post(['id' => 5, 'title' => 'new']);

        $work->delete($post);

        $work->replace($post);
    }

    public function testReplace()
    {
        $work = UnitOfWork::make();

        $post = new Post(['id' => 1, 'title' => 'new']);

        $this->assertFalse($work->replaced($post));

        $work->replace($post);

        $this->assertTrue($work->replaced($post));

        $work->flush();

        $this->assertFalse($work->replaced($post));

        $createPost = Post::find(1);

        $this->assertInstanceof(Post::class, $createPost);
        $this->assertSame('1', $createPost->id);
        $this->assertSame('new', $createPost->title);

        $this->clear();
    }

    public function testReplaceAsUpdate()
    {
        // phpunit 不支持 try catch
        $this->expectException(\PDOException::class);
        $this->expectExceptionMessage(
            '(1062)Duplicate entry \'1\' for key \'PRIMARY\''
        );

        $work = UnitOfWork::make();

        $connect = $this->createConnectTest();

        $this->assertSame('1', $connect->
        table('post')->
        insert([
            'title'   => 'hello world',
            'user_id' => 1,
            'summary' => 'post summary',
        ]));

        $post = new Post(['id' => 1, 'title' => 'new', 'summary' => 'new']);

        $work->replace($post);

        $work->flush();

        // 非 phpunit 模式下面系统会更新 post 的数据
    }

    public function testReplaceButAlreadyInCreates()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Created entity `Tests\\Database\\Ddd\\Entity\\Relation\\Post` cannot be added for replace.'
        );

        $work = UnitOfWork::make();

        $post = new Post(['id' => 5, 'title' => 'new']);

        $work->create($post);

        $work->replace($post);
    }

    public function testReplaceButAlreadyInUpdates()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Updated entity `Tests\\Database\\Ddd\\Entity\\Relation\\Post` cannot be added for replace.'
        );

        $work = UnitOfWork::make();

        $post = new Post(['id' => 5, 'title' => 'new']);

        $work->update($post);

        $work->replace($post);
    }

    public function testReplaceManyTimes()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Entity `Tests\\Database\\Ddd\\Entity\\Relation\\Post` cannot be replaced for twice.'
        );

        $work = UnitOfWork::make();

        $connect = $this->createConnectTest();

        $post = new Post(['title' => 'foo']);

        $work->replace($post);
        $work->replace($post);
    }

    public function testDeleteButHasNoPrimaryData()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Entity `Tests\\Database\\Ddd\\Entity\\Relation\\Post` has no identity for delete.'
        );

        $work = UnitOfWork::make();

        $post = new Post(['title' => 'new']);

        $work->delete($post);
    }

    public function testDeleteManyTimes()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Entity `Tests\\Database\\Ddd\\Entity\\Relation\\Post` cannot be deleted for twice.'
        );

        $work = UnitOfWork::make();

        $connect = $this->createConnectTest();

        $post = new Post(['id' => 1, 'title' => 'foo']);

        $work->delete($post);
        $work->delete($post);
    }

    public function testRegisterManaged()
    {
        $work = UnitOfWork::make();

        $connect = $this->createConnectTest();

        $post = new Post(['id' => 1, 'title' => 'foo']);

        $this->assertSame(IUnitOfWork::STATE_DETACHED, $work->getEntityState($post));

        $work->registerManaged($post);

        $this->assertSame(IUnitOfWork::STATE_MANAGED, $work->getEntityState($post));
    }

    public function testRegisterManagedFromNew()
    {
        $work = UnitOfWork::make();

        $connect = $this->createConnectTest();

        $post = new Post(['title' => 'foo']);

        $this->assertSame(IUnitOfWork::STATE_NEW, $work->getEntityState($post));

        $work->registerManaged($post);

        $this->assertSame(IUnitOfWork::STATE_MANAGED, $work->getEntityState($post));
    }

    public function testPersistAsCompositeIdReplace()
    {
        $this->truncate('composite_id');

        $work = UnitOfWork::make();

        $connect = $this->createConnectTest();

        $compositeId = new CompositeId([
            'id1'      => 1,
            'id2'      => 2,
            'name'     => 'old',
        ]);

        $work->persist($compositeId, 'replace');

        $work->flush();

        $this->assertSame(1, $connect->table('composite_id')->findCount());

        $this->truncate('composite_id');
        $this->clear();
    }

    public function testPersistAsCompositeIdReplace2()
    {
        $this->truncate('composite_id');

        $work = UnitOfWork::make();

        $connect = $this->createConnectTest();

        $compositeId = new CompositeId([
            'id1'      => 1,
            'id2'      => 2,
            'name'     => 'old',
        ]);

        $work->persist($compositeId);

        $work->flush();

        $this->assertSame(1, $connect->table('composite_id')->findCount());

        $this->truncate('composite_id');
        $this->clear();
    }

    protected function clear()
    {
        $this->truncate('post');
        $this->truncate('guest_book');
    }
}
