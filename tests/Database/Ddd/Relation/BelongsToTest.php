<?php

declare(strict_types=1);

namespace Tests\Database\Ddd\Relation;

use Leevel\Database\Ddd\EntityCollection as Collection;
use Leevel\Database\Ddd\Relation\BelongsTo;
use Leevel\Database\Ddd\Relation\Relation;
use Leevel\Database\Ddd\Select;
use Leevel\Kernel\Utils\Api;
use Tests\Database\DatabaseTestCase as TestCase;
use Tests\Database\Ddd\Entity\Relation\Post;
use Tests\Database\Ddd\Entity\Relation\User;

#[Api([
    'zh-CN:title' => 'belongsTo 从属关联',
    'path' => 'orm/belongs-to',
    'zh-CN:description' => <<<'EOT'
从属关联也是一对一的关联的一种，比如一篇文章属于某个用户发表。

**从属关联支持类型关联项**

|  关联项   | 说明  |    例子   |
|  ----  | ----  | ----  |
| \Leevel\Database\Ddd\Entity::BELONGS_TO  | 从属关联实体 |  \Tests\Database\Ddd\Entity\Relation\User::class  |
| \Leevel\Database\Ddd\Entity::SOURCE_KEY  | 关联查询源键字段 | user_id |
| \Leevel\Database\Ddd\Entity::TARGET_KEY  | 关联目标键字段 | id |
| \Leevel\Database\Ddd\Entity::RELATION_SCOPE  | 关联查询作用域 | foo |
EOT,
])]
final class BelongsToTest extends TestCase
{
    #[Api([
        'zh-CN:title' => '基本使用方法',
        'zh-CN:description' => <<<'EOT'
**fixture 定义**

**Tests\Database\Ddd\Entity\Relation\Post**

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Database\Ddd\Entity\Relation\Post::class)]}
```

**Tests\Database\Ddd\Entity\Relation\User**

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Database\Ddd\Entity\Relation\User::class)]}
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
                ]),
        );

        self::assertSame(
            1,
            $connect
                ->table('user')
                ->insert([
                    'name' => 'niu',
                ]),
        );

        $post = Post::select()->where('id', 1)->findOne();

        self::assertSame(1, $post->id);
        self::assertSame(1, $post->userId);
        self::assertSame('hello world', $post->title);
        self::assertSame('Say hello to the world.', $post->summary);

        $user = $post->user;

        $this->assertInstanceof(User::class, $user);
        self::assertSame(1, $user->id);
        self::assertSame('niu', $user->name);
    }

    #[Api([
        'zh-CN:title' => 'eager 预加载关联',
    ])]
    public function testEager(): void
    {
        $posts = Post::select()->limit(5)->findAll();

        $this->assertInstanceof(Collection::class, $posts);
        self::assertCount(0, $posts);

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
                    ]),
            );
        }

        self::assertSame(
            1,
            $connect
                ->table('user')
                ->insert([
                    'name' => 'niu',
                ]),
        );

        $posts = Post::eager(['user'])
            ->limit(5)
            ->findAll()
        ;

        $this->assertInstanceof(Collection::class, $posts);
        self::assertCount(5, $posts);

        foreach ($posts as $value) {
            $user = $value->user;

            $this->assertInstanceof(User::class, $user);
            self::assertSame(1, $user->id);
            self::assertSame('niu', $user->name);
        }
    }

    #[Api([
        'zh-CN:title' => 'eager 预加载关联支持查询条件过滤(闭包)',
    ])]
    public function testEagerWithCondition(): void
    {
        $posts = Post::select()->limit(5)->findAll();

        $this->assertInstanceof(Collection::class, $posts);
        self::assertCount(0, $posts);

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
                    ]),
            );
        }

        self::assertSame(
            1,
            $connect
                ->table('user')
                ->insert([
                    'name' => 'niu',
                ]),
        );

        $posts = Post::eager(['user' => static function (Relation $select): void {
            $select->where('id', '>', 99999);
        }])
            ->limit(5)
            ->findAll()
        ;

        $this->assertInstanceof(Collection::class, $posts);
        self::assertCount(5, $posts);

        $sql = Post::select()->getLastSql();
        $resultSql = <<<'eot'
SQL: [92] SELECT `user`.* FROM `user` WHERE `user`.`id` > :user_id AND `user`.`id` IN (:user_id_1_in0) | Params:  2 | Key: Name: [8] :user_id | paramno=0 | name=[8] ":user_id" | is_param=1 | param_type=1 | Key: Name: [14] :user_id_1_in0 | paramno=1 | name=[14] ":user_id_1_in0" | is_param=1 | param_type=1 (SELECT `user`.* FROM `user` WHERE `user`.`id` > 99999 AND `user`.`id` IN (1))
eot;
        self::assertSame($resultSql, $sql);

        foreach ($posts as $value) {
            $user = $value->user;
            $this->assertInstanceof(User::class, $user);
            self::assertNotSame(1, $user->id);
            self::assertNotSame('niu', $user->name);
            self::assertNull($user->id);
            self::assertNull($user->name);
        }
    }

    #[Api([
        'zh-CN:title' => 'eager 预加载关联支持查询条件过滤(字符串)',
    ])]
    public function testEagerWithCondition2(): void
    {
        $posts = Post::select()->limit(5)->findAll();

        $this->assertInstanceof(Collection::class, $posts);
        self::assertCount(0, $posts);

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
                    ]),
            );
        }

        self::assertSame(
            1,
            $connect
                ->table('user')
                ->insert([
                    'name' => 'niu',
                ]),
        );

        $posts = Post::eager([
            'user' => 'stringUserRelationScope',
        ])
            ->limit(5)
            ->findAll()
        ;

        $this->assertInstanceof(Collection::class, $posts);
        self::assertCount(5, $posts);

        $sql = Post::select()->getLastSql();
        $resultSql = <<<'eot'
SQL: [92] SELECT `user`.* FROM `user` WHERE `user`.`id` > :user_id AND `user`.`id` IN (:user_id_1_in0) | Params:  2 | Key: Name: [8] :user_id | paramno=0 | name=[8] ":user_id" | is_param=1 | param_type=1 | Key: Name: [14] :user_id_1_in0 | paramno=1 | name=[14] ":user_id_1_in0" | is_param=1 | param_type=1 (SELECT `user`.* FROM `user` WHERE `user`.`id` > 4 AND `user`.`id` IN (1))
eot;
        self::assertSame($resultSql, $sql);

        foreach ($posts as $value) {
            $user = $value->user;
            $this->assertInstanceof(User::class, $user);
            self::assertNotSame(1, $user->id);
            self::assertNotSame('niu', $user->name);
            self::assertNull($user->id);
            self::assertNull($user->name);
        }
    }

    #[Api([
        'zh-CN:title' => 'eager 预加载关联支持查询条件过滤(关联数组)',
    ])]
    public function testEagerWithCondition3(): void
    {
        $posts = Post::select()->limit(5)->findAll();

        $this->assertInstanceof(Collection::class, $posts);
        self::assertCount(0, $posts);

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
                    ]),
            );
        }

        self::assertSame(
            1,
            $connect
                ->table('user')
                ->insert([
                    'name' => 'niu',
                ]),
        );

        $posts = Post::eager([
            'user' => [
                'where' => ['id', '>', 4],
                'orderBy' => ['id DESC'],
            ],
        ])
            ->limit(5)
            ->findAll()
        ;

        $this->assertInstanceof(Collection::class, $posts);
        self::assertCount(5, $posts);

        $sql = Post::select()->getLastSql();
        $resultSql = <<<'eot'
SQL: [118] SELECT `user`.* FROM `user` WHERE `user`.`id` > :user_id AND `user`.`id` IN (:user_id_1_in0) ORDER BY `user`.`id` DESC | Params:  2 | Key: Name: [8] :user_id | paramno=0 | name=[8] ":user_id" | is_param=1 | param_type=1 | Key: Name: [14] :user_id_1_in0 | paramno=1 | name=[14] ":user_id_1_in0" | is_param=1 | param_type=1 (SELECT `user`.* FROM `user` WHERE `user`.`id` > 4 AND `user`.`id` IN (1) ORDER BY `user`.`id` DESC)
eot;
        self::assertSame($resultSql, $sql);

        foreach ($posts as $value) {
            $user = $value->user;

            $this->assertInstanceof(User::class, $user);
            self::assertNotSame(1, $user->id);
            self::assertNotSame('niu', $user->name);
            self::assertNull($user->id);
            self::assertNull($user->name);
        }
    }

    #[Api([
        'zh-CN:title' => 'eager 预加载关联支持查询条件过滤(索引数组)',
    ])]
    public function testEagerWithCondition4(): void
    {
        $posts = Post::select()->limit(5)->findAll();

        $this->assertInstanceof(Collection::class, $posts);
        self::assertCount(0, $posts);

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
                    ]),
            );
        }

        self::assertSame(
            1,
            $connect
                ->table('user')
                ->insert([
                    'name' => 'niu',
                ]),
        );

        $posts = Post::eager([
            'user' => [
                ['where', 'id', '>', 4],
                'where' => ['id', '>', 5],
                ['orderBy', 'id DESC'],
            ],
        ])
            ->limit(5)
            ->findAll()
        ;

        $this->assertInstanceof(Collection::class, $posts);
        self::assertCount(5, $posts);

        $sql = Post::select()->getLastSql();
        $resultSql = <<<'eot'
SQL: [147] SELECT `user`.* FROM `user` WHERE `user`.`id` > :user_id AND `user`.`id` > :user_id_1 AND `user`.`id` IN (:user_id_2_in0) ORDER BY `user`.`id` DESC | Params:  3 | Key: Name: [8] :user_id | paramno=0 | name=[8] ":user_id" | is_param=1 | param_type=1 | Key: Name: [10] :user_id_1 | paramno=1 | name=[10] ":user_id_1" | is_param=1 | param_type=1 | Key: Name: [14] :user_id_2_in0 | paramno=2 | name=[14] ":user_id_2_in0" | is_param=1 | param_type=1 (SELECT `user`.* FROM `user` WHERE `user`.`id` > 4 AND `user`.`id` > 5 AND `user`.`id` IN (1) ORDER BY `user`.`id` DESC)
eot;
        self::assertSame($resultSql, $sql);

        foreach ($posts as $value) {
            $user = $value->user;

            $this->assertInstanceof(User::class, $user);
            self::assertNotSame(1, $user->id);
            self::assertNotSame('niu', $user->name);
            self::assertNull($user->id);
            self::assertNull($user->name);
        }
    }

    public function testEagerWithCondition5(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Query method must be string.');

        $posts = Post::select()->limit(5)->findAll();

        $this->assertInstanceof(Collection::class, $posts);
        self::assertCount(0, $posts);

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
                    ]),
            );
        }

        self::assertSame(
            1,
            $connect
                ->table('user')
                ->insert([
                    'name' => 'niu',
                ]),
        );

        Post::eager([
            'user' => [
                [1],
            ],
        ])
            ->limit(5)
            ->findAll()
        ;
    }

    public function testEagerWithCondition6(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Query method must be string.');

        $posts = Post::select()->limit(5)->findAll();

        $this->assertInstanceof(Collection::class, $posts);
        self::assertCount(0, $posts);

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
                    ]),
            );
        }

        self::assertSame(
            1,
            $connect
                ->table('user')
                ->insert([
                    'name' => 'niu',
                ]),
        );

        Post::eager([
            'user' => [
                [],
            ],
        ])
            ->limit(5)
            ->findAll()
        ;
    }

    #[Api([
        'zh-CN:title' => 'getRelation 读取关联',
    ])]
    public function testGetRelationAsMethod(): void
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
                ]),
        );

        self::assertSame(
            1,
            $connect
                ->table('user')
                ->insert([
                    'name' => 'niu',
                ]),
        );

        $userRelation = Post::make()->getRelation('user');

        $this->assertInstanceof(BelongsTo::class, $userRelation);
        self::assertSame('user_id', $userRelation->getSourceKey());
        self::assertSame('id', $userRelation->getTargetKey());
        $this->assertInstanceof(Post::class, $userRelation->getSourceEntity());
        $this->assertInstanceof(User::class, $userRelation->getTargetEntity());
        $this->assertInstanceof(Select::class, $userRelation->getSelect());
    }

    public function testEagerButNotFoundSourceData(): void
    {
        $posts = Post::select()->limit(5)->findAll();

        $this->assertInstanceof(Collection::class, $posts);
        self::assertCount(0, $posts);

        $posts = Post::eager(['user'])->limit(5)->findAll();

        $this->assertInstanceof(Collection::class, $posts);
        self::assertCount(0, $posts);
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
                    'user_id' => 99999,
                    'summary' => 'Say hello to the world.',
                    'delete_at' => 0,
                ]),
        );

        $post = Post::select()->where('id', 1)->findOne();

        self::assertSame(1, $post->id);
        self::assertSame(99999, $post->userId);
        self::assertSame('hello world', $post->title);
        self::assertSame('Say hello to the world.', $post->summary);

        $user = $post->user;

        $this->assertInstanceof(User::class, $user);
        self::assertNull($user->id);
        self::assertNull($user->name);
    }

    public function testSourceDataIsEmtpy(): void
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
                    'user_id' => 0,
                    'summary' => 'Say hello to the world.',
                    'delete_at' => 0,
                ]),
        );

        $post = Post::select()->where('id', 1)->findOne();

        self::assertSame(1, $post->id);
        self::assertSame(0, $post->userId);
        self::assertSame('hello world', $post->title);
        self::assertSame('Say hello to the world.', $post->summary);

        $user = $post->user;

        $this->assertInstanceof(User::class, $user);
        self::assertNull($user->id);
        self::assertNull($user->name);
    }

    public function testSourceDataIsEmtpyAndValueIsNull(): void
    {
        $post = Post::select()->where('id', 1)->findOne();

        $this->assertInstanceof(Post::class, $post);
        self::assertNull($post->id);
        $user = $post->user;

        $this->assertInstanceof(User::class, $user);
        self::assertNull($user->id);
        self::assertNull($user->name);
    }

    public function testEagerSourceDataIsEmtpy(): void
    {
        $posts = Post::select()->limit(5)->findAll();

        $this->assertInstanceof(Collection::class, $posts);
        self::assertCount(0, $posts);

        $connect = $this->createDatabaseConnect();

        for ($i = 0; $i <= 5; ++$i) {
            self::assertSame(
                $i + 1,
                $connect
                    ->table('post')
                    ->insert([
                        'title' => 'hello world',
                        'user_id' => 0,
                        'summary' => 'Say hello to the world.',
                        'delete_at' => 0,
                    ]),
            );
        }

        $posts = Post::eager(['user'])
            ->limit(5)
            ->findAll()
        ;

        $this->assertInstanceof(Collection::class, $posts);
        self::assertCount(5, $posts);

        foreach ($posts as $value) {
            $user = $value->user;

            $this->assertInstanceof(User::class, $user);
            self::assertNull($user->id);
            self::assertNull($user->name);
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
        self::assertNull($post->id);

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
        self::assertNull($post->id);

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
        self::assertNull($post->id);

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
        self::assertNull($post->id);

        $post->belongsTo(User::class, 'not_found_target_key', 'user_id');
    }

    protected function getDatabaseTable(): array
    {
        return ['post', 'user'];
    }
}
