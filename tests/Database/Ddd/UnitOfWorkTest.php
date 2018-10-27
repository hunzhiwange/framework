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

        Meta::setDatabaseManager($this->createManager());
    }

    protected function tearDown()
    {
        $this->clear();

        Meta::setDatabaseManager(null);
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

    public function testInsert()
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
        $this->assertFalse($work->inserted($post));
        $this->assertFalse($work->inserted($post2));
        $this->assertFalse($work->registered($post));
        $this->assertFalse($work->registered($post2));

        $work->insert($post);
        $work->insert($post2);

        $this->assertTrue($work->inserted($post));
        $this->assertTrue($work->inserted($post2));
        $this->assertTrue($work->registered($post));
        $this->assertTrue($work->registered($post2));

        $work->flush();

        $this->assertFalse($work->inserted($post));
        $this->assertFalse($work->inserted($post2));
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

        $work->insert($post);
        $work->insert($post2);

        $work->flush();
    }

    public function testTransaction()
    {
        $work = UnitOfWork::make(new Post());

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

        $work = UnitOfWork::make(new Post());

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

            $w->insert($post);
            $w->insert($post2);
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

        $work->persist($post);
        $work->persist($post);

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

    public function testPersistStageDetachedEntity()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Detached entity `Tests\\Database\\Ddd\\Entity\\Relation\\Post` cannot be persist.'
        );

        $work = UnitOfWork::make();

        $connect = $this->createConnectTest();

        $post = new Post(['title' => 'foo']);

        $work->insert($post);

        $work->flush();

        $this->assertSame(1, $connect->table('post')->findCount());

        $work->persist($post);
    }

    public function testInsertButAlreadyInUpdates()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Updated entity `Tests\\Database\\Ddd\\Entity\\Relation\\Post` cannot be added for insert.'
        );

        $work = UnitOfWork::make();

        $post = new Post(['id' => 5, 'title' => 'foo']);

        $work->update($post);

        $work->insert($post);
    }

    public function testInsertButAlreadyInDeletes()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Deleted entity `Tests\\Database\\Ddd\\Entity\\Relation\\Post` cannot be added for insert.'
        );

        $work = UnitOfWork::make();

        $post = new Post(['id' => 5]);

        $work->delete($post);

        $work->insert($post);
    }

    public function testInsertManyTimes()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Entity `Tests\\Database\\Ddd\\Entity\\Relation\\Post` cannot be added for twice.'
        );

        $work = UnitOfWork::make();

        $connect = $this->createConnectTest();

        $post = new Post(['title' => 'foo']);

        $work->insert($post);
        $work->insert($post);

        $work->flush();

        $this->assertSame(1, $connect->table('post')->findCount());

        $this->clear();
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

    public function testUpdateButAlreadyInInserts()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Inserted entity `Tests\\Database\\Ddd\\Entity\\Relation\\Post` cannot be added for update.'
        );

        $work = UnitOfWork::make();

        $post = new Post(['id' => 5, 'title' => 'new']);

        $work->insert($post);

        $work->update($post);
    }

    public function testDeleteInserted()
    {
        $work = UnitOfWork::make();

        $connect = $this->createConnectTest();

        $post = new Post(['title' => 'foo']);

        $work->insert($post);
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

    public function testRefreshButNotIsStageManaged()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Entity `Tests\\Database\\Ddd\\Entity\\Relation\\Post` was not managed.'
        );

        $work = UnitOfWork::make();

        $post = new Post(['id' => 5, 'title' => 'new']);

        $work->insert($post);

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

    protected function clear()
    {
        $this->truncate('post');
        $this->truncate('guestbook');
    }
}
