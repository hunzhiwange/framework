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
 * (c) 2010-2020 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Database\Ddd;

use Exception;
use Leevel\Collection\Collection;
use Leevel\Database\Ddd\Entity;
use Leevel\Database\Ddd\Select;
use Leevel\Database\Page;
use Tests\Database\DatabaseTestCase as TestCase;
use Tests\Database\Ddd\Entity\CompositeId;
use Tests\Database\Ddd\Entity\Relation\Post;

/**
 * @api(
 *     title="实体查询",
 *     path="orm/select",
 *     description="
 * 在设计实体的时候，我们是这样想的，查询不属于实体的一部分而应该是独立的，所以实体查询被抽象出来了。
 * ",
 * )
 */
class SelectTest extends TestCase
{
    /**
     * @api(
     *     title="基本使用方法",
     *     description="",
     *     note="",
     * )
     */
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
        $this->assertInstanceof(Entity::class, $entity);
    }

    /**
     * @api(
     *     title="findEntity 通过主键查找实体",
     *     description="",
     *     note="",
     * )
     */
    public function testFindEntity(): void
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

    /**
     * @api(
     *     title="复合主键请使用 where 条件查询",
     *     description="",
     *     note="",
     * )
     */
    public function testFindEntityForCompositeId(): void
    {
        $connect = $this->createDatabaseConnect();

        $this->assertSame(
            1,
            $connect
                ->table('composite_id')
                ->insert([
                    'id1'     => 1,
                    'id2'     => 2,
                    'name'    => 'hello liu',
                ]));

        $select = new Select(new CompositeId());
        $entity = $select->where(['id1' => 1, 'id2' => 2])->findOne();
        $this->assertInstanceof(CompositeId::class, $entity);
        $this->assertSame(1, $entity->id1);
        $this->assertSame(2, $entity->id2);
        $this->assertSame('hello liu', $entity->name);
    }

    /**
     * @api(
     *     title="findOrFail 通过主键查找实体，未找到则抛出异常",
     *     description="",
     *     note="",
     * )
     */
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

    /**
     * @api(
     *     title="findOrFail 通过主键查找实体，未找到则抛出异常例子",
     *     description="",
     *     note="",
     * )
     */
    public function testFindOrFailThrowsException(): void
    {
        $this->expectException(\Leevel\Database\Ddd\EntityNotFoundException::class);
        $this->expectExceptionMessage(
            'Entity `Tests\\Database\\Ddd\\Entity\\Relation\\Post` was not found.'
        );

        $select = new Select(new Post());
        $post = $select->findOrFail(1);
    }

    /**
     * @api(
     *     title="findMany 通过主键查找多个实体",
     *     description="",
     *     note="",
     * )
     */
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

    /**
     * @api(
     *     title="findMany 通过主键查找多个实体未找到数据返回空集合",
     *     description="",
     *     note="",
     * )
     */
    public function testFindManyWithoutResults(): void
    {
        $select = new Select(new Post());
        $posts = $select->findMany([1, 2]);

        $this->assertInstanceof(Collection::class, $posts);
        $this->assertCount(0, $posts);
    }

    /**
     * @api(
     *     title="实体查询默认不带软删除数据",
     *     description="",
     *     note="",
     * )
     */
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
            SQL: [70] SELECT `post`.* FROM `post` WHERE `post`.`delete_at` = :post_delete_at | Params:  1 | Key: Name: [15] :post_delete_at | paramno=0 | name=[15] ":post_delete_at" | is_param=1 | param_type=1 (SELECT `post`.* FROM `post` WHERE `post`.`delete_at` = 0)
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
            SQL: [72] SELECT `post`.* FROM `post` WHERE `post`.`delete_at` = :post_delete_at_1 | Params:  1 | Key: Name: [17] :post_delete_at_1 | paramno=0 | name=[17] ":post_delete_at_1" | is_param=1 | param_type=1 (SELECT `post`.* FROM `post` WHERE `post`.`delete_at` = 0)
            eot;
        $this->assertSame(
            $sql,
            $select->getLastSql(),
        );

        $this->assertInstanceof(Collection::class, $posts);
        $this->assertCount(1, $posts);

        $posts = Post::select()->findAll();
        $sql = <<<'eot'
            SQL: [70] SELECT `post`.* FROM `post` WHERE `post`.`delete_at` = :post_delete_at | Params:  1 | Key: Name: [15] :post_delete_at | paramno=0 | name=[15] ":post_delete_at" | is_param=1 | param_type=1 (SELECT `post`.* FROM `post` WHERE `post`.`delete_at` = 0)
            eot;
        $this->assertSame(
            $sql,
            $select->getLastSql(),
        );

        $this->assertInstanceof(Collection::class, $posts);
        $this->assertCount(1, $posts);
    }

    /**
     * @api(
     *     title="withSoftDeleted 包含软删除数据的实体查询对象（实体发起）",
     *     description="",
     *     note="",
     * )
     */
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
            SQL: [70] SELECT `post`.* FROM `post` WHERE `post`.`delete_at` = :post_delete_at | Params:  1 | Key: Name: [15] :post_delete_at | paramno=0 | name=[15] ":post_delete_at" | is_param=1 | param_type=1 (SELECT `post`.* FROM `post` WHERE `post`.`delete_at` = 0)
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
            SQL: [72] SELECT `post`.* FROM `post` WHERE `post`.`delete_at` = :post_delete_at_1 | Params:  1 | Key: Name: [17] :post_delete_at_1 | paramno=0 | name=[17] ":post_delete_at_1" | is_param=1 | param_type=1 (SELECT `post`.* FROM `post` WHERE `post`.`delete_at` = 0)
            eot;
        $this->assertSame(
            $sql,
            $select->getLastSql(),
        );

        $this->assertInstanceof(Collection::class, $posts);
        $this->assertCount(1, $posts);

        $posts = Post::withSoftDeleted()->findAll();
        $sql = <<<'eot'
            SQL: [27] SELECT `post`.* FROM `post` | Params:  0 (SELECT `post`.* FROM `post`)
            eot;
        $this->assertSame(
            $sql,
            $select->getLastSql(),
        );

        $this->assertInstanceof(Collection::class, $posts);
        $this->assertCount(2, $posts);
    }

    /**
     * @api(
     *     title="onlySoftDeleted 仅仅包含软删除数据的实体查询对象（实体发起）",
     *     description="",
     *     note="",
     * )
     */
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
            SQL: [70] SELECT `post`.* FROM `post` WHERE `post`.`delete_at` = :post_delete_at | Params:  1 | Key: Name: [15] :post_delete_at | paramno=0 | name=[15] ":post_delete_at" | is_param=1 | param_type=1 (SELECT `post`.* FROM `post` WHERE `post`.`delete_at` = 0)
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
            SQL: [72] SELECT `post`.* FROM `post` WHERE `post`.`delete_at` = :post_delete_at_1 | Params:  1 | Key: Name: [17] :post_delete_at_1 | paramno=0 | name=[17] ":post_delete_at_1" | is_param=1 | param_type=1 (SELECT `post`.* FROM `post` WHERE `post`.`delete_at` = 0)
            eot;
        $this->assertSame(
            $sql,
            $select->getLastSql(),
        );

        $this->assertInstanceof(Collection::class, $posts);
        $this->assertCount(1, $posts);

        $posts = Post::onlySoftDeleted()->findAll();
        $sql = <<<'eot'
            SQL: [70] SELECT `post`.* FROM `post` WHERE `post`.`delete_at` > :post_delete_at | Params:  1 | Key: Name: [15] :post_delete_at | paramno=0 | name=[15] ":post_delete_at" | is_param=1 | param_type=1 (SELECT `post`.* FROM `post` WHERE `post`.`delete_at` > 0)
            eot;
        $this->assertSame(
            $sql,
            $select->getLastSql(),
        );

        $this->assertInstanceof(Collection::class, $posts);
        $this->assertCount(1, $posts);
    }

    /**
     * @api(
     *     title="onlySoftDeleted 包含软删除数据的实体查询对象（实体查询发起）",
     *     description="",
     *     note="",
     * )
     */
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
            SQL: [70] SELECT `post`.* FROM `post` WHERE `post`.`delete_at` = :post_delete_at | Params:  1 | Key: Name: [15] :post_delete_at | paramno=0 | name=[15] ":post_delete_at" | is_param=1 | param_type=1 (SELECT `post`.* FROM `post` WHERE `post`.`delete_at` = 0)
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
            SQL: [72] SELECT `post`.* FROM `post` WHERE `post`.`delete_at` = :post_delete_at_1 | Params:  1 | Key: Name: [17] :post_delete_at_1 | paramno=0 | name=[17] ":post_delete_at_1" | is_param=1 | param_type=1 (SELECT `post`.* FROM `post` WHERE `post`.`delete_at` = 0)
            eot;
        $this->assertSame(
            $sql,
            $select->getLastSql(),
        );

        $this->assertInstanceof(Collection::class, $posts);
        $this->assertCount(1, $posts);

        $posts = $select->withSoftDeleted()->findAll();
        $sql = <<<'eot'
            SQL: [27] SELECT `post`.* FROM `post` | Params:  0 (SELECT `post`.* FROM `post`)
            eot;
        $this->assertSame(
            $sql,
            $select->getLastSql(),
        );

        $this->assertInstanceof(Collection::class, $posts);
        $this->assertCount(2, $posts);
    }

    /**
     * @api(
     *     title="onlySoftDeleted 仅仅包含软删除数据的实体查询对象（实体查询发起）",
     *     description="",
     *     note="",
     * )
     */
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
            SQL: [70] SELECT `post`.* FROM `post` WHERE `post`.`delete_at` = :post_delete_at | Params:  1 | Key: Name: [15] :post_delete_at | paramno=0 | name=[15] ":post_delete_at" | is_param=1 | param_type=1 (SELECT `post`.* FROM `post` WHERE `post`.`delete_at` = 0)
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
            SQL: [72] SELECT `post`.* FROM `post` WHERE `post`.`delete_at` = :post_delete_at_1 | Params:  1 | Key: Name: [17] :post_delete_at_1 | paramno=0 | name=[17] ":post_delete_at_1" | is_param=1 | param_type=1 (SELECT `post`.* FROM `post` WHERE `post`.`delete_at` = 0)
            eot;
        $this->assertSame(
            $sql,
            $select->getLastSql(),
        );

        $this->assertInstanceof(Collection::class, $posts);
        $this->assertCount(1, $posts);

        $posts = $select->onlySoftDeleted()->findAll();
        $sql = <<<'eot'
            SQL: [70] SELECT `post`.* FROM `post` WHERE `post`.`delete_at` > :post_delete_at | Params:  1 | Key: Name: [15] :post_delete_at | paramno=0 | name=[15] ":post_delete_at" | is_param=1 | param_type=1 (SELECT `post`.* FROM `post` WHERE `post`.`delete_at` > 0)
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
            SQL: [97] SELECT `post`.* FROM `post` WHERE `post`.`delete_at` = :post_delete_at AND `post`.`id` > :post_id | Params:  2 | Key: Name: [15] :post_delete_at | paramno=0 | name=[15] ":post_delete_at" | is_param=1 | param_type=1 | Key: Name: [8] :post_id | paramno=1 | name=[8] ":post_id" | is_param=1 | param_type=1 (SELECT `post`.* FROM `post` WHERE `post`.`delete_at` = 0 AND `post`.`id` > 1)
            eot;
        $this->assertSame(
            $sql,
            $select->getLastSql(),
        );

        $this->assertInstanceof(Collection::class, $posts);
        $this->assertCount(1, $posts);

        $posts = $select->where('id', '>', 2)->findAll();
        $sql = <<<'eot'
            SQL: [130] SELECT `post`.* FROM `post` WHERE `post`.`delete_at` = :post_delete_at_1 AND `post`.`id` > :post_id_1 AND `post`.`id` > :post_id_2 | Params:  3 | Key: Name: [17] :post_delete_at_1 | paramno=0 | name=[17] ":post_delete_at_1" | is_param=1 | param_type=1 | Key: Name: [10] :post_id_1 | paramno=1 | name=[10] ":post_id_1" | is_param=1 | param_type=1 | Key: Name: [10] :post_id_2 | paramno=2 | name=[10] ":post_id_2" | is_param=1 | param_type=1 (SELECT `post`.* FROM `post` WHERE `post`.`delete_at` = 0 AND `post`.`id` > 1 AND `post`.`id` > 2)
            eot;
        $this->assertSame(
            $sql,
            $select->getLastSql(),
        );

        $this->assertInstanceof(Collection::class, $posts);
        $this->assertCount(0, $posts);

        $posts = $select->withSoftDeleted()->findAll();
        $sql = <<<'eot'
            SQL: [27] SELECT `post`.* FROM `post` | Params:  0 (SELECT `post`.* FROM `post`)
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
            SQL: [97] SELECT `post`.* FROM `post` WHERE `post`.`delete_at` = :post_delete_at AND `post`.`id` > :post_id | Params:  2 | Key: Name: [15] :post_delete_at | paramno=0 | name=[15] ":post_delete_at" | is_param=1 | param_type=1 | Key: Name: [8] :post_id | paramno=1 | name=[8] ":post_id" | is_param=1 | param_type=1 (SELECT `post`.* FROM `post` WHERE `post`.`delete_at` = 0 AND `post`.`id` > 1)
            eot;
        $this->assertSame(
            $sql,
            $select->getLastSql(),
        );

        $this->assertInstanceof(Collection::class, $posts);
        $this->assertCount(1, $posts);

        $posts = $select->where('id', '>', 2)->findAll();
        $sql = <<<'eot'
            SQL: [130] SELECT `post`.* FROM `post` WHERE `post`.`delete_at` = :post_delete_at_1 AND `post`.`id` > :post_id_1 AND `post`.`id` > :post_id_2 | Params:  3 | Key: Name: [17] :post_delete_at_1 | paramno=0 | name=[17] ":post_delete_at_1" | is_param=1 | param_type=1 | Key: Name: [10] :post_id_1 | paramno=1 | name=[10] ":post_id_1" | is_param=1 | param_type=1 | Key: Name: [10] :post_id_2 | paramno=2 | name=[10] ":post_id_2" | is_param=1 | param_type=1 (SELECT `post`.* FROM `post` WHERE `post`.`delete_at` = 0 AND `post`.`id` > 1 AND `post`.`id` > 2)
            eot;
        $this->assertSame(
            $sql,
            $select->getLastSql(),
        );

        $this->assertInstanceof(Collection::class, $posts);
        $this->assertCount(0, $posts);

        $posts = $select->onlySoftDeleted()->findAll();
        $sql = <<<'eot'
            SQL: [70] SELECT `post`.* FROM `post` WHERE `post`.`delete_at` > :post_delete_at | Params:  1 | Key: Name: [15] :post_delete_at | paramno=0 | name=[15] ":post_delete_at" | is_param=1 | param_type=1 (SELECT `post`.* FROM `post` WHERE `post`.`delete_at` > 0)
            eot;
        $this->assertSame(
            $sql,
            $select->getLastSql(),
        );

        $this->assertInstanceof(Collection::class, $posts);
        $this->assertCount(0, $posts);
    }

    /**
     * @api(
     *     title="getLastSql 获取最近一次查询的 SQL 语句",
     *     description="",
     *     note="",
     * )
     */
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
            SQL: [105] SELECT `post`.* FROM `post` WHERE `post`.`delete_at` = :post_delete_at AND `post`.`id` = :post_id LIMIT 1 | Params:  2 | Key: Name: [15] :post_delete_at | paramno=0 | name=[15] ":post_delete_at" | is_param=1 | param_type=1 | Key: Name: [8] :post_id | paramno=1 | name=[8] ":post_id" | is_param=1 | param_type=1 (SELECT `post`.* FROM `post` WHERE `post`.`delete_at` = 0 AND `post`.`id` = 1 LIMIT 1)
            eot;

        $this->assertSame(
            $sql,
            $select->databaseConnect()->getLastSql(),
        );
    }

    /**
     * @api(
     *     title="withoutPreLoadsResult 获取不执行预载入的查询结果",
     *     description="",
     *     note="",
     * )
     */
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

    /**
     * @api(
     *     title="eager 添加预载入关联查询",
     *     description="",
     *     note="",
     * )
     */
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
            SQL: [63] SELECT `user`.* FROM `user` WHERE `user`.`id` IN (:user_id_in0) | Params:  1 | Key: Name: [12] :user_id_in0 | paramno=0 | name=[12] ":user_id_in0" | is_param=1 | param_type=1 (SELECT `user`.* FROM `user` WHERE `user`.`id` IN (1))
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
        return ['post', 'post_content', 'composite_id'];
    }
}
