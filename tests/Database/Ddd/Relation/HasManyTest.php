<?php

declare(strict_types=1);

namespace Tests\Database\Ddd\Relation;

use Leevel\Collection\Collection;
use Leevel\Database\Ddd\Relation\HasMany;
use Leevel\Database\Ddd\Select;
use Tests\Database\DatabaseTestCase as TestCase;
use Tests\Database\Ddd\Entity\Relation\Comment;
use Tests\Database\Ddd\Entity\Relation\Post;

/**
 * @api(
 *     zh-CN:title="hasMany 一对多关联",
 *     path="orm/hasmany",
 *     zh-CN:description="
 * 一对多的关联是一种常用的关联，比如一篇文章与文章评论属于一对多的关系。
 *
 * **一对多关联支持类型关联项**
 *
 * |  关联项   | 说明  |    例子   |
 * |  ----  | ----  | ----  |
 * | \Leevel\Database\Ddd\Entity::HAS_MANY  | 一对多关联实体 |  \Tests\Database\Ddd\Entity\Relation\Comment::class  |
 * | \Leevel\Database\Ddd\Entity::SOURCE_KEY  | 关联查询源键字段 | id |
 * | \Leevel\Database\Ddd\Entity::TARGET_KEY  | 关联目标键字段 | post_id |
 * | \Leevel\Database\Ddd\Entity::RELATION_SCOPE  | 关联查询作用域 | comment |
 * ",
 * )
 */
class HasManyTest extends TestCase
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
     * **Tests\Database\Ddd\Entity\Relation\Comment**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Database\Ddd\Entity\Relation\Comment::class)]}
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

        for ($i = 0; $i < 10; $i++) {
            $connect
                ->table('comment')
                ->insert([
                    'title'   => 'niu'.($i + 1),
                    'post_id' => 1,
                    'content' => 'Comment data.'.($i + 1),
                ]);
        }

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

        $comment = $post->comment;

        $this->assertInstanceof(Collection::class, $comment);

        $n = 0;

        foreach ($comment as $k => $v) {
            $id = (int) ($n + 5);

            $this->assertInstanceOf(Comment::class, $v);
            $this->assertSame($n, $k);
            $this->assertSame($id, (int) $v->id);
            $this->assertSame($id, (int) $v['id']);
            $this->assertSame($id, (int) $v->getId());
            $this->assertSame('niu'.$id, $v['title']);
            $this->assertSame('niu'.$id, $v->title);
            $this->assertSame('niu'.$id, $v->getTitle());
            $this->assertSame('Comment data.'.$id, $v['content']);
            $this->assertSame('Comment data.'.$id, $v->content);
            $this->assertSame('Comment data.'.$id, $v->getContent());

            $n++;
        }

        $this->assertCount(6, $comment);
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
            2,
            $connect
                ->table('post')
                ->insert([
                    'title'     => 'foo bar',
                    'user_id'   => 1,
                    'summary'   => 'Say foo to the bar.',
                    'delete_at' => 0,
                ]),
        );

        for ($i = 0; $i < 10; $i++) {
            $connect
                ->table('comment')
                ->insert([
                    'title'   => 'niu'.($i + 1),
                    'post_id' => 1,
                    'content' => 'Comment data.'.($i + 1),
                ]);
        }

        for ($i = 0; $i < 10; $i++) {
            $connect
                ->table('comment')
                ->insert([
                    'title'   => 'niu'.($i + 1),
                    'post_id' => 2,
                    'content' => 'Comment data.'.($i + 1),
                ]);
        }

        $posts = Post::eager(['comment'])->findAll();

        $this->assertInstanceof(Collection::class, $posts);
        $this->assertCount(2, $posts);

        $min = 5;

        foreach ($posts as $k => $value) {
            $comments = $value->comment;

            $this->assertInstanceof(Collection::class, $comments);
            $this->assertSame(0 === $k ? 6 : 10, count($comments));

            foreach ($comments as $comment) {
                $this->assertInstanceof(Comment::class, $comment);
                $this->assertSame($min, $comment->id);
                $min++;
            }
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
            2,
            $connect
                ->table('post')
                ->insert([
                    'title'     => 'foo bar',
                    'user_id'   => 1,
                    'summary'   => 'Say foo to the bar.',
                    'delete_at' => 0,
                ]),
        );

        for ($i = 0; $i < 10; $i++) {
            $connect
                ->table('comment')
                ->insert([
                    'title'   => 'niu'.($i + 1),
                    'post_id' => 1,
                    'content' => 'Comment data.'.($i + 1),
                ]);
        }

        for ($i = 0; $i < 10; $i++) {
            $connect
                ->table('comment')
                ->insert([
                    'title'   => 'niu'.($i + 1),
                    'post_id' => 2,
                    'content' => 'Comment data.'.($i + 1),
                ]);
        }

        $posts = Post::eager(['comment' => function ($select) {
            $select->where('id', '>', 99999);
        }])->findAll();

        $this->assertInstanceof(Collection::class, $posts);
        $this->assertCount(2, $posts);

        foreach ($posts as $k => $value) {
            $comments = $value->comment;
            $this->assertInstanceof(Collection::class, $comments);
            $this->assertCount(0, $comments);
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

        for ($i = 0; $i < 10; $i++) {
            $connect
                ->table('comment')
                ->insert([
                    'title'   => 'niu'.($i + 1),
                    'post_id' => 1,
                    'content' => 'Comment data.'.($i + 1),
                ]);
        }

        $commentRelation = Post::make()->relation('comment');

        $this->assertInstanceof(HasMany::class, $commentRelation);
        $this->assertSame('id', $commentRelation->getSourceKey());
        $this->assertSame('post_id', $commentRelation->getTargetKey());
        $this->assertInstanceof(Post::class, $commentRelation->getSourceEntity());
        $this->assertInstanceof(Comment::class, $commentRelation->getTargetEntity());
        $this->assertInstanceof(Select::class, $commentRelation->getSelect());
    }

    public function testSourceDataIsEmtpy(): void
    {
        $post = Post::select()->where('id', 1)->findOne();

        $this->assertInstanceof(Post::class, $post);
        $this->assertNull($post->id);
        $comment = $post->comment;

        $this->assertInstanceof(Collection::class, $comment);
        $this->assertCount(0, $comment);
    }

    /**
     * @api(
     *     zh-CN:title="relation 关联模型数据不存在返回空集合",
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
                    'user_id'   => 1,
                    'summary'   => 'Say hello to the world.',
                    'delete_at' => 0,
                ]),
        );

        for ($i = 0; $i < 10; $i++) {
            $connect
                ->table('comment')
                ->insert([
                    'title'   => 'niu'.($i + 1),
                    'post_id' => 2,
                    'content' => 'Comment data.'.($i + 1),
                ]);
        }

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

        $comment = $post->comment;

        $this->assertInstanceof(Collection::class, $comment);
        $this->assertCount(0, $comment);
    }

    public function testEagerRelationWasNotFound(): void
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
            2,
            $connect
                ->table('post')
                ->insert([
                    'title'     => 'foo bar',
                    'user_id'   => 1,
                    'summary'   => 'Say foo to the bar.',
                    'delete_at' => 0,
                ]),
        );

        for ($i = 0; $i < 10; $i++) {
            $connect
                ->table('comment')
                ->insert([
                    'title'   => 'niu'.($i + 1),
                    'post_id' => 5,
                    'content' => 'Comment data.'.($i + 1),
                ]);
        }

        for ($i = 0; $i < 10; $i++) {
            $connect
                ->table('comment')
                ->insert([
                    'title'   => 'niu'.($i + 1),
                    'post_id' => 99,
                    'content' => 'Comment data.'.($i + 1),
                ]);
        }

        $posts = Post::eager(['comment'])->findAll();

        $this->assertInstanceof(Collection::class, $posts);
        $this->assertCount(2, $posts);

        foreach ($posts as $value) {
            $comments = $value->comment;

            $this->assertInstanceof(Collection::class, $comments);
            $this->assertCount(0, $comments);
        }
    }

    public function testEagerWithEmptySourceData(): void
    {
        $posts = Post::eager(['comment'])->findAll();

        $this->assertInstanceof(Collection::class, $posts);
        $this->assertCount(0, $posts);
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

        $post->commentNotDefinedSourceKey;
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

        $post->commentNotDefinedTargetKey;
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

        $post->hasMany(Comment::class, 'post_id', 'not_found_source_key');
    }

    public function testValidateRelationFieldTargetKey(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'The field `comment`.`not_found_target_key` of entity `Tests\\Database\\Ddd\\Entity\\Relation\\Comment` was not defined.'
        );

        $post = Post::select()->where('id', 1)->findOne();
        $this->assertInstanceof(Post::class, $post);
        $this->assertNull($post->id);

        $post->hasMany(Comment::class, 'not_found_target_key', 'id');
    }

    protected function getDatabaseTable(): array
    {
        return ['post', 'comment'];
    }
}
