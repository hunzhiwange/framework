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

use Leevel\Collection\Collection;
use Leevel\Database\Ddd\IEntity;
use Leevel\Database\Ddd\Select;
use Leevel\Database\Select as DatabaseSelect;
use Tests\Database\DatabaseTestCase as TestCase;
use Tests\Database\Ddd\Entity\Relation\Post;
use Tests\Database\Ddd\Entity\Relation\PostContent;

/**
 * select test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.10.29
 *
 * @version 1.0
 */
class SelectTest extends TestCase
{
    public function testBase()
    {
        $connect = $this->createDatabaseConnect();

        $this->assertSame('1', $connect->
        table('post')->
        insert([
            'title'   => 'hello world',
            'user_id' => 1,
            'summary' => 'post summary',
        ]));

        $select = new Select(new Post());
        $post = $select->find(1);
        $entity = $select->entity();

        $this->assertInstanceof(Post::class, $post);
        $this->assertSame('1', $post->id);
        $this->assertSame('1', $post->userId);
        $this->assertSame('hello world', $post->title);
        $this->assertSame('post summary', $post->summary);
        $this->assertInstanceof(IEntity::class, $entity);
    }

    public function testFind()
    {
        $connect = $this->createDatabaseConnect();

        $this->assertSame('1', $connect->
        table('post')->
        insert([
            'title'   => 'hello world',
            'user_id' => 1,
            'summary' => 'post summary',
        ]));

        $select = new Select(new Post());
        $post = $select->find(1);

        $this->assertInstanceof(Post::class, $post);
        $this->assertSame('1', $post->id);
        $this->assertSame('1', $post->userId);
        $this->assertSame('hello world', $post->title);
        $this->assertSame('post summary', $post->summary);
    }

    public function testFindOrFail()
    {
        $connect = $this->createDatabaseConnect();

        $this->assertSame('1', $connect->
        table('post')->
        insert([
            'title'   => 'hello world',
            'user_id' => 1,
            'summary' => 'post summary',
        ]));

        $select = new Select(new Post());
        $post = $select->findOrFail(1);

        $this->assertInstanceof(Post::class, $post);
        $this->assertSame('1', $post->id);
        $this->assertSame('1', $post->userId);
        $this->assertSame('hello world', $post->title);
        $this->assertSame('post summary', $post->summary);
    }

    public function testFindOrFailThrowsException()
    {
        $this->expectException(\Leevel\Database\Ddd\EntityNotFoundException::class);
        $this->expectExceptionMessage(
            'Entity `Tests\\Database\\Ddd\\Entity\\Relation\\Post` was not found.'
        );

        $select = new Select(new Post());
        $post = $select->findOrFail(1);
    }

    public function testFindMany()
    {
        $connect = $this->createDatabaseConnect();

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
            'user_id' => 1,
            'summary' => 'post summary',
        ]));

        $select = new Select(new Post());
        $posts = $select->findMany([1, 2]);

        $this->assertInstanceof(Collection::class, $posts);
        $this->assertCount(2, $posts);

        $post1 = $posts[0];
        $this->assertInstanceof(Post::class, $post1);
        $this->assertSame('1', $post1->userId);
        $this->assertSame('hello world', $post1->title);
        $this->assertSame('post summary', $post1->summary);

        $post2 = $posts[1];
        $this->assertInstanceof(Post::class, $post2);
        $this->assertSame('1', $post2->userId);
        $this->assertSame('hello world', $post2->title);
        $this->assertSame('post summary', $post2->summary);
    }

    public function testFindManyWithEmptyIds()
    {
        $select = new Select(new Post());
        $posts = $select->findMany([]);

        $this->assertInstanceof(Collection::class, $posts);
        $this->assertCount(0, $posts);
    }

    public function testFindManyWithoutResults()
    {
        $select = new Select(new Post());
        $posts = $select->findMany([1, 2]);

        $this->assertInstanceof(Collection::class, $posts);
        $this->assertCount(0, $posts);
    }

    public function testSoftDelete()
    {
        $connect = $this->createDatabaseConnect();

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
            'user_id' => 1,
            'summary' => 'post summary',
        ]));

        $select = new Select($post = Post::find(1));

        $this->assertInstanceof(Post::class, $post);
        $this->assertSame('1', $post->userId);
        $this->assertSame('hello world', $post->title);
        $this->assertSame('post summary', $post->summary);
        $this->assertNull($post->delete_at);

        $this->assertFalse($post->softDeleted());
        $this->assertSame(1, $select->softDelete());
        $this->assertTrue($post->softDeleted());

        $post1 = Post::find(1);
        $this->assertInstanceof(Post::class, $post1);
        $this->assertSame('1', $post1->userId);
        $this->assertSame('hello world', $post1->title);
        $this->assertSame('post summary', $post1->summary);
        $this->assertStringContainsString(date('Y-m-d'), $post1->delete_at);

        $post2 = Post::find(2);
        $this->assertInstanceof(Post::class, $post2);
        $this->assertSame('1', $post2->userId);
        $this->assertSame('hello world', $post2->title);
        $this->assertSame('post summary', $post2->summary);
        $this->assertNull($post2->delete_at);
    }

    public function testSoftDestroy()
    {
        $connect = $this->createDatabaseConnect();

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
            'user_id' => 1,
            'summary' => 'post summary',
        ]));

        $select = new Select($post = Post::find(1));

        $this->assertInstanceof(Post::class, $post);
        $this->assertSame('1', $post->userId);
        $this->assertSame('hello world', $post->title);
        $this->assertSame('post summary', $post->summary);
        $this->assertNull($post->delete_at);

        $this->assertFalse($post->softDeleted());
        $this->assertSame(1, $select->softDestroy([1]));
        $this->assertFalse($post->softDeleted());

        $post1 = Post::find(1);
        $this->assertInstanceof(Post::class, $post1);
        $this->assertSame('1', $post1->userId);
        $this->assertSame('hello world', $post1->title);
        $this->assertSame('post summary', $post1->summary);
        $this->assertStringContainsString(date('Y-m-d'), $post1->delete_at);

        $post2 = Post::find(2);
        $this->assertInstanceof(Post::class, $post2);
        $this->assertSame('1', $post2->userId);
        $this->assertSame('hello world', $post2->title);
        $this->assertSame('post summary', $post2->summary);
        $this->assertNull($post2->delete_at);
    }

    public function testSoftRestore()
    {
        $connect = $this->createDatabaseConnect();

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
            'user_id' => 1,
            'summary' => 'post summary',
        ]));

        $select = new Select($post = Post::find(1));

        $this->assertInstanceof(Post::class, $post);
        $this->assertSame('1', $post->userId);
        $this->assertSame('hello world', $post->title);
        $this->assertSame('post summary', $post->summary);
        $this->assertNull($post->delete_at);

        $this->assertFalse($post->softDeleted());
        $this->assertSame(1, $select->softDelete());
        $this->assertTrue($post->softDeleted());

        $post1 = Post::find(1);
        $this->assertInstanceof(Post::class, $post1);
        $this->assertSame('1', $post1->userId);
        $this->assertSame('hello world', $post1->title);
        $this->assertSame('post summary', $post1->summary);
        $this->assertStringContainsString(date('Y-m-d'), $post1->delete_at);

        $post2 = Post::find(2);
        $this->assertInstanceof(Post::class, $post2);
        $this->assertSame('1', $post2->userId);
        $this->assertSame('hello world', $post2->title);
        $this->assertSame('post summary', $post2->summary);
        $this->assertNull($post2->delete_at);

        $newSelect = new Select(Post::find(1));
        $this->assertTrue($newSelect->softDeleted());
        $this->assertSame(1, $newSelect->softRestore());
        $this->assertFalse($newSelect->softDeleted());

        $restorePost1 = Post::find(1);
        $this->assertNull($restorePost1->delete_at);
    }

    public function testWithoutSoftDeleted()
    {
        $connect = $this->createDatabaseConnect();

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
            'user_id' => 1,
            'summary' => 'post summary',
        ]));

        $select = new Select($post = Post::find(1));

        $posts = $select->findAll();

        $this->assertInstanceof(Collection::class, $posts);
        $this->assertCount(2, $posts);

        $this->assertFalse($post->softDeleted());
        $this->assertSame(1, $select->softDestroy([1]));
        $this->assertFalse($post->softDeleted());

        $posts = $select->findAll();

        $this->assertInstanceof(Collection::class, $posts);
        $this->assertCount(2, $posts);

        $posts = $select->withoutSoftDeleted()->findAll();

        $this->assertInstanceof(Collection::class, $posts);
        $this->assertCount(1, $posts);
    }

    public function testOnlySoftDeleted()
    {
        $connect = $this->createDatabaseConnect();

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
            'user_id' => 1,
            'summary' => 'post summary',
        ]));

        $select = new Select($post = Post::find(1));

        $posts = $select->findAll();

        $this->assertInstanceof(Collection::class, $posts);
        $this->assertCount(2, $posts);

        $this->assertFalse($post->softDeleted());
        $this->assertSame(1, $select->softDestroy([1]));
        $this->assertFalse($post->softDeleted());

        $posts = $select->findAll();

        $this->assertInstanceof(Collection::class, $posts);
        $this->assertCount(2, $posts);

        $posts = $select->onlySoftDeleted()->findAll();

        $this->assertInstanceof(Collection::class, $posts);
        $this->assertCount(1, $posts);
    }

    public function testDeleteAtColumnNotFound()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Entity `Tests\\Database\\Ddd\\Entity\\Relation\\PostContent` soft delete field `delete_at` was not found.'
        );

        $select = new Select(new PostContent());
        $select->softDeleted();
    }

    public function testScopeBase()
    {
        $connect = $this->createDatabaseConnect();

        for ($i = 0; $i < 10; $i++) {
            $connect->
            table('post')->
            insert([
                'title'   => 'hello world',
                'user_id' => 1,
                'summary' => 'post summary',
            ]);
        }

        $select = new Select($post = Post::find(1));

        $posts = $select->findAll();

        $this->assertInstanceof(Collection::class, $posts);
        $this->assertCount(10, $posts);

        $result1 = Post::test()->findAll();

        $this->assertInstanceof(Collection::class, $result1);
        $this->assertCount(6, $result1);

        $result2 = Post::test2()->findAll();

        $this->assertInstanceof(Collection::class, $result2);
        $this->assertCount(9, $result2);

        $result3 = Post::scope('test,test2')->findAll();

        $this->assertInstanceof(Collection::class, $result3);
        $this->assertCount(5, $result3);

        $result4 = Post::scope(['test', 'test2'])->findAll();

        $this->assertInstanceof(Collection::class, $result4);
        $this->assertCount(5, $result4);
    }

    public function testScopeClosure()
    {
        $connect = $this->createDatabaseConnect();

        for ($i = 0; $i < 10; $i++) {
            $connect->
            table('post')->
            insert([
                'title'   => 'hello world',
                'user_id' => 1,
                'summary' => 'post summary',
            ]);
        }

        $select = new Select($post = Post::find(1));

        $posts = $select->findAll();

        $this->assertInstanceof(Collection::class, $posts);
        $this->assertCount(10, $posts);

        $result1 = Post::scope(function (DatabaseSelect $select) {
            $select->where('id', '>', 5);
        })->findAll();

        $this->assertInstanceof(Collection::class, $result1);
        $this->assertCount(5, $result1);

        $result2 = Post::scope(function (DatabaseSelect $select) {
            $select->where('id', '>', 7);
        })->findAll();

        $this->assertInstanceof(Collection::class, $result2);
        $this->assertCount(3, $result2);
    }

    public function testFindPage()
    {
        $connect = $this->createDatabaseConnect();

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
            'user_id' => 1,
            'summary' => 'post summary',
        ]));

        $this->assertSame('0', $connect->
        table('post_content')->
        insert([
            'post_id' => 1,
            'content' => 'I am content with big data.',
        ]));

        $this->assertSame('0', $connect->
        table('post_content')->
        insert([
            'post_id' => 2,
            'content' => 'I am content with big data2.',
        ]));

        $select = new Select(new Post());
        $select->eager(['post_content']);
        list($page, $posts) = $select->page(1, 10);

        $this->assertInstanceof(Collection::class, $posts);
        $this->assertCount(2, $posts);

        $post1 = $posts[0];
        $this->assertInstanceof(Post::class, $post1);
        $this->assertSame('1', $post1->userId);
        $this->assertSame('hello world', $post1->title);
        $this->assertSame('post summary', $post1->summary);

        $postContent = $post1->postContent;

        $this->assertInstanceof(PostContent::class, $postContent);
        $this->assertSame($post1->id, $postContent->postId);
        $this->assertSame('I am content with big data.', $postContent->content);

        $post2 = $posts[1];
        $this->assertInstanceof(Post::class, $post2);
        $this->assertSame('1', $post2->userId);
        $this->assertSame('hello world', $post2->title);
        $this->assertSame('post summary', $post2->summary);

        $postContent = $post2->postContent;

        $this->assertInstanceof(PostContent::class, $postContent);
        $this->assertSame($post2->id, $postContent->postId);
        $this->assertSame('I am content with big data2.', $postContent->content);

        $sql = <<<'eot'
            {
                "per_page": 10,
                "current_page": 1,
                "total_record": 2,
                "from": 0
            }
            eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $page
            )
        );
    }

    public function testLastSql()
    {
        $connect = $this->createDatabaseConnect();

        $this->assertSame('1', $connect->
        table('post')->
        insert([
            'title'   => 'hello world',
            'user_id' => 1,
            'summary' => 'post summary',
        ]));

        $select = new Select(new Post());
        $post = $select->find(1);

        $this->assertInstanceof(Post::class, $post);
        $this->assertSame('1', $post->id);
        $this->assertSame('1', $post->userId);
        $this->assertSame('hello world', $post->title);
        $this->assertSame('post summary', $post->summary);

        $sql = <<<'eot'
            [
                "SELECT `post`.* FROM `post` WHERE `post`.`id` = 1 LIMIT 1",
                []
            ]
            eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $select->lastSql()
            )
        );
    }

    protected function getDatabaseTable(): array
    {
        return ['post', 'post_content'];
    }
}
