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
use Tests\Database\Ddd\Entity\DemoEntity;
use Tests\Database\Ddd\Entity\EntityWithoutPrimaryKey;
use Tests\Database\Ddd\Entity\Relation\Post;
use Tests\Database\Ddd\Entity\Relation\PostContent;
use Tests\Database\Ddd\Entity\SoftDeleteNotFoundDeleteAtField;

class DeleteTest extends TestCase
{
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
            'Entity Tests\\Database\\Ddd\\Entity\\EntityWithoutPrimaryKey has no primary key.'
        );

        $entity = new EntityWithoutPrimaryKey();
        $entity->delete();
    }

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
        return ['post'];
    }
}
