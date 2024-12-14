<?php

declare(strict_types=1);

namespace Tests\Database\Ddd\Relation;

use Leevel\Database\Ddd\EntityCollection as Collection;
use Leevel\Database\Ddd\Relation\HasOne;
use Leevel\Database\Ddd\Relation\Relation;
use Leevel\Database\Ddd\Select;
use Leevel\Kernel\Utils\Api;
use Tests\Database\DatabaseTestCase as TestCase;
use Tests\Database\Ddd\Entity\Relation\Post;
use Tests\Database\Ddd\Entity\Relation\PostContent;

#[Api([
    'zh-CN:title' => 'hasOne 一对一关联',
    'path' => 'orm/hasone',
    'zh-CN:description' => <<<'EOT'
一对一的关联是一种常用的关联，比如一篇文章与文章内容属于一对一的关系。

**一对一关联支持类型关联项**

|  关联项   | 说明  |    例子   |
|  ----  | ----  | ----  |
| \Leevel\Database\Ddd\Entity::HAS_ONE  | 一对一关联实体 |  \Tests\Database\Ddd\Entity\Relation\PostContent::class  |
| \Leevel\Database\Ddd\Entity::SOURCE_KEY  | 关联查询源键字段 | id |
| \Leevel\Database\Ddd\Entity::TARGET_KEY  | 关联目标键字段 | post_id |
| \Leevel\Database\Ddd\Entity::RELATION_SCOPE  | 关联查询作用域 | foo |
EOT,
])]
final class HasOneTest extends TestCase
{
    #[Api([
        'zh-CN:title' => '基本使用方法',
        'zh-CN:description' => <<<'EOT'
**fixture 定义**

**Tests\Database\Ddd\Entity\Relation\Post**

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Database\Ddd\Entity\Relation\Post::class)]}
```

**Tests\Database\Ddd\Entity\Relation\PostContent**

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Database\Ddd\Entity\Relation\PostContent::class)]}
```
EOT,
    ])]
    public function testBaseUse(): void
    {
        $post = Post::select()->where('id', 1)->findOne();

        $this->assertInstanceof(Post::class, $post);
        self::assertNull($post->id);

        $connect = $this->createDatabaseConnect();

        self::assertSame(
            1,
            $connect
                ->table('post')
                ->insert([
                    'title' => 'hello world',
                    'user_id' => 1,
                    'summary' => 'Say hello to the world.',
                    'delete_at' => 0,
                ])
        );

        self::assertSame(
            0,
            $connect
                ->table('post_content')
                ->insert([
                    'post_id' => 1,
                    'content' => 'I am content with big data.',
                ])
        );

        $post = Post::select()->where('id', 1)->findOne();

        self::assertSame(1, $post->id);
        self::assertSame(1, $post->userId);
        self::assertSame('hello world', $post->title);
        self::assertSame('Say hello to the world.', $post->summary);

        $postContent = $post->postContent;

        $this->assertInstanceof(PostContent::class, $postContent);
        self::assertSame(1, $postContent->postId);
        self::assertSame('I am content with big data.', $postContent->content);
    }

    #[Api([
        'zh-CN:title' => 'eager 预加载关联',
    ])]
    public function testEager(): void
    {
        $post = Post::select()->where('id', 1)->findOne();

        $this->assertInstanceof(Post::class, $post);
        self::assertNull($post->id);

        $connect = $this->createDatabaseConnect();

        for ($i = 0; $i <= 5; ++$i) {
            self::assertSame(
                $i + 1,
                $connect
                    ->table('post')
                    ->insert([
                        'title' => 'hello world',
                        'user_id' => 1,
                        'summary' => 'Say hello to the world.',
                        'delete_at' => 0,
                    ])
            );

            self::assertSame(
                0,
                $connect
                    ->table('post_content')
                    ->insert([
                        'post_id' => $i + 1,
                        'content' => 'I am content with big data.',
                    ])
            );
        }

        $posts = Post::eager(['post_content'])->findAll();

        $this->assertInstanceof(Collection::class, $posts);
        self::assertCount(6, $posts);

        foreach ($posts as $value) {
            $postContent = $value->postContent;

            $this->assertInstanceof(PostContent::class, $postContent);
            self::assertSame($value->id, $postContent->postId);
            self::assertSame('I am content with big data.', $postContent->content);
        }
    }

    #[Api([
        'zh-CN:title' => 'eager 预加载关联支持查询条件过滤',
    ])]
    public function testEagerWithCondition(): void
    {
        $post = Post::select()->where('id', 1)->findOne();

        $this->assertInstanceof(Post::class, $post);
        self::assertNull($post->id);

        $connect = $this->createDatabaseConnect();

        for ($i = 0; $i <= 5; ++$i) {
            self::assertSame(
                $i + 1,
                $connect
                    ->table('post')
                    ->insert([
                        'title' => 'hello world',
                        'user_id' => 1,
                        'summary' => 'Say hello to the world.',
                        'delete_at' => 0,
                    ])
            );

            self::assertSame(
                0,
                $connect
                    ->table('post_content')
                    ->insert([
                        'post_id' => $i + 1,
                        'content' => 'I am content with big data.',
                    ])
            );
        }

        $posts = Post::eager(['post_content' => static function (Relation $select): void {
            $select->where('post_id', '>', 99999);
        }])->findAll();

        $this->assertInstanceof(Collection::class, $posts);
        self::assertCount(6, $posts);

        foreach ($posts as $value) {
            $postContent = $value->postContent;
            $this->assertInstanceof(PostContent::class, $postContent);
            self::assertNotSame($value->id, $postContent->postId);
            self::assertNotSame('I am content with big data.', $postContent->content);
            self::assertNull($postContent->postId);
            self::assertNull($postContent->content);
        }
    }

    #[Api([
        'zh-CN:title' => 'relation 读取关联',
    ])]
    public function testRelationAsMethod(): void
    {
        $connect = $this->createDatabaseConnect();

        self::assertSame(
            1,
            $connect
                ->table('post')
                ->insert([
                    'title' => 'hello world',
                    'user_id' => 1,
                    'summary' => 'Say hello to the world.',
                    'delete_at' => 0,
                ])
        );

        self::assertSame(
            0,
            $connect
                ->table('post_content')
                ->insert([
                    'post_id' => 1,
                    'content' => 'I am content with big data.',
                ])
        );

        $postContentRelation = Post::make()->relation('postContent');

        $this->assertInstanceof(HasOne::class, $postContentRelation);
        self::assertSame('id', $postContentRelation->getSourceKey());
        self::assertSame('post_id', $postContentRelation->getTargetKey());
        $this->assertInstanceof(Post::class, $postContentRelation->getSourceEntity());
        $this->assertInstanceof(PostContent::class, $postContentRelation->getTargetEntity());
        $this->assertInstanceof(Select::class, $postContentRelation->getSelect());
    }

    #[Api([
        'zh-CN:title' => 'relation 关联模型数据不存在返回空实体',
    ])]
    public function testRelationDataWasNotFound(): void
    {
        $post = Post::select()->where('id', 1)->findOne();

        $this->assertInstanceof(Post::class, $post);
        self::assertNull($post->id);

        $connect = $this->createDatabaseConnect();

        self::assertSame(
            1,
            $connect
                ->table('post')
                ->insert([
                    'title' => 'hello world',
                    'user_id' => 1,
                    'summary' => 'Say hello to the world.',
                    'delete_at' => 0,
                ])
        );

        self::assertSame(
            0,
            $connect
                ->table('post_content')
                ->insert([
                    'post_id' => 5,
                    'content' => 'I am content with big data.',
                ])
        );

        $post = Post::select()->where('id', 1)->findOne();

        self::assertSame(1, $post->id);
        self::assertSame(1, $post->userId);
        self::assertSame('hello world', $post->title);
        self::assertSame('Say hello to the world.', $post->summary);

        $postContent = $post->postContent;

        $this->assertInstanceof(PostContent::class, $postContent);
        self::assertNull($postContent->postId);
        self::assertNull($postContent->content);
    }

    public function testEagerRelationWasNotFound(): void
    {
        $post = Post::select()->where('id', 1)->findOne();

        $this->assertInstanceof(Post::class, $post);
        self::assertNull($post->id);

        $connect = $this->createDatabaseConnect();

        for ($i = 0; $i <= 5; ++$i) {
            self::assertSame(
                $i + 1,
                $connect
                    ->table('post')
                    ->insert([
                        'title' => 'hello world',
                        'user_id' => 1,
                        'summary' => 'Say hello to the world.',
                        'delete_at' => 0,
                    ])
            );

            self::assertSame(
                0,
                $connect
                    ->table('post_content')
                    ->insert([
                        'post_id' => 9999 + $i,
                        'content' => 'I am content with big data.',
                    ])
            );
        }

        $posts = Post::eager(['post_content'])->findAll();

        $this->assertInstanceof(Collection::class, $posts);
        self::assertCount(6, $posts);

        foreach ($posts as $value) {
            $postContent = $value->postContent;

            $this->assertInstanceof(PostContent::class, $postContent);
            self::assertNull($postContent->postId);
            self::assertNull($postContent->content);
        }
    }

    public function testSourceDataIsEmpty(): void
    {
        $post = new Post(['id' => 0]);

        $this->assertInstanceof(Post::class, $post);
        self::assertSame(0, $post->id);
        $postContent = $post->postContent;

        $this->assertInstanceof(PostContent::class, $postContent);
        self::assertNull($postContent->postId);
        self::assertNull($postContent->content);
    }

    public function testSourceDataIsEmtpyAndValueIsNull(): void
    {
        $post = Post::select()->where('id', 1)->findOne();

        $this->assertInstanceof(Post::class, $post);
        self::assertNull($post->id);
        $postContent = $post->postContent;

        $this->assertInstanceof(PostContent::class, $postContent);
        self::assertNull($postContent->postId);
        self::assertNull($postContent->content);
    }

    public function testValidateRelationKeyNotDefinedSourceKey(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Relation `source_key` field was not defined.'
        );

        $post = Post::select()->where('id', 1)->findOne();
        $this->assertInstanceof(Post::class, $post);
        self::assertNull($post->id);

        $post->postContentNotDefinedSourceKey;
    }

    public function testValidateRelationKeyNotDefinedTargetKey(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Relation `target_key` field was not defined.'
        );

        $post = Post::select()->where('id', 1)->findOne();
        $this->assertInstanceof(Post::class, $post);
        self::assertNull($post->id);

        $post->postContentNotDefinedTargetKey;
    }

    public function testValidateRelationFieldSourceKey(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'The field `post`.`not_found_source_key` of entity `Tests\\Database\\Ddd\\Entity\\Relation\\Post` was not defined.'
        );

        $post = Post::select()->where('id', 1)->findOne();
        $this->assertInstanceof(Post::class, $post);
        self::assertNull($post->id);

        $post->hasOne(PostContent::class, 'post_id', 'not_found_source_key');
    }

    public function testValidateRelationFieldTargetKey(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'The field `post_content`.`not_found_target_key` of entity `Tests\\Database\\Ddd\\Entity\\Relation\\PostContent` was not defined.'
        );

        $post = Post::select()->where('id', 1)->findOne();
        $this->assertInstanceof(Post::class, $post);
        self::assertNull($post->id);

        $post->hasOne(PostContent::class, 'not_found_target_key', 'id');
    }

    public function testEagerWithEmptySourceData(): void
    {
        $posts = Post::eager(['postContent'])->findAll();

        $this->assertInstanceof(Collection::class, $posts);
        self::assertCount(0, $posts);
    }

    protected function getDatabaseTable(): array
    {
        return ['post', 'post_content'];
    }
}
