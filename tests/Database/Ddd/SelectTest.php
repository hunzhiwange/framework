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

use Exception;
use Leevel\Collection\Collection;
use Leevel\Database\Ddd\IEntity;
use Leevel\Database\Ddd\Select;
use Leevel\Database\Page;
use Tests\Database\DatabaseTestCase as TestCase;
use Tests\Database\Ddd\Entity\Relation\Post;

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
    public function testBase(): void
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

        $select = new Select(new Post());
        $post = $select->findEntity(1);
        $entity = $select->entity();

        $this->assertInstanceof(Post::class, $post);
        $this->assertSame(1, $post->id);
        $this->assertSame(1, $post->userId);
        $this->assertSame('hello world', $post->title);
        $this->assertSame('post summary', $post->summary);
        $this->assertInstanceof(IEntity::class, $entity);
    }

    public function testFind(): void
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

        $select = new Select(new Post());
        $post = $select->findEntity(1);

        $this->assertInstanceof(Post::class, $post);
        $this->assertSame(1, $post->id);
        $this->assertSame(1, $post->userId);
        $this->assertSame('hello world', $post->title);
        $this->assertSame('post summary', $post->summary);
    }

    public function testFindOrFail(): void
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

        $select = new Select(new Post());
        $post = $select->findOrFail(1);

        $this->assertInstanceof(Post::class, $post);
        $this->assertSame(1, $post->id);
        $this->assertSame(1, $post->userId);
        $this->assertSame('hello world', $post->title);
        $this->assertSame('post summary', $post->summary);
    }

    public function testFindOrFailThrowsException(): void
    {
        $this->expectException(\Leevel\Database\Ddd\EntityNotFoundException::class);
        $this->expectExceptionMessage(
            'Entity `Tests\\Database\\Ddd\\Entity\\Relation\\Post` was not found.'
        );

        $select = new Select(new Post());
        $post = $select->findOrFail(1);
    }

    public function testFindMany(): void
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

        $select = new Select(new Post());
        $posts = $select->findMany([1, 2]);

        $this->assertInstanceof(Collection::class, $posts);
        $this->assertCount(2, $posts);

        $post1 = $posts[0];
        $this->assertInstanceof(Post::class, $post1);
        $this->assertSame(1, $post1->userId);
        $this->assertSame('hello world', $post1->title);
        $this->assertSame('post summary', $post1->summary);

        $post2 = $posts[1];
        $this->assertInstanceof(Post::class, $post2);
        $this->assertSame(1, $post2->userId);
        $this->assertSame('hello world', $post2->title);
        $this->assertSame('post summary', $post2->summary);
    }

    public function testFindManyWithEmptyIds(): void
    {
        $select = new Select(new Post());
        $posts = $select->findMany([]);

        $this->assertInstanceof(Collection::class, $posts);
        $this->assertCount(0, $posts);
    }

    public function testFindManyWithoutResults(): void
    {
        $select = new Select(new Post());
        $posts = $select->findMany([1, 2]);

        $this->assertInstanceof(Collection::class, $posts);
        $this->assertCount(0, $posts);
    }

    public function testEntityDefaultWithoutSoftDeleted(): void
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

        $select = new Select($post = Post::select()->findEntity(1));
        $posts = $select->findAll();
        $sql = <<<'eot'
            SQL: [56] SELECT `post`.* FROM `post` WHERE `post`.`delete_at` = 0 | Params:  0
            eot;
        $this->assertSame(
            $sql,
            $select->getLastSql(),
        );

        $this->assertInstanceof(Collection::class, $posts);
        $this->assertCount(2, $posts);

        $this->assertFalse($post->softDeleted());
        $this->assertSame(1, Post::softDestroy([1]));
        $this->assertFalse($post->softDeleted());

        $posts = $select->findAll();
        $sql = <<<'eot'
            SQL: [56] SELECT `post`.* FROM `post` WHERE `post`.`delete_at` = 0 | Params:  0
            eot;
        $this->assertSame(
            $sql,
            $select->getLastSql(),
        );

        $this->assertInstanceof(Collection::class, $posts);
        $this->assertCount(1, $posts);

        $posts = Post::select()->findAll();
        $sql = <<<'eot'
            SQL: [56] SELECT `post`.* FROM `post` WHERE `post`.`delete_at` = 0 | Params:  0
            eot;
        $this->assertSame(
            $sql,
            $select->getLastSql(),
        );

        $this->assertInstanceof(Collection::class, $posts);
        $this->assertCount(1, $posts);
    }

    public function testEntityWithSoftDeleted(): void
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

        $select = new Select($post = Post::select()->findEntity(1));
        $posts = $select->findAll();
        $sql = <<<'eot'
            SQL: [56] SELECT `post`.* FROM `post` WHERE `post`.`delete_at` = 0 | Params:  0
            eot;
        $this->assertSame(
            $sql,
            $select->getLastSql(),
        );

        $this->assertInstanceof(Collection::class, $posts);
        $this->assertCount(2, $posts);

        $this->assertFalse($post->softDeleted());
        $this->assertSame(1, Post::softDestroy([1]));
        $this->assertFalse($post->softDeleted());

        $posts = $select->findAll();
        $sql = <<<'eot'
            SQL: [56] SELECT `post`.* FROM `post` WHERE `post`.`delete_at` = 0 | Params:  0
            eot;
        $this->assertSame(
            $sql,
            $select->getLastSql(),
        );

        $this->assertInstanceof(Collection::class, $posts);
        $this->assertCount(1, $posts);

        $posts = Post::withSoftDeleted()->findAll();
        $sql = <<<'eot'
            SQL: [27] SELECT `post`.* FROM `post` | Params:  0
            eot;
        $this->assertSame(
            $sql,
            $select->getLastSql(),
        );

        $this->assertInstanceof(Collection::class, $posts);
        $this->assertCount(2, $posts);
    }

    public function testEntityOnlySoftDeleted(): void
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

        $select = new Select($post = Post::select()->findEntity(1));
        $posts = $select->findAll();
        $sql = <<<'eot'
            SQL: [56] SELECT `post`.* FROM `post` WHERE `post`.`delete_at` = 0 | Params:  0
            eot;
        $this->assertSame(
            $sql,
            $select->getLastSql(),
        );

        $this->assertInstanceof(Collection::class, $posts);
        $this->assertCount(2, $posts);

        $this->assertFalse($post->softDeleted());
        $this->assertSame(1, Post::softDestroy([1]));
        $this->assertFalse($post->softDeleted());

        $posts = $select->findAll();
        $sql = <<<'eot'
            SQL: [56] SELECT `post`.* FROM `post` WHERE `post`.`delete_at` = 0 | Params:  0
            eot;
        $this->assertSame(
            $sql,
            $select->getLastSql(),
        );

        $this->assertInstanceof(Collection::class, $posts);
        $this->assertCount(1, $posts);

        $posts = Post::onlySoftDeleted()->findAll();
        $sql = <<<'eot'
            SQL: [56] SELECT `post`.* FROM `post` WHERE `post`.`delete_at` > 0 | Params:  0
            eot;
        $this->assertSame(
            $sql,
            $select->getLastSql(),
        );

        $this->assertInstanceof(Collection::class, $posts);
        $this->assertCount(1, $posts);
    }

    public function testWithSoftDeleted(): void
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

        $select = new Select($post = Post::select()->findEntity(1));
        $posts = $select->findAll();
        $sql = <<<'eot'
            SQL: [56] SELECT `post`.* FROM `post` WHERE `post`.`delete_at` = 0 | Params:  0
            eot;
        $this->assertSame(
            $sql,
            $select->getLastSql(),
        );

        $this->assertInstanceof(Collection::class, $posts);
        $this->assertCount(2, $posts);

        $this->assertFalse($post->softDeleted());
        $this->assertSame(1, Post::softDestroy([1]));
        $this->assertFalse($post->softDeleted());

        $posts = $select->findAll();
        $sql = <<<'eot'
            SQL: [56] SELECT `post`.* FROM `post` WHERE `post`.`delete_at` = 0 | Params:  0
            eot;
        $this->assertSame(
            $sql,
            $select->getLastSql(),
        );

        $this->assertInstanceof(Collection::class, $posts);
        $this->assertCount(1, $posts);

        $posts = $select->withSoftDeleted()->findAll();
        $sql = <<<'eot'
            SQL: [27] SELECT `post`.* FROM `post` | Params:  0
            eot;
        $this->assertSame(
            $sql,
            $select->getLastSql(),
        );

        $this->assertInstanceof(Collection::class, $posts);
        $this->assertCount(2, $posts);
    }

    public function testOnlySoftDeleted(): void
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

        $select = new Select($post = Post::select()->findEntity(1));
        $posts = $select->findAll();
        $sql = <<<'eot'
            SQL: [56] SELECT `post`.* FROM `post` WHERE `post`.`delete_at` = 0 | Params:  0
            eot;
        $this->assertSame(
            $sql,
            $select->getLastSql(),
        );

        $this->assertInstanceof(Collection::class, $posts);
        $this->assertCount(2, $posts);

        $this->assertFalse($post->softDeleted());
        $this->assertSame(1, Post::softDestroy([1]));
        $this->assertFalse($post->softDeleted());

        $posts = $select->findAll();
        $sql = <<<'eot'
            SQL: [56] SELECT `post`.* FROM `post` WHERE `post`.`delete_at` = 0 | Params:  0
            eot;
        $this->assertSame(
            $sql,
            $select->getLastSql(),
        );

        $this->assertInstanceof(Collection::class, $posts);
        $this->assertCount(1, $posts);

        $posts = $select->onlySoftDeleted()->findAll();
        $sql = <<<'eot'
            SQL: [56] SELECT `post`.* FROM `post` WHERE `post`.`delete_at` > 0 | Params:  0
            eot;
        $this->assertSame(
            $sql,
            $select->getLastSql(),
        );

        $this->assertInstanceof(Collection::class, $posts);
        $this->assertCount(1, $posts);
    }

    public function testWithSoftDeletedWillInitSelect(): void
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

        $select = new Select($post = Post::select()->findEntity(1));
        $posts = $select->where('id', '>', 1)->findAll();
        $sql = <<<'eot'
            SQL: [76] SELECT `post`.* FROM `post` WHERE `post`.`delete_at` = 0 AND `post`.`id` > 1 | Params:  0
            eot;
        $this->assertSame(
            $sql,
            $select->getLastSql(),
        );

        $this->assertInstanceof(Collection::class, $posts);
        $this->assertCount(1, $posts);

        $posts = $select->where('id', '>', 2)->findAll();
        $sql = <<<'eot'
            SQL: [96] SELECT `post`.* FROM `post` WHERE `post`.`delete_at` = 0 AND `post`.`id` > 1 AND `post`.`id` > 2 | Params:  0
            eot;
        $this->assertSame(
            $sql,
            $select->getLastSql(),
        );

        $this->assertInstanceof(Collection::class, $posts);
        $this->assertCount(0, $posts);

        $posts = $select->withSoftDeleted()->findAll();
        $sql = <<<'eot'
            SQL: [27] SELECT `post`.* FROM `post` | Params:  0
            eot;
        $this->assertSame(
            $sql,
            $select->getLastSql(),
        );

        $this->assertInstanceof(Collection::class, $posts);
        $this->assertCount(2, $posts);
    }

    public function testOnlySoftDeletedWillInitSelect(): void
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

        $select = new Select($post = Post::select()->findEntity(1));
        $posts = $select->where('id', '>', 1)->findAll();
        $sql = <<<'eot'
            SQL: [76] SELECT `post`.* FROM `post` WHERE `post`.`delete_at` = 0 AND `post`.`id` > 1 | Params:  0
            eot;
        $this->assertSame(
            $sql,
            $select->getLastSql(),
        );

        $this->assertInstanceof(Collection::class, $posts);
        $this->assertCount(1, $posts);

        $posts = $select->where('id', '>', 2)->findAll();
        $sql = <<<'eot'
            SQL: [96] SELECT `post`.* FROM `post` WHERE `post`.`delete_at` = 0 AND `post`.`id` > 1 AND `post`.`id` > 2 | Params:  0
            eot;
        $this->assertSame(
            $sql,
            $select->getLastSql(),
        );

        $this->assertInstanceof(Collection::class, $posts);
        $this->assertCount(0, $posts);

        $posts = $select->onlySoftDeleted()->findAll();
        $sql = <<<'eot'
            SQL: [56] SELECT `post`.* FROM `post` WHERE `post`.`delete_at` > 0 | Params:  0
            eot;
        $this->assertSame(
            $sql,
            $select->getLastSql(),
        );

        $this->assertInstanceof(Collection::class, $posts);
        $this->assertCount(0, $posts);
    }

    public function testLastSql(): void
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

        $select = new Select(new Post());
        $post = $select->findEntity(1);

        $this->assertInstanceof(Post::class, $post);
        $this->assertSame(1, $post->id);
        $this->assertSame(1, $post->userId);
        $this->assertSame('hello world', $post->title);
        $this->assertSame('post summary', $post->summary);

        $sql = <<<'eot'
            SQL: [84] SELECT `post`.* FROM `post` WHERE `post`.`delete_at` = 0 AND `post`.`id` = 1 LIMIT 1 | Params:  0
            eot;

        $this->assertSame(
            $sql,
            $select->databaseConnect()->getLastSql(),
        );
    }

    public function testWithoutPreLoadsResult(): void
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

        $select = new Select(new Post());
        $post = Select::withoutPreLoadsResult(function () use ($select) {
            return $select->findEntity(1);
        });

        $this->assertInstanceof(Post::class, $post);
        $this->assertSame(1, $post->id);
        $this->assertSame(1, $post->userId);
        $this->assertSame('hello world', $post->title);
        $this->assertSame('post summary', $post->summary);
    }

    public function testWithoutPreLoadsResultWithException(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage(
            'test exception'
        );

        Select::withoutPreLoadsResult(function () {
            throw new Exception('test exception');
        });
    }

    public function testPreLoadPage(): void
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

        $select = new Select($post = Post::select()->findEntity(1));
        $select->eager(['user']);
        $page = $select->page(1, 10);
        $sql = <<<'eot'
            SQL: [52] SELECT `user`.* FROM `user` WHERE `user`.`id` IN (1) | Params:  0
            eot;
        $this->assertSame(
            $sql,
            $select->getLastSql(),
        );

        $this->assertInstanceof(Page::class, $page);
        $data = $page->getData();
        $this->assertCount(2, $data);
        $this->assertInstanceof(Collection::class, $data);
        $pageData = $page->toArray();
        $this->assertCount(2, $pageData['data']);
        $this->assertInstanceof(Collection::class, $pageData['data']);

        $data = <<<'eot'
            {
                "per_page": 10,
                "current_page": 1,
                "total_page": 1,
                "total_record": 2,
                "total_macro": false,
                "from": 0,
                "to": 2
            }
            eot;

        $this->assertSame(
            $data,
            $this->varJson(
                $pageData['page']
            )
        );
    }

    protected function getDatabaseTable(): array
    {
        return ['post', 'post_content'];
    }
}
