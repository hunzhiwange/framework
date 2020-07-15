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

namespace Tests\Database\Ddd\Delete;

use Leevel\Database\Ddd\Entity;
use Tests\Database\DatabaseTestCase as TestCase;
use Tests\Database\Ddd\Entity\CompositeId;
use Tests\Database\Ddd\Entity\DemoEntity;
use Tests\Database\Ddd\Entity\EntityWithoutPrimaryKey;
use Tests\Database\Ddd\Entity\Relation\Post;
use Tests\Database\Ddd\Entity\Relation\PostContent;
use Tests\Database\Ddd\Entity\SoftDeleteNotFoundDeleteAtField;

/**
 * @api(
 *     title="删除实体",
 *     path="orm/delete",
 *     description="
 * 将实体从数据库中删除。
 * ",
 * )
 */
class DeleteTest extends TestCase
{
    /**
     * @api(
     *     title="delete 删除一个实体",
     *     description="
     * **完整例子**
     *
     * ``` php
     * $entity = new DemoEntity(['id' => 5]);
     * $entity->delete()->flush();
     * ```
     *
     * 调用 `delete` 方法并没有立刻真正持久化到数据库，这一个步骤计算好了待删除的数据。
     *
     * **完整模型**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Database\Ddd\Entity\DemoEntity::class)]}
     * ```
     * ",
     *     note="通过 delete 方法删除一个实体，并通过 flush 将实体持久化到数据库。",
     * )
     */
    public function testBaseUse(): void
    {
        $entity = new DemoEntity(['id' => 5, 'name' => 'foo']);

        $this->assertInstanceof(Entity::class, $entity);
        $this->assertSame('foo', $entity->name);
        $this->assertSame(['id', 'name'], $entity->changed());
        $this->assertNull($entity->flushData());

        $entity->delete();

        $data = <<<'eot'
            [
                {
                    "id": 5
                }
            ]
            eot;

        $this->assertSame(
            $data,
            $this->varJson(
                $entity->flushData()
            )
        );

        $entity->flush();
    }

    /**
     * @api(
     *     title="softDelete 软删除一个实体",
     *     description="
     * **完整模型**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Database\Ddd\Entity\Relation\Post::class)]}
     * ```
     * ",
     *     note="",
     * )
     */
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

    /**
     * @api(
     *     title="softDestroy 根据主键 ID 软删除实体",
     *     description="",
     *     note="",
     * )
     */
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

    /**
     * @api(
     *     title="destroy 根据主键 ID 删除实体",
     *     description="",
     *     note="",
     * )
     */
    public function testDestroy(): void
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

        Post::destroy([1]);

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

    /**
     * @api(
     *     title="forceDestroy 根据主键 ID 强制删除实体",
     *     description="",
     *     note="",
     * )
     */
    public function testForceDestroy(): void
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

        Post::forceDestroy([1]);

        $post1 = Post::withSoftDeleted()->findEntity(1);
        $this->assertInstanceof(Post::class, $post1);
        $this->assertNull($post1->userId);
        $this->assertNull($post1->title);
        $this->assertNull($post1->summary);
        $this->assertNull($post1->delete_at);

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

    /**
     * @api(
     *     title="softRestore 恢复软删除的实体",
     *     description="",
     *     note="",
     * )
     */
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

    /**
     * @api(
     *     title="delete 删除实体",
     *     description="",
     *     note="",
     * )
     */
    public function testDelete(): void
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
        $post->delete()->flush();
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

    /**
     * @api(
     *     title="delete 复合主键删除实体",
     *     description="",
     *     note="",
     * )
     */
    public function testDeleteForCompositeId(): void
    {
        $connect = $this->createDatabaseConnect();

        $this->assertSame(
            1,
            $connect
                ->table('composite_id')
                ->insert([
                    'id1'  => 1,
                    'id2'  => 2,
                    'name' => 'hello liu',
                ]));

        $entity = CompositeId::select()->where(['id1' => 1, 'id2' => 2])->findOne();

        $this->assertInstanceof(CompositeId::class, $entity);
        $this->assertSame(1, $entity->id1);
        $this->assertSame(2, $entity->id2);
        $this->assertSame('hello liu', $entity->name);

        $entity->delete()->flush();

        $entity = CompositeId::select()->where(['id1' => 1, 'id2' => 2])->findOne();

        $this->assertInstanceof(CompositeId::class, $entity);
        $this->assertNull($entity->id1);
        $this->assertNull($entity->id2);
        $this->assertNull($entity->name);
    }

    /**
     * @api(
     *     title="forceDelete 强制删除实体",
     *     description="",
     *     note="",
     * )
     */
    public function testForceDelete(): void
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
        $post->forceDelete()->flush();
        $this->assertFalse($post->softDeleted());

        $post1 = Post::withSoftDeleted()->findEntity(1);
        $this->assertInstanceof(Post::class, $post1);
        $this->assertNull($post1->userId);
        $this->assertNull($post1->title);
        $this->assertNull($post1->summary);
        $this->assertNull($post1->delete_at);

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

    public function testDeleteWithoutPrimaryKey(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Entity Tests\\Database\\Ddd\\Entity\\EntityWithoutPrimaryKey has no primary key data.'
        );

        $entity = new EntityWithoutPrimaryKey();
        $entity->delete();
    }

    public function testDeleteButPrimaryKeyDataNotFound(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Entity Tests\\Database\\Ddd\\Entity\\Relation\\Post has no primary key data.'
        );

        $entity = new Post();
        $entity->delete(true);
    }

    public function testForceDeleteButPrimaryKeyDataNotFound(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Entity Tests\\Database\\Ddd\\Entity\\Relation\\Post has no primary key data.'
        );

        $entity = new Post();
        $entity->forceDelete();
    }

    public function testSelectWithNotSupportSoftDeletedType(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Invalid soft deleted type 9999.'
        );

        Post::select(9999);
    }

    protected function getDatabaseTable(): array
    {
        return ['post', 'composite_id'];
    }
}
