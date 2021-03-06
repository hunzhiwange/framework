<?php

declare(strict_types=1);

namespace Tests\Database\Ddd\Relation;

use Leevel\Collection\Collection;
use Leevel\Database\Ddd\Relation\BelongsTo;
use Leevel\Database\Ddd\Relation\Relation;
use Leevel\Database\Ddd\Select;
use Tests\Database\DatabaseTestCase as TestCase;
use Tests\Database\Ddd\Entity\Relation\Post;
use Tests\Database\Ddd\Entity\Relation\User;

/**
 * @api(
 *     zh-CN:title="belongsTo 从属关联",
 *     path="orm/belongsto",
 *     zh-CN:description="
 * 从属关联也是一对一的关联的一种，比如一篇文章属于某个用户发表。
 *
 * **从属关联支持类型关联项**
 *
 * |  关联项   | 说明  |    例子   |
 * |  ----  | ----  | ----  |
 * | \Leevel\Database\Ddd\Entity::BELONGS_TO  | 从属关联实体 |  \Tests\Database\Ddd\Entity\Relation\User::class  |
 * | \Leevel\Database\Ddd\Entity::SOURCE_KEY  | 关联查询源键字段 | user_id |
 * | \Leevel\Database\Ddd\Entity::TARGET_KEY  | 关联目标键字段 | id |
 * | \Leevel\Database\Ddd\Entity::RELATION_SCOPE  | 关联查询作用域 | foo |
 * ",
 * )
 */
class BelongsToTest extends TestCase
{
    /**
     * @api(
     *     zh-CN:title="基本使用方法",
     *     zh-CN:description="
     * **fixture 定义**
     *
     * **Tests\Database\Ddd\Entity\Relation\Post**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Database\Ddd\Entity\Relation\Post::class)]}
     * ```
     *
     * **Tests\Database\Ddd\Entity\Relation\User**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Database\Ddd\Entity\Relation\User::class)]}
     * ```
     * ",
     *     zh-CN:note="",
     * )
     */
    public function testBaseUse(): void
    {
        $post = Post::select()->where('id', 1)->findOne();

        $this->assertInstanceof(Post::class, $post);
        $this->assertNull($post->id);

        $connect = $this->createDatabaseConnect();

        $this->assertSame(
            1,
            $connect
                ->table('post')
                ->insert([
                    'title'     => 'hello world',
                    'user_id'   => 1,
                    'summary'   => 'Say hello to the world.',
                    'delete_at' => 0,
                ]),
        );

        $this->assertSame(
            1,
            $connect
                ->table('user')
                ->insert([
                    'name' => 'niu',
                ]),
        );

        $post = Post::select()->where('id', 1)->findOne();

        $this->assertSame(1, $post->id);
        $this->assertSame(1, $post['id']);
        $this->assertSame(1, $post->getId());
        $this->assertSame(1, $post->user_id);
        $this->assertSame(1, $post->userId);
        $this->assertSame(1, $post['user_id']);
        $this->assertSame(1, $post->getUserId());
        $this->assertSame('hello world', $post->title);
        $this->assertSame('hello world', $post['title']);
        $this->assertSame('hello world', $post->getTitle());
        $this->assertSame('Say hello to the world.', $post->summary);
        $this->assertSame('Say hello to the world.', $post['summary']);
        $this->assertSame('Say hello to the world.', $post->getSummary());

        $user = $post->user;

        $this->assertInstanceof(User::class, $user);
        $this->assertSame(1, $user->id);
        $this->assertSame(1, $user['id']);
        $this->assertSame(1, $user->getId());
        $this->assertSame('niu', $user->name);
        $this->assertSame('niu', $user['name']);
        $this->assertSame('niu', $user->getName());
    }

    /**
     * @api(
     *     zh-CN:title="eager 预加载关联",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testEager(): void
    {
        $posts = Post::select()->limit(5)->findAll();

        $this->assertInstanceof(Collection::class, $posts);
        $this->assertCount(0, $posts);

        $connect = $this->createDatabaseConnect();

        for ($i = 0; $i <= 5; $i++) {
            $this->assertSame(
                $i + 1,
                $connect
                    ->table('post')
                    ->insert([
                        'title'     => 'hello world',
                        'user_id'   => 1,
                        'summary'   => 'Say hello to the world.',
                        'delete_at' => 0,
                    ]),
            );
        }

        $this->assertSame(
            1,
            $connect
                ->table('user')
                ->insert([
                    'name' => 'niu',
                ]),
        );

        $posts = Post::eager(['user'])
            ->limit(5)
            ->findAll();

        $this->assertInstanceof(Collection::class, $posts);
        $this->assertCount(5, $posts);

        foreach ($posts as $value) {
            $user = $value->user;

            $this->assertInstanceof(User::class, $user);
            $this->assertSame(1, $user->id);
            $this->assertSame('niu', $user->name);
        }
    }

    /**
     * @api(
     *     zh-CN:title="eager 预加载关联支持查询条件过滤",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testEagerWithCondition(): void
    {
        $posts = Post::select()->limit(5)->findAll();

        $this->assertInstanceof(Collection::class, $posts);
        $this->assertCount(0, $posts);

        $connect = $this->createDatabaseConnect();

        for ($i = 0; $i <= 5; $i++) {
            $this->assertSame(
                $i + 1,
                $connect
                    ->table('post')
                    ->insert([
                        'title'     => 'hello world',
                        'user_id'   => 1,
                        'summary'   => 'Say hello to the world.',
                        'delete_at' => 0,
                    ]),
            );
        }

        $this->assertSame(
            1,
            $connect
                ->table('user')
                ->insert([
                    'name' => 'niu',
                ]),
        );

        $posts = Post::eager(['user' => function (Relation $select) {
            $select->where('id', '>', 99999);
        }])
            ->limit(5)
            ->findAll();

        $this->assertInstanceof(Collection::class, $posts);
        $this->assertCount(5, $posts);

        foreach ($posts as $value) {
            $user = $value->user;
            $this->assertInstanceof(User::class, $user);
            $this->assertNotSame(1, $user->id);
            $this->assertNotSame('niu', $user->name);
            $this->assertNull($user->id);
            $this->assertNull($user->name);
        }
    }

    /**
     * @api(
     *     zh-CN:title="relation 读取关联",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testRelationAsMethod(): void
    {
        $connect = $this->createDatabaseConnect();

        $this->assertSame(
            1,
            $connect
                ->table('post')
                ->insert([
                    'title'     => 'hello world',
                    'user_id'   => 1,
                    'summary'   => 'Say hello to the world.',
                    'delete_at' => 0,
                ]),
        );

        $this->assertSame(
            1,
            $connect
                ->table('user')
                ->insert([
                    'name' => 'niu',
                ]),
        );

        $userRelation = Post::make()->relation('user');

        $this->assertInstanceof(BelongsTo::class, $userRelation);
        $this->assertSame('user_id', $userRelation->getSourceKey());
        $this->assertSame('id', $userRelation->getTargetKey());
        $this->assertInstanceof(Post::class, $userRelation->getSourceEntity());
        $this->assertInstanceof(User::class, $userRelation->getTargetEntity());
        $this->assertInstanceof(Select::class, $userRelation->getSelect());
    }

    public function testEagerButNotFoundSourceData(): void
    {
        $posts = Post::select()->limit(5)->findAll();

        $this->assertInstanceof(Collection::class, $posts);
        $this->assertCount(0, $posts);

        $posts = Post::eager(['user'])->limit(5)->findAll();

        $this->assertInstanceof(Collection::class, $posts);
        $this->assertCount(0, $posts);
    }

    /**
     * @api(
     *     zh-CN:title="relation 关联模型数据不存在返回空实体",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testRelationDataWasNotFound(): void
    {
        $post = Post::select()->where('id', 1)->findOne();

        $this->assertInstanceof(Post::class, $post);
        $this->assertNull($post->id);

        $connect = $this->createDatabaseConnect();

        $this->assertSame(
            1,
            $connect
                ->table('post')
                ->insert([
                    'title'     => 'hello world',
                    'user_id'   => 99999,
                    'summary'   => 'Say hello to the world.',
                    'delete_at' => 0,
                ]),
        );

        $post = Post::select()->where('id', 1)->findOne();

        $this->assertSame(1, $post->id);
        $this->assertSame(1, $post['id']);
        $this->assertSame(1, $post->getId());
        $this->assertSame(99999, $post->user_id);
        $this->assertSame(99999, $post->userId);
        $this->assertSame(99999, $post['user_id']);
        $this->assertSame(99999, $post->getUserId());
        $this->assertSame('hello world', $post->title);
        $this->assertSame('hello world', $post['title']);
        $this->assertSame('hello world', $post->getTitle());
        $this->assertSame('Say hello to the world.', $post->summary);
        $this->assertSame('Say hello to the world.', $post['summary']);
        $this->assertSame('Say hello to the world.', $post->getSummary());

        $user = $post->user;

        $this->assertInstanceof(User::class, $user);
        $this->assertNull($user->id);
        $this->assertNull($user['id']);
        $this->assertNull($user->getId());
        $this->assertNull($user->name);
        $this->assertNull($user['name']);
        $this->assertNull($user->getName());
    }

    public function testSourceDataIsEmtpy(): void
    {
        $post = Post::select()->where('id', 1)->findOne();

        $this->assertInstanceof(Post::class, $post);
        $this->assertNull($post->id);

        $connect = $this->createDatabaseConnect();

        $this->assertSame(
            1,
            $connect
                ->table('post')
                ->insert([
                    'title'     => 'hello world',
                    'user_id'   => 0,
                    'summary'   => 'Say hello to the world.',
                    'delete_at' => 0,
                ]),
        );

        $post = Post::select()->where('id', 1)->findOne();

        $this->assertSame(1, $post->id);
        $this->assertSame(1, $post['id']);
        $this->assertSame(1, $post->getId());
        $this->assertSame(0, $post->user_id);
        $this->assertSame(0, $post->userId);
        $this->assertSame(0, $post['user_id']);
        $this->assertSame(0, $post->getUserId());
        $this->assertSame('hello world', $post->title);
        $this->assertSame('hello world', $post['title']);
        $this->assertSame('hello world', $post->getTitle());
        $this->assertSame('Say hello to the world.', $post->summary);
        $this->assertSame('Say hello to the world.', $post['summary']);
        $this->assertSame('Say hello to the world.', $post->getSummary());

        $user = $post->user;

        $this->assertInstanceof(User::class, $user);
        $this->assertNull($user->id);
        $this->assertNull($user['id']);
        $this->assertNull($user->getId());
        $this->assertNull($user->name);
        $this->assertNull($user['name']);
        $this->assertNull($user->getName());
    }

    public function testSourceDataIsEmtpyAndValueIsNull(): void
    {
        $post = Post::select()->where('id', 1)->findOne();

        $this->assertInstanceof(Post::class, $post);
        $this->assertNull($post->id);
        $this->assertNull($post->user_id);
        $user = $post->user;

        $this->assertInstanceof(User::class, $user);
        $this->assertNull($user->id);
        $this->assertNull($user['id']);
        $this->assertNull($user->getId());
        $this->assertNull($user->name);
        $this->assertNull($user['name']);
        $this->assertNull($user->getName());
    }

    public function testEagerSourceDataIsEmtpy(): void
    {
        $posts = Post::select()->limit(5)->findAll();

        $this->assertInstanceof(Collection::class, $posts);
        $this->assertCount(0, $posts);

        $connect = $this->createDatabaseConnect();

        for ($i = 0; $i <= 5; $i++) {
            $this->assertSame(
                $i + 1,
                $connect
                    ->table('post')
                    ->insert([
                        'title'     => 'hello world',
                        'user_id'   => 0,
                        'summary'   => 'Say hello to the world.',
                        'delete_at' => 0,
                    ]),
            );
        }

        $posts = Post::eager(['user'])
            ->limit(5)
            ->findAll();

        $this->assertInstanceof(Collection::class, $posts);
        $this->assertCount(5, $posts);

        foreach ($posts as $value) {
            $user = $value->user;

            $this->assertInstanceof(User::class, $user);
            $this->assertNull($user->id);
            $this->assertNull($user->name);
        }
    }

    public function testValidateRelationKeyNotDefinedSourceKey(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Relation `source_key` field was not defined.'
        );

        $post = Post::select()->where('id', 1)->findOne();
        $this->assertInstanceof(Post::class, $post);
        $this->assertNull($post->id);

        $post->userNotDefinedSourceKey;
    }

    public function testValidateRelationKeyNotDefinedTargetKey(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Relation `target_key` field was not defined.'
        );

        $post = Post::select()->where('id', 1)->findOne();
        $this->assertInstanceof(Post::class, $post);
        $this->assertNull($post->id);

        $post->userNotDefinedTargetKey;
    }

    public function testValidateRelationFieldSourceKey(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'The field `post`.`not_found_source_key` of entity `Tests\\Database\\Ddd\\Entity\\Relation\\Post` was not defined.'
        );

        $post = Post::select()->where('id', 1)->findOne();
        $this->assertInstanceof(Post::class, $post);
        $this->assertNull($post->id);

        $post->belongsTo(User::class, 'id', 'not_found_source_key');
    }

    public function testValidateRelationFieldTargetKey(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'The field `user`.`not_found_target_key` of entity `Tests\\Database\\Ddd\\Entity\\Relation\\User` was not defined.'
        );

        $post = Post::select()->where('id', 1)->findOne();
        $this->assertInstanceof(Post::class, $post);
        $this->assertNull($post->id);

        $post->belongsTo(User::class, 'not_found_target_key', 'user_id');
    }

    protected function getDatabaseTable(): array
    {
        return ['post', 'user'];
    }
}
