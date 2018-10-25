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
use Tests\Database\Ddd\Entity\Relation\Post;
use Tests\Database\Query\Query;
use Tests\TestCase;

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

        $work->insert($post);
        $work->insert($post2);

        $this->assertTrue($work->inserted($post));
        $this->assertTrue($work->inserted($post2));

        $work->flush();

        $this->assertFalse($work->inserted($post));
        $this->assertFalse($work->inserted($post2));

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

        $post->title = 'new post title';
        $post->summary = 'new post summary';

        $post2->title = 'new post2 title';
        $post2->summary = 'new post2 summary';

        $work->update($post);
        $work->update($post2);

        $this->assertTrue($work->updated($post));
        $this->assertTrue($work->updated($post2));

        $work->flush();

        $this->assertFalse($work->updated($post));
        $this->assertFalse($work->updated($post2));

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

        $work->delete($post);
        $work->delete($post2);

        $this->assertTrue($work->deleted($post));
        $this->assertTrue($work->deleted($post2));

        $work->flush();

        $this->assertFalse($work->deleted($post));
        $this->assertFalse($work->deleted($post2));

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
    }

    protected function clear()
    {
        $this->truncate('post');
    }
}
