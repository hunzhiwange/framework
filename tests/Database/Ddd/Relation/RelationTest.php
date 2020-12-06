<?php

declare(strict_types=1);

namespace Tests\Database\Ddd\Relation;

use Exception;
use Leevel\Database\Ddd\Relation\HasOne;
use Leevel\Database\Ddd\Relation\Relation;
use Tests\Database\DatabaseTestCase as TestCase;
use Tests\Database\Ddd\Entity\Relation\Post;
use Tests\Database\Ddd\Entity\Relation\PostContent;

/**
 * @api(
 *     zh-CN:title="关联",
 *     path="orm/relation",
 *     zh-CN:description="
 * 将相关实体连接起来，可以更加方便地操作数据。
 *
 * **关联支持类型**
 *
 * |  关联类型   | 说明  |
 * |  ----  | ----  |
 * | belongsTo  | 从属关联 |
 * | hasOne  | 一对一关联 |
 * | hasMany  | 一对多关联 |
 * | manyMany  | 多对多关联 |
 * ",
 * )
 */
class RelationTest extends TestCase
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
     * **Tests\Database\Ddd\Entity\Relation\PostContent**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Database\Ddd\Entity\Relation\PostContent::class)]}
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
                ])
        );

        $this->assertSame(
            1,
            $connect
                ->table('post_content')
                ->insert([
                    'post_id' => 1,
                    'content' => 'I am content with big data.',
                ])
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

        $postContent = $post->postContent;

        $this->assertInstanceof(PostContent::class, $postContent);
        $this->assertSame(1, $postContent->post_id);
        $this->assertSame(1, $postContent->postId);
        $this->assertSame(1, $postContent['post_id']);
        $this->assertSame(1, $postContent['postId']);
        $this->assertSame(1, $postContent->getPostId());
        $this->assertSame('I am content with big data.', $postContent->content);
        $this->assertSame('I am content with big data.', $postContent['content']);
        $this->assertSame('I am content with big data.', $postContent->getContent());
    }

    public function testWithoutRelationCondition(): void
    {
        $relation = Relation::withoutRelationCondition(function (): Relation {
            return new class() extends HasOne {
                public function __construct()
                {
                }
            };
        });

        $this->assertInstanceof(Relation::class, $relation);
    }

    public function testWithoutRelationConditionException(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage(
            'error'
        );

        $relation = Relation::withoutRelationCondition(function (): Relation {
            return new class() extends HasOne {
                public function __construct()
                {
                    throw new Exception('error');
                }
            };
        });
    }

    public function testValidateRelationIsInvalid(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Prop `summary` of entity `Tests\\Database\\Ddd\\Entity\\Relation\\Post` is not a relation type.'
        );

        $notARelation = Post::make()->relation('summary');
    }

    protected function getDatabaseTable(): array
    {
        return ['post', 'post_content'];
    }
}
