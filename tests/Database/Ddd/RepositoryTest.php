<?php

declare(strict_types=1);

namespace Tests\Database\Ddd;

use Leevel\Database\Ddd\Entity;
use Leevel\Database\Ddd\EntityCollection as Collection;
use Leevel\Database\Ddd\Repository;
use Leevel\Database\Ddd\Select;
use Leevel\Database\Page;
use Leevel\Di\Container;
use Leevel\Event\Dispatch;
use Leevel\Kernel\Utils\Api;
use Leevel\Page\Page as BasePage;
use Tests\Database\DatabaseTestCase as TestCase;
use Tests\Database\Ddd\Entity\DemoUnique;
use Tests\Database\Ddd\Entity\Relation\Post;

#[Api([
    'zh-CN:title' => '仓储',
    'path' => 'orm/repository',
    'zh-CN:description' => <<<'EOT'
仓储层可以看作是对实体的一种包装，通过构造器注入的实体。
EOT,
])]
final class RepositoryTest extends TestCase
{
    #[Api([
        'zh-CN:title' => '基本使用方法',
    ])]
    public function testBase(): void
    {
        $connect = $this->createDatabaseConnect();

        static::assertSame(
            1,
            $connect
                ->table('post')
                ->insert([
                    'title' => 'hello world',
                    'user_id' => 1,
                    'summary' => 'post summary',
                    'delete_at' => 0,
                ])
        );

        $repository = new Repository(new Post());

        $newPost = $repository->findEntity(1);

        $this->assertInstanceof(Post::class, $newPost);
        static::assertSame(1, $newPost->id);
        static::assertSame(1, $newPost->userId);
        static::assertSame('hello world', $newPost->title);
        static::assertSame('post summary', $newPost->summary);
        $this->assertInstanceof(Post::class, $repository->entity());
    }

    #[Api([
        'zh-CN:title' => 'findEntity 通过主键查找实体',
    ])]
    public function testFindEntity(): void
    {
        $connect = $this->createDatabaseConnect();

        static::assertSame(
            1,
            $connect
                ->table('post')
                ->insert([
                    'title' => 'hello world',
                    'user_id' => 1,
                    'summary' => 'post summary',
                    'delete_at' => 0,
                ])
        );

        $repository = new Repository(new Post());

        $newPost = $repository->findEntity(1);

        $this->assertInstanceof(Post::class, $newPost);
        static::assertSame(1, $newPost->id);
        static::assertSame(1, $newPost->userId);
        static::assertSame('hello world', $newPost->title);
        static::assertSame('post summary', $newPost->summary);
    }

    #[Api([
        'zh-CN:title' => '批量插入数据 insertAll',
    ])]
    public function testInsertAll(): void
    {
        $repository = new Repository(new Post());
        $repository->insertAll([[
            'title' => 'hello world',
            'user_id' => 1,
            'summary' => 'post summary',
            'delete_at' => 0,
        ]]);

        $newPost = $repository->findEntity(1);

        $this->assertInstanceof(Post::class, $newPost);
        static::assertSame(1, $newPost->id);
        static::assertSame(1, $newPost->userId);
        static::assertSame('hello world', $newPost->title);
        static::assertSame('post summary', $newPost->summary);
    }

    #[Api([
        'zh-CN:title' => '批量插入回调 insertAllBoot',
    ])]
    public function testInsertAllBoot(): void
    {
        $repository = new Repository(new Post());
        $repository->insertAllBoot(function (array &$data): void {
            $data[0]['title'] = 'new';
        });
        $repository->insertAll([[
            'title' => 'hello world',
            'user_id' => 1,
            'summary' => 'post summary',
            'delete_at' => 0,
        ]]);

        $newPost = $repository->findEntity(1);

        $this->assertInstanceof(Post::class, $newPost);
        static::assertSame(1, $newPost->id);
        static::assertSame(1, $newPost->userId);
        static::assertSame('new', $newPost->title);
        static::assertSame('post summary', $newPost->summary);
    }

    #[Api([
        'zh-CN:title' => 'findAll 取得所有记录',
    ])]
    public function testFindAll(): void
    {
        $connect = $this->createDatabaseConnect();

        for ($i = 0; $i < 10; ++$i) {
            $connect
                ->table('post')
                ->insert([
                    'title' => 'hello world',
                    'user_id' => 1,
                    'summary' => 'post summary',
                    'delete_at' => 0,
                ])
            ;
        }

        $repository = new Repository(new Post());
        $select = $repository->condition();
        $result = $repository->findAll();

        $sql = <<<'eot'
            SQL: [70] SELECT `post`.* FROM `post` WHERE `post`.`delete_at` = :post_delete_at | Params:  1 | Key: Name: [15] :post_delete_at | paramno=0 | name=[15] ":post_delete_at" | is_param=1 | param_type=1 (SELECT `post`.* FROM `post` WHERE `post`.`delete_at` = 0)
            eot;
        static::assertSame(
            $sql,
            $select->getLastSql(),
        );

        $this->assertInstanceof(Select::class, $select);
        $this->assertInstanceof(Collection::class, $result);
        static::assertCount(10, $result);
    }

    #[Api([
        'zh-CN:title' => 'findAll 取得所有记录支持查询条件',
    ])]
    public function testFindAll2(): void
    {
        $connect = $this->createDatabaseConnect();

        for ($i = 0; $i < 10; ++$i) {
            $connect
                ->table('post')
                ->insert([
                    'title' => 'hello world',
                    'user_id' => 1,
                    'summary' => 'post summary',
                    'delete_at' => 0,
                ])
            ;
        }

        $repository = new Repository(new Post());
        $result = $repository->findAll(function (Select $select): void {
            $select->where('id', '<', 8);
        });

        $sql = <<<'eot'
            SQL: [97] SELECT `post`.* FROM `post` WHERE `post`.`delete_at` = :post_delete_at AND `post`.`id` < :post_id | Params:  2 | Key: Name: [15] :post_delete_at | paramno=0 | name=[15] ":post_delete_at" | is_param=1 | param_type=1 | Key: Name: [8] :post_id | paramno=1 | name=[8] ":post_id" | is_param=1 | param_type=1 (SELECT `post`.* FROM `post` WHERE `post`.`delete_at` = 0 AND `post`.`id` < 8)
            eot;
        static::assertSame(
            $sql,
            $repository->getLastSql(),
        );

        $this->assertInstanceof(Collection::class, $result);
        static::assertCount(7, $result);
    }

    #[Api([
        'zh-CN:title' => 'findCount 取得记录数量',
    ])]
    public function testFindCount(): void
    {
        $connect = $this->createDatabaseConnect();

        for ($i = 0; $i < 10; ++$i) {
            $connect
                ->table('post')
                ->insert([
                    'title' => 'hello world',
                    'user_id' => 1,
                    'summary' => 'post summary',
                    'delete_at' => 0,
                ])
            ;
        }

        $repository = new Repository(new Post());
        $select = $repository->condition();
        $result = $repository->findCount();

        $sql = <<<'eot'
            SQL: [91] SELECT COUNT(*) AS row_count FROM `post` WHERE `post`.`delete_at` = :post_delete_at LIMIT 1 | Params:  1 | Key: Name: [15] :post_delete_at | paramno=0 | name=[15] ":post_delete_at" | is_param=1 | param_type=1 (SELECT COUNT(*) AS row_count FROM `post` WHERE `post`.`delete_at` = 0 LIMIT 1)
            eot;
        static::assertSame(
            $sql,
            $select->getLastSql(),
        );

        $this->assertInstanceof(Select::class, $select);
        static::assertSame(10, $result);
    }

    #[Api([
        'zh-CN:title' => 'findCount 取得记录数量支持查询条件',
    ])]
    public function testFindCount2(): void
    {
        $connect = $this->createDatabaseConnect();

        for ($i = 0; $i < 10; ++$i) {
            $connect
                ->table('post')
                ->insert([
                    'title' => 'hello world',
                    'user_id' => 1,
                    'summary' => 'post summary',
                    'delete_at' => 0,
                ])
            ;
        }

        $repository = new Repository(new Post());
        $result = $repository->findCount(function (Select $select): void {
            $select->where('id', '<', 8);
        });

        $sql = <<<'eot'
            SQL: [118] SELECT COUNT(*) AS row_count FROM `post` WHERE `post`.`delete_at` = :post_delete_at AND `post`.`id` < :post_id LIMIT 1 | Params:  2 | Key: Name: [15] :post_delete_at | paramno=0 | name=[15] ":post_delete_at" | is_param=1 | param_type=1 | Key: Name: [8] :post_id | paramno=1 | name=[8] ":post_id" | is_param=1 | param_type=1 (SELECT COUNT(*) AS row_count FROM `post` WHERE `post`.`delete_at` = 0 AND `post`.`id` < 8 LIMIT 1)
            eot;
        static::assertSame(
            $sql,
            $repository->getLastSql(),
        );

        static::assertSame(7, $result);
    }

    #[Api([
        'zh-CN:title' => 'insertAll 支持事件',
    ])]
    public function testInsertAllEvent(): void
    {
        $dispatch = new Dispatch(new Container());
        $repository = new Repository(new Post(), $dispatch);
        $dispatch->register(Repository::INSERT_ALL_EVENT, function (string $event, Repository $repository): void {
            $repository->insertAllBoot(function (array &$data): void {
                $data[0]['title'] = 'new';
            });
        });
        $repository->insertAll([[
            'title' => 'hello world',
            'user_id' => 1,
            'summary' => 'post summary',
            'delete_at' => 0,
        ]]);

        $newPost = $repository->findEntity(1);

        $this->assertInstanceof(Post::class, $newPost);
        static::assertSame(1, $newPost->id);
        static::assertSame(1, $newPost->userId);
        static::assertSame('new', $newPost->title);
        static::assertSame('post summary', $newPost->summary);
    }

    #[Api([
        'zh-CN:title' => 'validateDataExists 判断指定数组数据值是否存在',
    ])]
    public function testValidateDataExists(): void
    {
        $connect = $this->createDatabaseConnect();

        static::assertSame(
            1,
            $connect
                ->table('post')
                ->insert([
                    'title' => 'hello world',
                    'user_id' => 1,
                    'summary' => 'post summary',
                    'delete_at' => 0,
                ])
        );

        $repository = new Repository(new Post());
        $repository->validateDataExists([1]);
    }

    #[Api([
        'zh-CN:title' => 'validateDataExists 判断指定数组数据值是否存在支持条件查询',
    ])]
    public function testValidateDataExists3(): void
    {
        $this->expectException(\Leevel\Database\Ddd\DataNotFoundException::class);
        $this->expectExceptionMessage(
            'Data of `Tests\\Database\\Ddd\\Entity\\Relation\Post` was not found.'
        );

        $connect = $this->createDatabaseConnect();

        static::assertSame(
            1,
            $connect
                ->table('post')
                ->insert([
                    'title' => 'hello world',
                    'user_id' => 1,
                    'summary' => 'post summary',
                    'delete_at' => 0,
                ])
        );

        $repository = new Repository(new Post());
        $repository->validateDataExists([1], condition: function (Select $select): void {
            $select->where('id', '>', 1);
        });
    }

    #[Api([
        'zh-CN:title' => 'validateDataExists 判断指定数组数据值是否存在支持字段查询',
    ])]
    public function testValidateDataExists4(): void
    {
        $this->expectException(\Leevel\Database\Ddd\DataNotFoundException::class);
        $this->expectExceptionMessage(
            'Data of `Tests\\Database\\Ddd\\Entity\\Relation\Post` was not found.'
        );

        $connect = $this->createDatabaseConnect();

        static::assertSame(
            1,
            $connect
                ->table('post')
                ->insert([
                    'title' => 'hello world',
                    'user_id' => 1,
                    'summary' => 'post summary',
                    'delete_at' => 0,
                ])
        );

        $repository = new Repository(new Post());
        $repository->validateDataExists([1], 'title');
    }

    #[Api([
        'zh-CN:title' => 'validateDataExists 判断指定数组数据值是否存在(不存在的情况)',
    ])]
    public function testValidateDataExists2(): void
    {
        $this->expectException(\Leevel\Database\Ddd\DataNotFoundException::class);
        $this->expectExceptionMessage(
            'Data of `Tests\\Database\\Ddd\\Entity\\Relation\Post` was not found.'
        );

        $connect = $this->createDatabaseConnect();

        static::assertSame(
            1,
            $connect
                ->table('post')
                ->insert([
                    'title' => 'hello world',
                    'user_id' => 1,
                    'summary' => 'post summary',
                    'delete_at' => 0,
                ])
        );

        $repository = new Repository(new Post());
        $repository->validateDataExists([1, 2]);
    }

    #[Api([
        'zh-CN:title' => 'findOrFail 通过主键查找实体，未找到则抛出异常',
    ])]
    public function testFindOrFail(): void
    {
        $connect = $this->createDatabaseConnect();

        static::assertSame(
            1,
            $connect
                ->table('post')
                ->insert([
                    'title' => 'hello world',
                    'user_id' => 1,
                    'summary' => 'post summary',
                    'delete_at' => 0,
                ])
        );

        $repository = new Repository(new Post());

        $newPost = $repository->findOrFail(1);

        $this->assertInstanceof(Post::class, $newPost);
        static::assertSame(1, $newPost->id);
        static::assertSame(1, $newPost->userId);
        static::assertSame('hello world', $newPost->title);
        static::assertSame('post summary', $newPost->summary);
    }

    #[Api([
        'zh-CN:title' => 'findOrFail 通过主键查找实体，未找到则抛出异常例子',
    ])]
    public function testFindOrFailNotFound(): void
    {
        $this->expectException(\Leevel\Database\Ddd\EntityNotFoundException::class);
        $this->expectExceptionMessage(
            'Entity `Tests\\Database\\Ddd\\Entity\\Relation\\Post` was not found.'
        );

        $repository = new Repository(new Post());

        $newPost = $repository->findOrFail(1);
    }

    #[Api([
        'zh-CN:title' => '__call 魔术方法访问实体查询',
    ])]
    public function testCall(): void
    {
        $connect = $this->createDatabaseConnect();

        static::assertSame(
            1,
            $connect
                ->table('post')
                ->insert([
                    'title' => 'hello world',
                    'user_id' => 1,
                    'summary' => 'post summary',
                    'delete_at' => 0,
                ])
        );

        $repository = new Repository(new Post());

        $newPost = $repository
            ->where('id', 5)
            ->findEntity(1)
        ;

        $this->assertInstanceof(Post::class, $newPost);
        static::assertNull($newPost->id);
        static::assertNull($newPost->userId);
        static::assertNull($newPost->title);
        static::assertNull($newPost->summary);
    }

    #[Api([
        'zh-CN:title' => 'createEntity 新增实体',
    ])]
    public function testCreateTwice(): void
    {
        $repository = new Repository(new Post());

        $repository->createEntity($post = new Post([
            'id' => 5,
            'title' => 'foo',
            'user_id' => 0,
        ]));

        static::assertSame('SQL: [129] INSERT INTO `post` (`post`.`id`,`post`.`title`,`post`.`user_id`) VALUES (:named_param_id,:named_param_title,:named_param_user_id) | Params:  3 | Key: Name: [15] :named_param_id | paramno=0 | name=[15] ":named_param_id" | is_param=1 | param_type=1 | Key: Name: [18] :named_param_title | paramno=1 | name=[18] ":named_param_title" | is_param=1 | param_type=2 | Key: Name: [20] :named_param_user_id | paramno=2 | name=[20] ":named_param_user_id" | is_param=1 | param_type=1 (INSERT INTO `post` (`post`.`id`,`post`.`title`,`post`.`user_id`) VALUES (5,\'foo\',0))', $repository->getLastSql());

        static::assertSame(5, $post->id);
        static::assertSame('foo', $post->title);
        static::assertSame(0, $post->userId);
        static::assertSame([], $post->changed());
        $repository->createEntity($post);
        static::assertSame('SQL: [31] INSERT INTO `post` () VALUES () | Params:  0 (INSERT INTO `post` () VALUES ())', $repository->getLastSql());

        $newPost = $repository->findEntity(5);

        $this->assertInstanceof(Post::class, $newPost);
        static::assertSame(5, $newPost->id);
        static::assertSame('foo', $newPost->title);

        $newPost = $repository->findEntity(6);

        $this->assertInstanceof(Post::class, $newPost);
        static::assertSame(6, $newPost->id);
        static::assertSame('', $newPost->title);
    }

    #[Api([
        'zh-CN:title' => 'updateEntity 更新实体',
    ])]
    public function testUpdateTwiceAndDoNothing(): void
    {
        $connect = $this->createDatabaseConnect();

        static::assertSame(
            1,
            $connect
                ->table('post')
                ->insert([
                    'title' => 'hello world',
                    'user_id' => 1,
                    'summary' => 'post summary',
                    'delete_at' => 0,
                ])
        );

        $repository = new Repository(new Post());
        static::assertSame(1, $repository->updateEntity($post = new Post(['id' => 1, 'title' => 'new title'])));

        static::assertSame('SQL: [90] UPDATE `post` SET `post`.`title` = :named_param_title WHERE `post`.`id` = :post_id LIMIT 1 | Params:  2 | Key: Name: [18] :named_param_title | paramno=0 | name=[18] ":named_param_title" | is_param=1 | param_type=2 | Key: Name: [8] :post_id | paramno=1 | name=[8] ":post_id" | is_param=1 | param_type=1 (UPDATE `post` SET `post`.`title` = \'new title\' WHERE `post`.`id` = 1 LIMIT 1)', $repository->getLastSql());
        static::assertSame([], $post->changed());
        static::assertNull($repository->updateEntity($post));
    }

    #[Api([
        'zh-CN:title' => 'replaceEntity 替换实体',
    ])]
    public function testReplaceTwiceAndFindExistData(): void
    {
        $connect = $this->createDatabaseConnect();

        static::assertSame(
            1,
            $connect
                ->table('post')
                ->insert([
                    'title' => 'hello world',
                    'user_id' => 1,
                    'summary' => 'post summary',
                    'delete_at' => 0,
                ])
        );

        $repository = new Repository(new Post());
        $affectedRow = $repository->replaceEntity($post = new Post([
            'id' => 1,
            'title' => 'new title',
            'user_id' => 1,
        ]));
        static::assertSame('SQL: [130] UPDATE `post` SET `post`.`title` = :named_param_title,`post`.`user_id` = :named_param_user_id WHERE `post`.`id` = :post_id LIMIT 1 | Params:  3 | Key: Name: [18] :named_param_title | paramno=0 | name=[18] ":named_param_title" | is_param=1 | param_type=2 | Key: Name: [20] :named_param_user_id | paramno=1 | name=[20] ":named_param_user_id" | is_param=1 | param_type=1 | Key: Name: [8] :post_id | paramno=2 | name=[8] ":post_id" | is_param=1 | param_type=1 (UPDATE `post` SET `post`.`title` = \'new title\',`post`.`user_id` = 1 WHERE `post`.`id` = 1 LIMIT 1)', $repository->getLastSql());

        static::assertSame(1, $affectedRow);
        static::assertSame([], $post->changed());

        $repository->replaceEntity($post); // 新增一条数据.
        static::assertSame('SQL: [31] INSERT INTO `post` () VALUES () | Params:  0 (INSERT INTO `post` () VALUES ())', $repository->getLastSql());

        $updatedPost = $repository->findEntity(1);
        static::assertSame(1, $updatedPost->id);
        static::assertSame('new title', $updatedPost->title);
        static::assertSame(1, $updatedPost->userId);
        static::assertSame('post summary', $updatedPost->summary);

        $newPost2 = $repository->findEntity(2);

        $this->assertInstanceof(Post::class, $newPost2);
        static::assertSame(2, $newPost2->id);
        static::assertSame('', $newPost2->title);
        static::assertSame('', $newPost2->summary);
    }

    public function testReplaceTwiceAndNotFindExistData(): void
    {
        $connect = $this->createDatabaseConnect();

        static::assertSame(
            1,
            $connect
                ->table('post')
                ->insert([
                    'title' => 'hello world',
                    'user_id' => 1,
                    'summary' => 'post summary',
                    'delete_at' => 0,
                ])
        );

        $repository = new Repository(new Post());
        $post = new Post([
            'id' => 2,
            'title' => 'new title',
            'user_id' => 0,
        ]);
        static::assertTrue($post->newed());
        $repository->replaceEntity($post);
        static::assertSame($insertSql = 'SQL: [129] INSERT INTO `post` (`post`.`id`,`post`.`title`,`post`.`user_id`) VALUES (:named_param_id,:named_param_title,:named_param_user_id) | Params:  3 | Key: Name: [15] :named_param_id | paramno=0 | name=[15] ":named_param_id" | is_param=1 | param_type=1 | Key: Name: [18] :named_param_title | paramno=1 | name=[18] ":named_param_title" | is_param=1 | param_type=2 | Key: Name: [20] :named_param_user_id | paramno=2 | name=[20] ":named_param_user_id" | is_param=1 | param_type=1 (INSERT INTO `post` (`post`.`id`,`post`.`title`,`post`.`user_id`) VALUES (2,\'new title\',0))', $repository->getLastSql());
        static::assertSame([], $post->changed());
        static::assertFalse($post->newed()); // 新增数据后实体变为对应数据库一条记录非新记录
        $repository->replaceEntity($post); // 更新数据，但是没有数据需要更新不做任何处理.
        static::assertSame($insertSql, $repository->getLastSql());

        $newPost = $repository->findEntity(1);

        $this->assertInstanceof(Post::class, $newPost);
        static::assertSame(1, $newPost->id);
        static::assertSame('hello world', $newPost->title);
        static::assertSame('post summary', $newPost->summary);

        $newPost2 = $repository->findEntity(2);

        $this->assertInstanceof(Post::class, $newPost2);
        static::assertSame(2, $newPost2->id);
        static::assertSame('new title', $newPost2->title);
        static::assertSame('', $newPost2->summary);

        $newPost3 = $repository->findEntity(3);

        $this->assertInstanceof(Post::class, $newPost3);
        static::assertNull($newPost3->id);
        static::assertNull($newPost3->title);
        static::assertNull($newPost3->summary);
    }

    public function testReplaceUnique(): void
    {
        $connect = $this->createDatabaseConnect();

        static::assertSame(
            1,
            $connect
                ->table('test_unique')
                ->insert([
                    'name' => 'hello world',
                    'identity' => 'hello',
                ])
        );

        $testUniqueData = DemoUnique::select()->findEntity(1);

        $this->assertInstanceof(DemoUnique::class, $testUniqueData);
        static::assertSame(1, $testUniqueData->id);
        static::assertSame('hello world', $testUniqueData->name);
        static::assertSame('hello', $testUniqueData->identity);

        $testUnique = new DemoUnique(['id' => 1, 'name' => 'hello new', 'identity' => 'hello']);

        $repository = new Repository($testUnique);
        $repository->replaceEntity($testUnique);

        $testUniqueData = DemoUnique::select()->findEntity(1);

        $this->assertInstanceof(DemoUnique::class, $testUniqueData);
        static::assertSame(1, $testUniqueData->id);
        static::assertSame('hello new', $testUniqueData->name);
        static::assertSame('hello', $testUniqueData->identity);
    }

    #[Api([
        'zh-CN:title' => 'deleteEntity 响应删除',
    ])]
    public function testSoftDeleteTwice(): void
    {
        $connect = $this->createDatabaseConnect();

        static::assertSame(
            1,
            $connect
                ->table('post')
                ->insert([
                    'title' => 'hello world',
                    'user_id' => 1,
                    'summary' => 'post summary',
                    'delete_at' => 0,
                ])
        );

        $repository = new Repository(new Post());

        $repository->deleteEntity($post = new Post(['id' => 1, 'title' => 'new title']));
        $sql = 'SQL: [98] UPDATE `post` SET `post`.`delete_at` = :named_param_delete_at WHERE `post`.`id` = :post_id LIMIT 1 | Params:  2 | Key: Name: [22] :named_param_delete_at | paramno=0 | name=[22] ":named_param_delete_at" | is_param=1 | param_type=1 | Key: Name: [8] :post_id | paramno=1 | name=[8] ":post_id" | is_param=1 | param_type=1 (UPDATE `post` SET `post`.`delete_at` = %d WHERE `post`.`id` = 1 LIMIT 1)';
        static::assertTrue(\in_array($repository->getLastSql(), [
            sprintf($sql, time() - 1),
            sprintf($sql, time()),
            sprintf($sql, time() + 1),
        ], true));

        $repository->deleteEntity($post); // 将会更新 `delete_at` 字段.
        $sql = 'SQL: [98] UPDATE `post` SET `post`.`delete_at` = :named_param_delete_at WHERE `post`.`id` = :post_id LIMIT 1 | Params:  2 | Key: Name: [22] :named_param_delete_at | paramno=0 | name=[22] ":named_param_delete_at" | is_param=1 | param_type=1 | Key: Name: [8] :post_id | paramno=1 | name=[8] ":post_id" | is_param=1 | param_type=1 (UPDATE `post` SET `post`.`delete_at` = %d WHERE `post`.`id` = 1 LIMIT 1)';
        static::assertTrue(\in_array($repository->getLastSql(), [
            sprintf($sql, time() - 1),
            sprintf($sql, time()),
            sprintf($sql, time() + 1),
        ], true));

        $newPost = $repository->findEntity(1);

        $this->assertInstanceof(Post::class, $newPost);
        static::assertNull($newPost->id);
        static::assertNull($newPost->userId);
        static::assertNull($newPost->title);
        static::assertNull($newPost->summary);
    }

    #[Api([
        'zh-CN:title' => 'forceDeleteEntity 强制删除实体',
    ])]
    public function testForceDeleteTwice(): void
    {
        $connect = $this->createDatabaseConnect();

        static::assertSame(
            1,
            $connect
                ->table('post')
                ->insert([
                    'title' => 'hello world',
                    'user_id' => 1,
                    'summary' => 'post summary',
                    'delete_at' => 0,
                ])
        );

        $repository = new Repository(new Post());

        $repository->forceDeleteEntity($post = new Post(['id' => 1, 'title' => 'new title']));
        static::assertSame('SQL: [55] DELETE FROM `post` WHERE `post`.`id` = :post_id LIMIT 1 | Params:  1 | Key: Name: [8] :post_id | paramno=0 | name=[8] ":post_id" | is_param=1 | param_type=1 (DELETE FROM `post` WHERE `post`.`id` = 1 LIMIT 1)', $repository->getLastSql());
        $repository->forceDeleteEntity($post); // 会执行 SQL，因为已经删除，没有任何影响.
        static::assertSame('SQL: [55] DELETE FROM `post` WHERE `post`.`id` = :post_id LIMIT 1 | Params:  1 | Key: Name: [8] :post_id | paramno=0 | name=[8] ":post_id" | is_param=1 | param_type=1 (DELETE FROM `post` WHERE `post`.`id` = 1 LIMIT 1)', $repository->getLastSql());
        $newPost = $repository->findEntity(1);

        $this->assertInstanceof(Post::class, $newPost);
        static::assertNull($newPost->id);
        static::assertNull($newPost->userId);
        static::assertNull($newPost->title);
        static::assertNull($newPost->summary);
    }

    #[Api([
        'zh-CN:title' => 'condition 条件查询器支持闭包',
    ])]
    public function testConditionIsClosure(): void
    {
        $connect = $this->createDatabaseConnect();

        for ($i = 0; $i < 10; ++$i) {
            $connect
                ->table('post')
                ->insert([
                    'title' => 'hello world',
                    'user_id' => 1,
                    'summary' => 'post summary',
                    'delete_at' => 0,
                ])
            ;
        }

        $request = ['foo' => 'no-bar', 'hello' => 'no-world'];

        $repository = new Repository(new Post());

        $condition = function (Select $select, Entity $entity): void {
            $select->where('id', '<', 8);
        };

        $select = $repository->condition($condition);
        $result = $select->findAll();

        $this->assertInstanceof(Select::class, $select);
        $this->assertInstanceof(Collection::class, $result);
        static::assertCount(7, $result);
    }

    public function testConditionTypeIsInvalid(): void
    {
        $this->expectException(\TypeError::class);

        $repository = new Repository(new Post());
        $repository->condition(5);
    }

    #[Api([
        'zh-CN:title' => 'findPage 分页查询',
    ])]
    public function testFindPage(): void
    {
        $connect = $this->createDatabaseConnect();

        for ($i = 0; $i < 10; ++$i) {
            $connect
                ->table('post')
                ->insert([
                    'title' => 'hello world',
                    'user_id' => 1,
                    'summary' => 'post summary',
                    'delete_at' => 0,
                ])
            ;
        }

        $repository = new Repository(new Post());

        $page = $repository->findPage(1, 10);
        $result = $page->getData();

        $this->assertInstanceof(BasePage::class, $page);
        $this->assertInstanceof(Page::class, $page);
        $this->assertInstanceof(Collection::class, $result);
        static::assertCount(10, $result);
    }

    #[Api([
        'zh-CN:title' => 'findPage 分页查询支持条件过滤',
    ])]
    public function testFindPageWithCondition(): void
    {
        $connect = $this->createDatabaseConnect();

        for ($i = 0; $i < 10; ++$i) {
            $connect
                ->table('post')
                ->insert([
                    'title' => 'hello world',
                    'user_id' => 1,
                    'summary' => 'post summary',
                    'delete_at' => 0,
                ])
            ;
        }

        $request = ['foo' => 'no-bar', 'hello' => 'no-world'];

        $repository = new Repository(new Post());

        $condition = function (Select $select, Entity $entity): void {
            $select->where('id', '<', 8);
        };

        $page = $repository->findPage(1, 10, $condition);
        $result = $page->getData();

        $this->assertInstanceof(BasePage::class, $page);
        $this->assertInstanceof(Page::class, $page);
        $this->assertInstanceof(Collection::class, $result);
        static::assertCount(7, $result);
    }

    #[Api([
        'zh-CN:title' => 'findPageMacro 创建一个无限数据的分页查询',
    ])]
    public function testFindPageMacro(): void
    {
        $connect = $this->createDatabaseConnect();

        for ($i = 0; $i < 10; ++$i) {
            $connect
                ->table('post')
                ->insert([
                    'title' => 'hello world',
                    'user_id' => 1,
                    'summary' => 'post summary',
                    'delete_at' => 0,
                ])
            ;
        }

        $repository = new Repository(new Post());

        $page = $repository->findPageMacro(1, 10);
        $result = $page->getData();

        $this->assertInstanceof(BasePage::class, $page);
        $this->assertInstanceof(Page::class, $page);
        $this->assertInstanceof(Collection::class, $result);
        static::assertCount(10, $result);
    }

    #[Api([
        'zh-CN:title' => 'findPageMacro 创建一个无限数据的分页查询支持条件过滤',
    ])]
    public function testFindPageMacroWithCondition(): void
    {
        $connect = $this->createDatabaseConnect();

        for ($i = 0; $i < 10; ++$i) {
            $connect
                ->table('post')
                ->insert([
                    'title' => 'hello world',
                    'user_id' => 1,
                    'summary' => 'post summary',
                    'delete_at' => 0,
                ])
            ;
        }

        $request = ['foo' => 'no-bar', 'hello' => 'no-world'];

        $repository = new Repository(new Post());

        $condition = function (Select $select, Entity $entity): void {
            $select->where('id', '<', 8);
        };

        $page = $repository->findPageMacro(1, 10, $condition);
        $result = $page->getData();

        $this->assertInstanceof(BasePage::class, $page);
        $this->assertInstanceof(Page::class, $page);
        $this->assertInstanceof(Collection::class, $result);
        static::assertCount(7, $result);
    }

    #[Api([
        'zh-CN:title' => 'findPagePrevNext 创建一个只有上下页的分页查询',
    ])]
    public function testFindPagePrevNext(): void
    {
        $connect = $this->createDatabaseConnect();

        for ($i = 0; $i < 10; ++$i) {
            $connect
                ->table('post')
                ->insert([
                    'title' => 'hello world',
                    'user_id' => 1,
                    'summary' => 'post summary',
                    'delete_at' => 0,
                ])
            ;
        }

        $repository = new Repository(new Post());

        $page = $repository->findPagePrevNext(1, 10);
        $result = $page->getData();

        $this->assertInstanceof(BasePage::class, $page);
        $this->assertInstanceof(Page::class, $page);
        $this->assertInstanceof(Collection::class, $result);
        static::assertCount(10, $result);
    }

    #[Api([
        'zh-CN:title' => 'findPagePrevNext 创建一个只有上下页的分页查询支持条件过滤',
    ])]
    public function testFindPagePrevNextWithCondition(): void
    {
        $connect = $this->createDatabaseConnect();

        for ($i = 0; $i < 10; ++$i) {
            $connect
                ->table('post')
                ->insert([
                    'title' => 'hello world',
                    'user_id' => 1,
                    'summary' => 'post summary',
                    'delete_at' => 0,
                ])
            ;
        }

        $request = ['foo' => 'no-bar', 'hello' => 'no-world'];

        $repository = new Repository(new Post());

        $condition = function (Select $select, Entity $entity): void {
            $select->where('id', '<', 8);
        };

        $page = $repository->findPagePrevNext(1, 10, $condition);
        $result = $page->getData();

        $this->assertInstanceof(BasePage::class, $page);
        $this->assertInstanceof(Page::class, $page);
        $this->assertInstanceof(Collection::class, $result);
        static::assertCount(7, $result);
    }

    #[Api([
        'zh-CN:title' => 'findList 返回一列数据',
    ])]
    public function testFindList(): void
    {
        $connect = $this->createDatabaseConnect();

        for ($i = 0; $i < 10; ++$i) {
            $connect
                ->table('post')
                ->insert([
                    'title' => 'hello world'.$i,
                    'user_id' => 1,
                    'summary' => 'post summary',
                    'delete_at' => 0,
                ])
            ;
        }

        $repository = new Repository(new Post());

        $result = $repository->findList(null, 'summary', 'title');

        static::assertIsArray($result);

        $data = <<<'eot'
            {
                "hello world0": "post summary",
                "hello world1": "post summary",
                "hello world2": "post summary",
                "hello world3": "post summary",
                "hello world4": "post summary",
                "hello world5": "post summary",
                "hello world6": "post summary",
                "hello world7": "post summary",
                "hello world8": "post summary",
                "hello world9": "post summary"
            }
            eot;

        static::assertSame(
            $data,
            $this->varJson(
                $result
            )
        );
    }

    public function testFindListFieldValueIsArray(): void
    {
        $connect = $this->createDatabaseConnect();

        for ($i = 0; $i < 10; ++$i) {
            $connect
                ->table('post')
                ->insert([
                    'title' => 'hello world'.$i,
                    'user_id' => 1,
                    'summary' => 'post summary',
                    'delete_at' => 0,
                ])
            ;
        }

        $repository = new Repository(new Post());

        $result = $repository->findList(null, ['summary', 'title']);

        static::assertIsArray($result);

        $data = <<<'eot'
            {
                "hello world0": "post summary",
                "hello world1": "post summary",
                "hello world2": "post summary",
                "hello world3": "post summary",
                "hello world4": "post summary",
                "hello world5": "post summary",
                "hello world6": "post summary",
                "hello world7": "post summary",
                "hello world8": "post summary",
                "hello world9": "post summary"
            }
            eot;

        static::assertSame(
            $data,
            $this->varJson(
                $result
            )
        );
    }

    #[Api([
        'zh-CN:title' => 'findList 返回一列数据支持条件过滤',
    ])]
    public function testFindListWithCondition(): void
    {
        $connect = $this->createDatabaseConnect();

        for ($i = 0; $i < 10; ++$i) {
            $connect
                ->table('post')
                ->insert([
                    'title' => 'hello world'.$i,
                    'user_id' => 1,
                    'summary' => 'post summary',
                    'delete_at' => 0,
                ])
            ;
        }

        $repository = new Repository(new Post());

        $result = $repository->findList(function (Select $select): void {
            $select->where('id', '>', 5);
        }, ['summary', 'title']);

        static::assertIsArray($result);

        $data = <<<'eot'
            {
                "hello world5": "post summary",
                "hello world6": "post summary",
                "hello world7": "post summary",
                "hello world8": "post summary",
                "hello world9": "post summary"
            }
            eot;

        static::assertSame(
            $data,
            $this->varJson(
                $result
            )
        );
    }

    protected function getDatabaseTable(): array
    {
        return ['post', 'test_unique'];
    }
}
