<?php

declare(strict_types=1);

namespace Tests\Database\Ddd;

use Leevel\Database\Condition;
use Leevel\Database\Ddd\Meta;
use Leevel\Database\Ddd\Select;
use Leevel\Di\Container;
use Leevel\Kernel\Utils\Api;
use Leevel\Validate\Validator;
use Tests\Database\DatabaseTestCase as TestCase;
use Tests\Database\Ddd\Entity\CompositeId;
use Tests\Database\Ddd\Entity\DemoVersion;
use Tests\Database\Ddd\Entity\DemoVirtualEntity;
use Tests\Database\Ddd\Entity\EntityWithEnum;
use Tests\Database\Ddd\Entity\EntityWithEnumButClassNotFound;
use Tests\Database\Ddd\Entity\EntityWithoutAnyField;
use Tests\Database\Ddd\Entity\EntityWithoutPrimaryKey;
use Tests\Database\Ddd\Entity\EntityWithoutPrimaryKeyNullInArray;
use Tests\Database\Ddd\Entity\Relation\Post;
use Tests\Database\Ddd\Entity\Relation\PostForReplace;
use Tests\Database\Ddd\Entity\StatusEnum;
use Tests\Database\Ddd\Entity\WithoutPrimarykey;
use Tests\Database\Ddd\Entity\WithoutPrimarykeyAndAllAreKey;

#[Api([
    'zh-CN:title' => '实体',
    'path' => 'orm/entity',
    'zh-CN:description' => <<<'EOT'
实体是整个系统最为核心的基本单位，实体封装了一些常用的功能。
EOT,
])]
final class EntityTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        Container::singletons()->clear();
        Post::removeGlobalScope('hello');
    }

    public function testSetPropManyTimesDoNothing(): void
    {
        $entity = new Post();
        $entity->title = '5';
        $entity->title = '5';
        $entity->title = '5';
        $entity->title = '5';

        static::assertSame('5', $entity->title);
    }

    public function testSetPropButIsRelation(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Cannot set a relation prop `post_content` on entity `Tests\\Database\\Ddd\\Entity\\Relation\\Post`.'
        );

        $entity = new Post();
        $entity->postContent = 5;
    }

    public function testDatabaseResolverWasNotSet(): void
    {
        $this->metaWithoutDatabase();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Database resolver was not set.'
        );

        $container = new Container();
        $container->instance('app', $container);

        $entity = new Post(['title' => 'foo']);
        $entity->create()->flush();
    }

    public function testMetaSelect(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage(
            'Database connect was not set.'
        );

        $instance = Meta::instance('post');
        $instance->setDatabaseConnect();
        $instance->select();
    }

    #[Api([
        'zh-CN:title' => 'withProps 批量设置属性数据',
    ])]
    public function testWithProps(): void
    {
        $entity = new Post();
        $entity->withProps([
            'title' => 'foo',
            'summary' => 'bar',
        ]);

        static::assertSame('foo', $entity->title);
        static::assertSame('bar', $entity->summary);
        static::assertSame(['title', 'summary'], $entity->changed());
    }

    #[Api([
        'zh-CN:title' => 'description 获取枚举值对应的描述',
        'zh-CN:description' => <<<'EOT'
**fixture 定义**

**Tests\Database\Ddd\Entity\EntityWithEnum**

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Database\Ddd\Entity\EntityWithEnum::class)]}
```
EOT,
    ])]
    public function testEntityWithEnumDescription(): void
    {
        $this->initI18n();

        $entity = new EntityWithEnum([
            'title' => 'foo',
            'status' => 1,
        ]);

        static::assertSame('foo', $entity->title);
        static::assertSame(1, $entity->status);

        $data = <<<'eot'
            {
                "title": "foo"
            }
            eot;

        static::assertSame(
            $data,
            $this->varJson(
                $entity
                    ->only(['title'])
                    ->toArray()
            )
        );

        $data = <<<'eot'
            {
                "title": "foo",
                "status": 1,
                "status_enum": "启用"
            }
            eot;

        static::assertSame(
            $data,
            $this->varJson(
                $entity->toArray(),
                2
            )
        );

        static::assertSame('启用', StatusEnum::description('1'));
        static::assertSame('禁用', StatusEnum::description('0'));

        $data = <<<'eot'
            {
                "value": {
                    "DISABLE": 0,
                    "ENABLE": 1
                },
                "description": {
                    "DISABLE": "禁用",
                    "ENABLE": "启用"
                }
            }
            eot;

        static::assertSame(
            $data,
            $this->varJson(
                StatusEnum::descriptions(),
                3
            )
        );
    }

    public function testEntityWithEnumItemNotFound2(): void
    {
        $this->expectException(\OutOfBoundsException::class);
        $this->expectExceptionMessage(
            '5 is not a valid backing value for enum'
        );

        $entity = new EntityWithEnum([
            'title' => 'foo',
            'status' => 1,
        ]);

        StatusEnum::description('5');
    }

    public function testEntityWithEnumItemNotFoundAndWillThrowException(): void
    {
        $entity = new EntityWithEnum([
            'title' => 'foo',
            'status' => 5,
        ], true);

        [$validatorRules, $validatorMessages] = EntityWithEnum::columnValidators('store');
        $validator = new Validator($entity->toArray(), $validatorRules, $validatorMessages);
        $result = $validator->fail();
        static::assertTrue($result);

        $data = <<<'eot'
{
    "status": [
        ""
    ]
}
eot;

        static::assertSame(
            $data,
            $this->varJson(
                $validator->error()
            )
        );
    }

    public function testEntityWithEnumItemNotFoundAndWillBeEmptyStringNotFromStorage(): void
    {
        $entity = new EntityWithEnum([
            'title' => 'foo',
            'status' => 5,
        ]);
        [$validatorRules, $validatorMessages] = EntityWithEnum::columnValidators('store');
        $validator = new Validator($entity->toArray(), $validatorRules, $validatorMessages);
        $result = $validator->fail();
        static::assertTrue($result);

        $data = <<<'eot'
{
    "status": [
        ""
    ]
}
eot;

        static::assertSame(
            $data,
            $this->varJson(
                $validator->error()
            )
        );
    }

    #[Api([
        'zh-CN:title' => 'hasChanged 检测属性是否已经改变',
    ])]
    public function testHasChanged(): void
    {
        $entity = new Post();
        static::assertFalse($entity->hasChanged('title'));
        $entity->title = 'change';
        static::assertTrue($entity->hasChanged('title'));
    }

    #[Api([
        'zh-CN:title' => 'addChanged 添加指定属性为已改变',
    ])]
    public function testAddChanged(): void
    {
        $entity = new Post();
        $data = <<<'eot'
            []
            eot;

        static::assertSame(
            $data,
            $this->varJson(
                $entity->changed()
            )
        );

        $entity->addChanged(['user_id', 'title']);

        $data = <<<'eot'
            [
                "user_id",
                "title"
            ]
            eot;

        static::assertSame(
            $data,
            $this->varJson(
                $entity->changed(),
                1
            )
        );
    }

    public function testAddChangedMoreThanOnce(): void
    {
        $entity = new Post();
        $data = <<<'eot'
            []
            eot;

        static::assertSame(
            $data,
            $this->varJson(
                $entity->changed()
            )
        );

        $entity->addChanged(['user_id', 'title']);
        $entity->addChanged(['user_id', 'title']);

        $data = <<<'eot'
            [
                "user_id",
                "title"
            ]
            eot;

        static::assertSame(
            $data,
            $this->varJson(
                $entity->changed(),
                1
            )
        );
    }

    #[Api([
        'zh-CN:title' => 'deleteChanged 删除已改变属性',
    ])]
    public function testDeleteChanged(): void
    {
        $entity = new Post();
        $data = <<<'eot'
            []
            eot;

        static::assertSame(
            $data,
            $this->varJson(
                $entity->changed()
            )
        );

        $entity->addChanged(['user_id', 'title']);

        $data = <<<'eot'
            [
                "user_id",
                "title"
            ]
            eot;

        static::assertSame(
            $data,
            $this->varJson(
                $entity->changed(),
                1,
            )
        );

        $entity->deleteChanged(['user_id']);

        $data = <<<'eot'
            [
                "title"
            ]
            eot;

        static::assertSame(
            $data,
            $this->varJson(
                $entity->changed(),
                2,
            )
        );
    }

    #[Api([
        'zh-CN:title' => 'clearChanged 清空已改变属性',
    ])]
    public function testClearChanged(): void
    {
        $entity = new Post();
        $data = <<<'eot'
            []
            eot;

        static::assertSame(
            $data,
            $this->varJson(
                $entity->changed()
            )
        );

        $entity->addChanged(['user_id', 'title']);

        $data = <<<'eot'
            [
                "user_id",
                "title"
            ]
            eot;

        static::assertSame(
            $data,
            $this->varJson(
                $entity->changed(),
                1,
            )
        );

        $entity->clearChanged(['user_id']);

        $data = <<<'eot'
            []
            eot;

        static::assertSame(
            $data,
            $this->varJson(
                $entity->changed(),
                2,
            )
        );
    }

    public function testPrimaryKeyHasNullInArrayIsEmpty(): void
    {
        $entity = new EntityWithoutPrimaryKeyNullInArray();
        static::assertSame(['name'], $entity->primaryKey());
    }

    public function testSinglePrimaryKeyNotFound(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Entity Tests\\Database\\Ddd\\Entity\\EntityWithoutAnyField has no primary key.'
        );

        $entity = new EntityWithoutAnyField();
        static::assertNull($entity->primaryKey());
        $entity->singlePrimaryKey();
    }

    public function testSinglePrimaryKeyNotFoundAndAllIsKeyAndFoundOne(): void
    {
        $entity = new EntityWithoutPrimaryKey();
        static::assertSame(['name'], $entity->primaryKey());
        static::assertSame('name', $entity->singlePrimaryKey());
    }

    public function testSinglePrimaryKeyNotFoundAndFoundMoreThanOne(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Entity Tests\\Database\\Ddd\\Entity\\WithoutPrimarykeyAndAllAreKey does not support composite primary keys.'
        );

        $entity = new WithoutPrimarykeyAndAllAreKey();
        static::assertSame(['goods_id', 'description', 'name'], $entity->primaryKey());
        $entity->singlePrimaryKey();
    }

    public function testSinglePrimaryKeyNotSupportComposite(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Entity Tests\\Database\\Ddd\\Entity\\CompositeId does not support composite primary keys.'
        );

        $entity = new CompositeId();
        $entity->singlePrimaryKey();
    }

    #[Api([
        'zh-CN:title' => 'idCondition 获取查询主键条件',
    ])]
    public function testIdCondition(): void
    {
        $entity = new Post(['id' => 5]);
        static::assertSame(['id' => 5], $entity->idCondition());
    }

    public function testIdConditionHasNoPrimaryKeyData(): void
    {
        $this->expectException(\Leevel\Database\Ddd\EntityIdentifyConditionException::class);
        $this->expectExceptionMessage(
            'Entity Tests\\Database\\Ddd\\Entity\\Relation\\Post has no identify condition data.'
        );

        $entity = new Post();
        static::assertNull($entity->idCondition());
    }

    #[Api([
        'zh-CN:title' => '实体属性数组访问 ArrayAccess.offsetExists 支持',
    ])]
    public function testArrayAccessOffsetExists(): void
    {
        $entity = new Post(['id' => 5, 'title' => 'hello']);
        static::assertTrue(isset($entity['title']));
        static::assertFalse(isset($entity['user_id']));
    }

    #[Api([
        'zh-CN:title' => '实体属性数组访问 ArrayAccess.offsetSet 支持',
    ])]
    public function testArrayAccessOffsetSet(): void
    {
        $entity = new Post(['id' => 5]);
        static::assertFalse(isset($entity['title']));
        static::assertNull($entity->title);
        $entity['title'] = 'world';
        static::assertTrue(isset($entity['title']));
        static::assertSame('world', $entity->title);
    }

    #[Api([
        'zh-CN:title' => '实体属性数组访问 ArrayAccess.offsetGet 支持',
    ])]
    public function testArrayAccessOffsetGet(): void
    {
        $entity = new Post(['id' => 5]);
        static::assertNull($entity['title']);
        $entity['title'] = 'world';
        static::assertSame('world', $entity['title']);
    }

    #[Api([
        'zh-CN:title' => '实体属性数组访问 ArrayAccess.offsetUnset 支持',
    ])]
    public function testArrayAccessOffsetUnset(): void
    {
        $entity = new Post(['id' => 5]);
        static::assertNull($entity['title']);
        $entity['title'] = 'world';
        static::assertSame('world', $entity['title']);
        unset($entity['title']);
        static::assertNull($entity['title']);
    }

    #[Api([
        'zh-CN:title' => '实体属性访问魔术方法 __isset 支持',
    ])]
    public function testMagicIsset(): void
    {
        $entity = new Post(['id' => 5, 'title' => 'hello']);
        static::assertTrue(isset($entity->title));
        static::assertFalse(isset($entity->userId));
    }

    #[Api([
        'zh-CN:title' => '实体属性访问魔术方法 __set 支持',
    ])]
    public function testMagicSet(): void
    {
        $entity = new Post(['id' => 5]);
        static::assertFalse(isset($entity->title));
        static::assertNull($entity->title);
        $entity->title = 'world';
        static::assertTrue(isset($entity->title));
        static::assertSame('world', $entity->title);
    }

    #[Api([
        'zh-CN:title' => '实体属性访问魔术方法 __get 支持',
    ])]
    public function testMagicGet(): void
    {
        $entity = new Post(['id' => 5]);
        static::assertNull($entity->title);
        $entity->title = 'world';
        static::assertSame('world', $entity->title);
    }

    #[Api([
        'zh-CN:title' => '实体属性访问魔术方法 __unset 支持',
    ])]
    public function testMagicUnset(): void
    {
        $entity = new Post(['id' => 5]);
        static::assertNull($entity->title);
        $entity->title = 'world';
        static::assertSame('world', $entity->title);
        $entity->title = null;
        static::assertNull($entity->title);
    }

    #[Api([
        'zh-CN:title' => 'setter 设置属性值',
    ])]
    public function testCallSetter(): void
    {
        $entity = new Post(['id' => 5]);
        static::assertNull($entity->title);
        static::assertNull($entity->userId);
        $entity->setTitle('hello');
        $entity->setUserId(5);
        static::assertSame('hello', $entity->title);
        static::assertSame(5, $entity->userId);
    }

    #[Api([
        'zh-CN:title' => 'getter 获取属性值',
    ])]
    public function testCallGetter(): void
    {
        $entity = new Post(['id' => 5]);
        static::assertNull($entity->getTitle());
        static::assertNull($entity->getUserId());
        $entity->setTitle('hello');
        $entity->setUserId(5);
        static::assertSame('hello', $entity->getTitle());
        static::assertSame(5, $entity->getUserId());
    }

    public function testCallTryLoadRelation(): void
    {
        $this->expectException(\BadMethodCallException::class);
        $this->expectExceptionMessage(
            'Method `user` is not exits,maybe you can try `Tests\\Database\\Ddd\\Entity\\Relation\\Post::make()->relation(\'user\')`.'
        );

        $entity = new Post();
        $entity->user();
    }

    public function testCallTryNotFoundMethod(): void
    {
        $this->expectException(\BadMethodCallException::class);
        $this->expectExceptionMessage(
            'Method `notFoundMethod` is not exits,maybe you can try `Tests\\Database\\Ddd\\Entity\\Relation\\Post::select|make()->notFoundMethod(...)`.'
        );

        $entity = new Post();
        $entity->notFoundMethod();
    }

    public function testCallStaticTryNotFoundMethod(): void
    {
        $this->expectException(\BadMethodCallException::class);
        $this->expectExceptionMessage(
            'Method `notFoundMethod` is not exits,maybe you can try `Tests\\Database\\Ddd\\Entity\\Relation\\Post::select|make()->notFoundMethod(...)`.'
        );

        Post::notFoundMethod();
    }

    #[Api([
        'zh-CN:title' => 'find 获取实体查询对象',
    ])]
    public function testStaticFind(): void
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

        $post = Post::find()->where('id', 1)->findOne();
        static::assertSame('hello world', $post->title);
        static::assertSame(1, $post->userId);
        static::assertSame('post summary', $post->summary);
    }

    #[Api([
        'zh-CN:title' => 'connectSandbox 数据库连接沙盒',
    ])]
    public function testConnectSandbox(): void
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

        $post = Post::connectSandbox('password_right', function () {
            return Post::find()->where('id', 1)->findOne();
        });

        static::assertSame('hello world', $post->title);
        static::assertSame(1, $post->userId);
        static::assertSame('post summary', $post->summary);
    }

    public function testConnectSandboxAndPasswordIsError(): void
    {
        // 因为消息 IP 有变，所有这里不测试异常消息
        // SQLSTATE[HY000] [1045] Access denied for user 'root'@'10.0.2.2' (using password: YES)
        $this->expectException(\PDOException::class);

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

        Post::connectSandbox('password_not_right', function () {
            return Post::find()->where('id', 1)->findOne();
        });
    }

    #[Api([
        'zh-CN:title' => 'newed 确定对象是否对应数据库中的一条记录',
    ])]
    public function testNewed(): void
    {
        $entity = new Post();
        static::assertTrue($entity->newed());

        $entity = new Post(['id' => 5]);
        static::assertTrue($entity->newed());

        $entity = new Post(['id' => 5], true);
        static::assertFalse($entity->newed());
    }

    #[Api([
        'zh-CN:title' => 'withNewed 设置确定对象是否对应数据库中的一条记录',
    ])]
    public function testWithNewed(): void
    {
        $entity = new Post();
        static::assertTrue($entity->newed());
        $entity->withNewed(false);
        static::assertFalse($entity->newed());

        $entity = new Post(['id' => 5]);
        static::assertTrue($entity->newed());
        $entity->withNewed(false);
        static::assertFalse($entity->newed());

        $entity = new Post(['id' => 5], true);
        static::assertFalse($entity->newed());
        $entity->withNewed(true);
        static::assertTrue($entity->newed());
    }

    #[Api([
        'zh-CN:title' => 'original 获取原始数据',
    ])]
    public function testOriginal(): void
    {
        $entity = new Post();
        static::assertSame([], $entity->original());

        $entity = new Post($data = [
            'title' => 'hello',
            'summary' => 'world',
            'foo' => 'bar',
        ], false, true);
        static::assertSame($data, $entity->original());
        static::assertSame('hello', $entity->title);
        static::assertSame('world', $entity->summary);
    }

    #[Api([
        'zh-CN:title' => 'id 获取主键值',
    ])]
    public function testId(): void
    {
        $entity = new Post();
        static::assertFalse($entity->id());

        $entity = new Post(['id' => 5]);
        static::assertSame(['id' => 5], $entity->id());
    }

    #[Api([
        'zh-CN:title' => 'id 获取复合主键值',
        'zh-CN:description' => <<<'EOT'
**fixture 定义**

**Tests\Database\Ddd\Entity\CompositeId**

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Database\Ddd\Entity\CompositeId::class)]}
```
EOT,
    ])]
    public function testCompositeId(): void
    {
        $entity = new CompositeId();
        static::assertFalse($entity->id(false));
        static::assertFalse($entity->id());

        $entity = new CompositeId(['id1' => 5]);
        static::assertFalse($entity->id(false));
        static::assertFalse($entity->id());

        $entity = new CompositeId(['id1' => 5, 'id2' => 8]);
        static::assertSame(['id1' => 5, 'id2' => 8], $entity->id(false));
        static::assertSame(['id1' => 5, 'id2' => 8], $entity->id());
    }

    #[Api([
        'zh-CN:title' => 'refresh 从数据库重新读取当前对象的属性',
    ])]
    public function testRefresh(): void
    {
        $post1 = new Post();
        $post1->create()->flush();
        $this->assertInstanceof(Post::class, $post1);
        static::assertSame(1, $post1->id);
        static::assertNull($post1->userId);
        static::assertNull($post1->title);
        static::assertNull($post1->summary);
        static::assertNull($post1->delete_at);

        $post1->refresh();
        static::assertSame(1, $post1->id);
        static::assertSame(0, $post1->userId);
        static::assertSame('', $post1->title);
        static::assertSame('', $post1->summary);
        static::assertSame(0, $post1->delete_at);
    }

    #[Api([
        'zh-CN:title' => 'refresh 从数据库重新读取当前对象的属性支持复合主键',
    ])]
    public function testRefreshWithCompositeId(): void
    {
        $entity = new CompositeId(['id1' => 1, 'id2' => 3]);
        $entity->create()->flush();
        $this->assertInstanceof(CompositeId::class, $entity);
        static::assertSame(1, $entity->id1);
        static::assertSame(3, $entity->id2);
        static::assertNull($entity->name);

        $entity->refresh();
        static::assertSame(1, $entity->id1);
        static::assertSame(3, $entity->id2);
        static::assertSame('', $entity->name);
    }

    public function testRefreshButNoPrimaryKeyData(): void
    {
        $this->expectException(\Leevel\Database\Ddd\EntityIdentifyConditionException::class);
        $this->expectExceptionMessage(
            'Entity Tests\\Database\\Ddd\\Entity\\Relation\\Post has no identify condition data.'
        );

        $entity = new Post();
        $this->assertInstanceof(Post::class, $entity);
        static::assertNull($entity->id);

        $entity->refresh();
    }

    public function testRefreshWithCompositeIdButNoPrimaryKeyData(): void
    {
        $this->expectException(\Leevel\Database\Ddd\EntityIdentifyConditionException::class);
        $this->expectExceptionMessage(
            'Entity Tests\\Database\\Ddd\\Entity\\CompositeId has no identify condition data.'
        );

        $entity = new CompositeId();
        $entity->create()->flush();
        $this->assertInstanceof(CompositeId::class, $entity);
        static::assertNull($entity->id1);
        static::assertNull($entity->id2);
        static::assertNull($entity->name);

        $entity->refresh();
    }

    public function testRefreshWithoutPrimaryKey(): void
    {
        $this->expectException(\Leevel\Database\Ddd\EntityIdentifyConditionException::class);
        $this->expectExceptionMessage(
            'Entity Tests\\Database\\Ddd\\Entity\\EntityWithoutPrimaryKey has no identify condition data.'
        );

        $entity = new EntityWithoutPrimaryKey();
        $entity->refresh();
    }

    public function testRefreshWithoutPrimaryKeyData(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Entity Tests\\Database\\Ddd\\Entity\\EntityWithoutAnyField has no primary key.'
        );

        $entity = new EntityWithoutAnyField();
        $entity->refresh();
    }

    public function testFlushWithoutDataAndDoNothing(): void
    {
        $post1 = new Post();
        static::assertNull($post1->flush());
    }

    public function testFlushTwiceWithoutDataAndDoNothing(): void
    {
        $post1 = new Post();
        $post1->create();
        static::assertSame(1, $post1->flush());
        static::assertNull($post1->flush());
    }

    public function testReplaceAndThrowException(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage(
            'Update error'
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

        $post = new PostForReplace(['id' => 1, 'title' => 'hello', 'delete_at' => 0]);
        $post->replace();
        $post->flush();
    }

    #[Api([
        'zh-CN:title' => '构造器支持忽略未定义属性',
        'zh-CN:description' => <<<'EOT'
`$ignoreUndefinedProp` 用于数据库添加了字段，但是我们的实体并没有更新字段，查询得到的实体对象将会忽略掉新增的字段而不报错。
EOT,
    ])]
    public function testIgnoreUndefinedProp(): void
    {
        $entity = new Post(['undefined_prop' => 5], true, true);
        static::assertSame([], $entity->toArray());
    }

    #[Api([
        'zh-CN:title' => 'update 更新数据带上版本号',
        'zh-CN:description' => <<<'EOT'
可以用于并发控制，例如商品库存，客户余额等。

**fixture 定义**

**Tests\Database\Ddd\Entity\DemoVersion**

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Database\Ddd\Entity\DemoVersion::class)]}
```
EOT,
    ])]
    public function testUpdateWithVersion(): void
    {
        $connect = $this->createDatabaseConnect();

        static::assertSame(
            1,
            $connect
                ->table('test_version')
                ->insert([
                    'name' => 'xiaoniuge',
                ])
        );

        $testVersion = DemoVersion::select()->findEntity(1);

        $this->assertInstanceof(DemoVersion::class, $testVersion);
        static::assertSame(1, $testVersion->id);
        static::assertSame('xiaoniuge', $testVersion->name);
        static::assertSame('0.0000', $testVersion->availableNumber);
        static::assertSame('0.0000', $testVersion->realNumber);

        $condition = [
            'available_number' => $testVersion->availableNumber,
            'real_number' => $testVersion->realNumber,
        ];
        $testVersion->name = 'aniu';
        $testVersion->availableNumber = Condition::raw('[available_number]+1');
        $testVersion->realNumber = Condition::raw('[real_number]+3');
        static::assertSame(
            1,
            $testVersion
                ->condition($condition)
                ->update()
                ->flush()
        );
        static::assertSame('SQL: [493] UPDATE `test_version` SET `test_version`.`name` = :named_param_name,`test_version`.`available_number` = `test_version`.`available_number`+1,`test_version`.`real_number` = `test_version`.`real_number`+3,`test_version`.`version` = `test_version`.`version`+1 WHERE `test_version`.`available_number` = :test_version_available_number AND `test_version`.`real_number` = :test_version_real_number AND `test_version`.`id` = :test_version_id AND `test_version`.`version` = :test_version_version LIMIT 1 | Params:  5 | Key: Name: [17] :named_param_name | paramno=0 | name=[17] ":named_param_name" | is_param=1 | param_type=2 | Key: Name: [30] :test_version_available_number | paramno=1 | name=[30] ":test_version_available_number" | is_param=1 | param_type=2 | Key: Name: [25] :test_version_real_number | paramno=2 | name=[25] ":test_version_real_number" | is_param=1 | param_type=2 | Key: Name: [16] :test_version_id | paramno=3 | name=[16] ":test_version_id" | is_param=1 | param_type=1 | Key: Name: [21] :test_version_version | paramno=4 | name=[21] ":test_version_version" | is_param=1 | param_type=1 (UPDATE `test_version` SET `test_version`.`name` = \'aniu\',`test_version`.`available_number` = `test_version`.`available_number`+1,`test_version`.`real_number` = `test_version`.`real_number`+3,`test_version`.`version` = `test_version`.`version`+1 WHERE `test_version`.`available_number` = \'0.0000\' AND `test_version`.`real_number` = \'0.0000\' AND `test_version`.`id` = 1 AND `test_version`.`version` = 0 LIMIT 1)', $testVersion->select()->getLastSql());

        $testVersion->name = 'hello';
        static::assertSame(1, $testVersion->update()->flush());
        static::assertSame('SQL: [227] UPDATE `test_version` SET `test_version`.`version` = `test_version`.`version`+1,`test_version`.`name` = :named_param_name WHERE `test_version`.`id` = :test_version_id AND `test_version`.`version` = :test_version_version LIMIT 1 | Params:  3 | Key: Name: [17] :named_param_name | paramno=0 | name=[17] ":named_param_name" | is_param=1 | param_type=2 | Key: Name: [16] :test_version_id | paramno=1 | name=[16] ":test_version_id" | is_param=1 | param_type=1 | Key: Name: [21] :test_version_version | paramno=2 | name=[21] ":test_version_version" | is_param=1 | param_type=1 (UPDATE `test_version` SET `test_version`.`version` = `test_version`.`version`+1,`test_version`.`name` = \'hello\' WHERE `test_version`.`id` = 1 AND `test_version`.`version` = 1 LIMIT 1)', $testVersion->select()->getLastSql());
    }

    #[Api([
        'zh-CN:title' => 'update 更新数据不含版本数据则不会带上版本号',
        'zh-CN:description' => <<<'EOT'
version 对应的字段无数据，将会忽略版本号。
EOT,
    ])]
    public function testUpdateNoVersionDataWithoutVersion(): void
    {
        $connect = $this->createDatabaseConnect();

        static::assertSame(
            1,
            $connect
                ->table('test_version')
                ->insert([
                    'name' => 'xiaoniuge',
                ])
        );

        $testVersion = DemoVersion::select()
            ->findEntity(1, ['id,name,available_number,real_number'])
        ;

        $this->assertInstanceof(DemoVersion::class, $testVersion);
        static::assertSame(1, $testVersion->id);
        static::assertNull($testVersion->version);
        static::assertSame('xiaoniuge', $testVersion->name);
        static::assertSame('0.0000', $testVersion->availableNumber);
        static::assertSame('0.0000', $testVersion->realNumber);

        $condition = [
            'available_number' => $testVersion->availableNumber,
            'real_number' => $testVersion->realNumber,
        ];
        $testVersion->name = 'aniu';
        $testVersion->availableNumber = Condition::raw('[available_number]+1');
        $testVersion->realNumber = Condition::raw('[real_number]+3');
        static::assertSame(
            1,
            $testVersion
                ->condition($condition)
                ->update()
                ->flush()
        );
        static::assertSame('SQL: [386] UPDATE `test_version` SET `test_version`.`name` = :named_param_name,`test_version`.`available_number` = `test_version`.`available_number`+1,`test_version`.`real_number` = `test_version`.`real_number`+3 WHERE `test_version`.`available_number` = :test_version_available_number AND `test_version`.`real_number` = :test_version_real_number AND `test_version`.`id` = :test_version_id LIMIT 1 | Params:  4 | Key: Name: [17] :named_param_name | paramno=0 | name=[17] ":named_param_name" | is_param=1 | param_type=2 | Key: Name: [30] :test_version_available_number | paramno=1 | name=[30] ":test_version_available_number" | is_param=1 | param_type=2 | Key: Name: [25] :test_version_real_number | paramno=2 | name=[25] ":test_version_real_number" | is_param=1 | param_type=2 | Key: Name: [16] :test_version_id | paramno=3 | name=[16] ":test_version_id" | is_param=1 | param_type=1 (UPDATE `test_version` SET `test_version`.`name` = \'aniu\',`test_version`.`available_number` = `test_version`.`available_number`+1,`test_version`.`real_number` = `test_version`.`real_number`+3 WHERE `test_version`.`available_number` = \'0.0000\' AND `test_version`.`real_number` = \'0.0000\' AND `test_version`.`id` = 1 LIMIT 1)', $testVersion->select()->getLastSql());

        $testVersion->name = 'hello';
        static::assertSame(1, $testVersion->update()->flush());
        static::assertSame('SQL: [120] UPDATE `test_version` SET `test_version`.`name` = :named_param_name WHERE `test_version`.`id` = :test_version_id LIMIT 1 | Params:  2 | Key: Name: [17] :named_param_name | paramno=0 | name=[17] ":named_param_name" | is_param=1 | param_type=2 | Key: Name: [16] :test_version_id | paramno=1 | name=[16] ":test_version_id" | is_param=1 | param_type=1 (UPDATE `test_version` SET `test_version`.`name` = \'hello\' WHERE `test_version`.`id` = 1 LIMIT 1)', $testVersion->select()->getLastSql());
    }

    #[Api([
        'zh-CN:title' => 'version.condition 设置是否启用乐观锁版本字段配合设置扩展查询条件',
    ])]
    public function testUpdateWithVersionAndWithCondition(): void
    {
        $connect = $this->createDatabaseConnect();

        static::assertSame(
            1,
            $connect
                ->table('test_version')
                ->insert([
                    'name' => 'xiaoniuge',
                ])
        );

        $testVersion = DemoVersion::select()->findEntity(1);

        $this->assertInstanceof(DemoVersion::class, $testVersion);
        static::assertSame(1, $testVersion->id);
        static::assertSame('xiaoniuge', $testVersion->name);
        static::assertSame('0.0000', $testVersion->availableNumber);
        static::assertSame('0.0000', $testVersion->realNumber);

        $testVersion->name = 'aniu';
        $testVersion->availableNumber = Condition::raw('[available_number]+1');
        $testVersion->realNumber = Condition::raw('[real_number]+3');
        static::assertSame(1, $testVersion->version(true)->update()->flush());
        static::assertSame('SQL: [361] UPDATE `test_version` SET `test_version`.`name` = :named_param_name,`test_version`.`available_number` = `test_version`.`available_number`+1,`test_version`.`real_number` = `test_version`.`real_number`+3,`test_version`.`version` = `test_version`.`version`+1 WHERE `test_version`.`id` = :test_version_id AND `test_version`.`version` = :test_version_version LIMIT 1 | Params:  3 | Key: Name: [17] :named_param_name | paramno=0 | name=[17] ":named_param_name" | is_param=1 | param_type=2 | Key: Name: [16] :test_version_id | paramno=1 | name=[16] ":test_version_id" | is_param=1 | param_type=1 | Key: Name: [21] :test_version_version | paramno=2 | name=[21] ":test_version_version" | is_param=1 | param_type=1 (UPDATE `test_version` SET `test_version`.`name` = \'aniu\',`test_version`.`available_number` = `test_version`.`available_number`+1,`test_version`.`real_number` = `test_version`.`real_number`+3,`test_version`.`version` = `test_version`.`version`+1 WHERE `test_version`.`id` = 1 AND `test_version`.`version` = 0 LIMIT 1)', $testVersion->select()->getLastSql());

        $testVersion->refresh();
        $condition = ['available_number' => $testVersion->availableNumber];
        $testVersion->name = 'hello';
        $testVersion->availableNumber = Condition::raw('[available_number]+8');
        static::assertSame(1, $testVersion->condition($condition)->update()->flush());
        static::assertSame('SQL: [370] UPDATE `test_version` SET `test_version`.`version` = `test_version`.`version`+1,`test_version`.`name` = :named_param_name,`test_version`.`available_number` = `test_version`.`available_number`+8 WHERE `test_version`.`available_number` = :test_version_available_number AND `test_version`.`id` = :test_version_id AND `test_version`.`version` = :test_version_version LIMIT 1 | Params:  4 | Key: Name: [17] :named_param_name | paramno=0 | name=[17] ":named_param_name" | is_param=1 | param_type=2 | Key: Name: [30] :test_version_available_number | paramno=1 | name=[30] ":test_version_available_number" | is_param=1 | param_type=2 | Key: Name: [16] :test_version_id | paramno=2 | name=[16] ":test_version_id" | is_param=1 | param_type=1 | Key: Name: [21] :test_version_version | paramno=3 | name=[21] ":test_version_version" | is_param=1 | param_type=1 (UPDATE `test_version` SET `test_version`.`version` = `test_version`.`version`+1,`test_version`.`name` = \'hello\',`test_version`.`available_number` = `test_version`.`available_number`+8 WHERE `test_version`.`available_number` = \'1.0000\' AND `test_version`.`id` = 1 AND `test_version`.`version` = 1 LIMIT 1)', $testVersion->select()->getLastSql());
    }

    #[Api([
        'zh-CN:title' => 'version 设置是否启用乐观锁版本字段支持取消',
    ])]
    public function testUpdateWithVersionAndWithoutVersionCondition(): void
    {
        $connect = $this->createDatabaseConnect();

        static::assertSame(
            1,
            $connect
                ->table('test_version')
                ->insert([
                    'name' => 'xiaoniuge',
                ])
        );

        $testVersion = DemoVersion::select()->findEntity(1);

        $this->assertInstanceof(DemoVersion::class, $testVersion);
        static::assertSame(1, $testVersion->id);
        static::assertSame('xiaoniuge', $testVersion->name);
        static::assertSame('0.0000', $testVersion->availableNumber);
        static::assertSame('0.0000', $testVersion->realNumber);

        $testVersion->name = 'aniu';
        $testVersion->availableNumber = Condition::raw('[available_number]+1');
        $testVersion->realNumber = Condition::raw('[real_number]+3');
        static::assertSame(1, $testVersion->version(false)->update()->flush());
        static::assertSame('SQL: [254] UPDATE `test_version` SET `test_version`.`name` = :named_param_name,`test_version`.`available_number` = `test_version`.`available_number`+1,`test_version`.`real_number` = `test_version`.`real_number`+3 WHERE `test_version`.`id` = :test_version_id LIMIT 1 | Params:  2 | Key: Name: [17] :named_param_name | paramno=0 | name=[17] ":named_param_name" | is_param=1 | param_type=2 | Key: Name: [16] :test_version_id | paramno=1 | name=[16] ":test_version_id" | is_param=1 | param_type=1 (UPDATE `test_version` SET `test_version`.`name` = \'aniu\',`test_version`.`available_number` = `test_version`.`available_number`+1,`test_version`.`real_number` = `test_version`.`real_number`+3 WHERE `test_version`.`id` = 1 LIMIT 1)', $testVersion->select()->getLastSql());

        $testVersion->name = 'hello';
        static::assertSame(1, $testVersion->update()->flush());
        static::assertSame('SQL: [120] UPDATE `test_version` SET `test_version`.`name` = :named_param_name WHERE `test_version`.`id` = :test_version_id LIMIT 1 | Params:  2 | Key: Name: [17] :named_param_name | paramno=0 | name=[17] ":named_param_name" | is_param=1 | param_type=2 | Key: Name: [16] :test_version_id | paramno=1 | name=[16] ":test_version_id" | is_param=1 | param_type=1 (UPDATE `test_version` SET `test_version`.`name` = \'hello\' WHERE `test_version`.`id` = 1 LIMIT 1)', $testVersion->select()->getLastSql());
    }

    #[Api([
        'zh-CN:title' => 'condition 设置扩展查询条件支持直接设置版本查询条件',
    ])]
    public function testUpdateWithCondition(): void
    {
        $connect = $this->createDatabaseConnect();

        static::assertSame(
            1,
            $connect
                ->table('test_version')
                ->insert([
                    'name' => 'xiaoniuge',
                ])
        );

        $testVersion = DemoVersion::select()->findEntity(1);

        $this->assertInstanceof(DemoVersion::class, $testVersion);
        static::assertSame(1, $testVersion->id);
        static::assertSame('xiaoniuge', $testVersion->name);
        static::assertSame('0.0000', $testVersion->availableNumber);
        static::assertSame('0.0000', $testVersion->realNumber);

        $testVersion->name = 'aniu';
        $testVersion->availableNumber = Condition::raw('[available_number]+1');
        $testVersion->realNumber = Condition::raw('[real_number]+3');
        static::assertSame(1, $testVersion->version(true)->update()->flush());
        static::assertSame('SQL: [361] UPDATE `test_version` SET `test_version`.`name` = :named_param_name,`test_version`.`available_number` = `test_version`.`available_number`+1,`test_version`.`real_number` = `test_version`.`real_number`+3,`test_version`.`version` = `test_version`.`version`+1 WHERE `test_version`.`id` = :test_version_id AND `test_version`.`version` = :test_version_version LIMIT 1 | Params:  3 | Key: Name: [17] :named_param_name | paramno=0 | name=[17] ":named_param_name" | is_param=1 | param_type=2 | Key: Name: [16] :test_version_id | paramno=1 | name=[16] ":test_version_id" | is_param=1 | param_type=1 | Key: Name: [21] :test_version_version | paramno=2 | name=[21] ":test_version_version" | is_param=1 | param_type=1 (UPDATE `test_version` SET `test_version`.`name` = \'aniu\',`test_version`.`available_number` = `test_version`.`available_number`+1,`test_version`.`real_number` = `test_version`.`real_number`+3,`test_version`.`version` = `test_version`.`version`+1 WHERE `test_version`.`id` = 1 AND `test_version`.`version` = 0 LIMIT 1)', $testVersion->select()->getLastSql());

        $testVersion->refresh();
        $condition = ['available_number' => $testVersion->availableNumber, DemoVersion::VERSION => 9999];
        $testVersion->name = 'hello';
        $testVersion->availableNumber = Condition::raw('[available_number]+8');
        static::assertSame(0, $testVersion->condition($condition)->update()->flush());
        static::assertSame('SQL: [370] UPDATE `test_version` SET `test_version`.`version` = `test_version`.`version`+1,`test_version`.`name` = :named_param_name,`test_version`.`available_number` = `test_version`.`available_number`+8 WHERE `test_version`.`available_number` = :test_version_available_number AND `test_version`.`version` = :test_version_version AND `test_version`.`id` = :test_version_id LIMIT 1 | Params:  4 | Key: Name: [17] :named_param_name | paramno=0 | name=[17] ":named_param_name" | is_param=1 | param_type=2 | Key: Name: [30] :test_version_available_number | paramno=1 | name=[30] ":test_version_available_number" | is_param=1 | param_type=2 | Key: Name: [21] :test_version_version | paramno=2 | name=[21] ":test_version_version" | is_param=1 | param_type=1 | Key: Name: [16] :test_version_id | paramno=3 | name=[16] ":test_version_id" | is_param=1 | param_type=1 (UPDATE `test_version` SET `test_version`.`version` = `test_version`.`version`+1,`test_version`.`name` = \'hello\',`test_version`.`available_number` = `test_version`.`available_number`+8 WHERE `test_version`.`available_number` = \'1.0000\' AND `test_version`.`version` = 9999 AND `test_version`.`id` = 1 LIMIT 1)', $testVersion->select()->getLastSql());
    }

    #[Api([
        'zh-CN:title' => '实体设置虚拟主键可以解决没有主键的表数据更新问题',
        'zh-CN:description' => <<<'EOT'
**fixture 定义**

**without_primarykey**

``` sql
CREATE TABLE `without_primarykey` (
    `goods_id` bigint(20) NOT NULL DEFAULT '0' COMMENT '商品 ID',
    `description` varchar(255) NOT NULL DEFAULT '' COMMENT '商品描述',
    `name` varchar(100) NOT NULL DEFAULT '' COMMENT '商品名称'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='没有主键的表';
```

**Tests\Database\Ddd\Entity\WithoutPrimarykey**

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Database\Ddd\Entity\WithoutPrimarykey::class)]}
```
EOT,
    ])]
    public function testUpdateWithoutPrimarykey(): void
    {
        $connect = $this->createDatabaseConnect();

        static::assertSame(
            1,
            $connect
                ->table('without_primarykey')
                ->insert([
                    'goods_id' => 1,
                    'description' => 'hello',
                ])
        );

        $withoutPrimarykey = WithoutPrimarykey::select()->findEntity(1);
        static::assertSame(['goods_id'], WithoutPrimarykey::primaryKey());

        $this->assertInstanceof(WithoutPrimarykey::class, $withoutPrimarykey);
        static::assertSame(1, $withoutPrimarykey->goodsId);
        static::assertSame('hello', $withoutPrimarykey->description);

        $withoutPrimarykey->description = 'world';
        static::assertSame(1, $withoutPrimarykey->update()->flush());
        static::assertSame('SQL: [170] UPDATE `without_primarykey` SET `without_primarykey`.`description` = :named_param_description WHERE `without_primarykey`.`goods_id` = :without_primarykey_goods_id LIMIT 1 | Params:  2 | Key: Name: [24] :named_param_description | paramno=0 | name=[24] ":named_param_description" | is_param=1 | param_type=2 | Key: Name: [28] :without_primarykey_goods_id | paramno=1 | name=[28] ":without_primarykey_goods_id" | is_param=1 | param_type=1 (UPDATE `without_primarykey` SET `without_primarykey`.`description` = \'world\' WHERE `without_primarykey`.`goods_id` = 1 LIMIT 1)', $withoutPrimarykey->select()->getLastSql());
    }

    #[Api([
        'zh-CN:title' => '实体未设置主键所有非关联字段将变为虚拟主键',
        'zh-CN:description' => <<<'EOT'
**fixture 定义**

**without_primarykey**

``` sql
CREATE TABLE `without_primarykey` (
    `goods_id` bigint(20) NOT NULL DEFAULT '0' COMMENT '商品 ID',
    `description` varchar(255) NOT NULL DEFAULT '' COMMENT '商品描述',
    `name` varchar(100) NOT NULL DEFAULT '' COMMENT '商品名称'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='没有主键的表';
```

**Tests\Database\Ddd\Entity\WithoutPrimarykeyAndAllAreKey**

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Database\Ddd\Entity\WithoutPrimarykeyAndAllAreKey::class)]}
```
EOT,
    ])]
    public function testUpdateWithoutPrimarykeyAndAllAreKey(): void
    {
        $connect = $this->createDatabaseConnect();

        static::assertSame(
            1,
            $connect
                ->table('without_primarykey')
                ->insert([
                    'goods_id' => 1,
                    'description' => 'hello',
                    'name' => 'world',
                ])
        );

        $withoutPrimarykey = WithoutPrimarykeyAndAllAreKey::select()->findOne();
        static::assertSame(['goods_id', 'description', 'name'], WithoutPrimarykeyAndAllAreKey::primaryKey());

        $this->assertInstanceof(WithoutPrimarykeyAndAllAreKey::class, $withoutPrimarykey);
        static::assertSame(1, $withoutPrimarykey->goodsId);
        static::assertSame('hello', $withoutPrimarykey->description);
        static::assertSame('world', $withoutPrimarykey->name);

        $withoutPrimarykey->description = 'my';
        $withoutPrimarykey->name = 'php';
        static::assertSame(1, $withoutPrimarykey->update()->flush());
        static::assertSame('SQL: [350] UPDATE `without_primarykey` SET `without_primarykey`.`description` = :named_param_description,`without_primarykey`.`name` = :named_param_name WHERE `without_primarykey`.`goods_id` = :without_primarykey_goods_id AND `without_primarykey`.`description` = :without_primarykey_description AND `without_primarykey`.`name` = :without_primarykey_name LIMIT 1 | Params:  5 | Key: Name: [24] :named_param_description | paramno=0 | name=[24] ":named_param_description" | is_param=1 | param_type=2 | Key: Name: [17] :named_param_name | paramno=1 | name=[17] ":named_param_name" | is_param=1 | param_type=2 | Key: Name: [28] :without_primarykey_goods_id | paramno=2 | name=[28] ":without_primarykey_goods_id" | is_param=1 | param_type=1 | Key: Name: [31] :without_primarykey_description | paramno=3 | name=[31] ":without_primarykey_description" | is_param=1 | param_type=2 | Key: Name: [24] :without_primarykey_name | paramno=4 | name=[24] ":without_primarykey_name" | is_param=1 | param_type=2 (UPDATE `without_primarykey` SET `without_primarykey`.`description` = \'my\',`without_primarykey`.`name` = \'php\' WHERE `without_primarykey`.`goods_id` = 1 AND `without_primarykey`.`description` = \'hello\' AND `without_primarykey`.`name` = \'world\' LIMIT 1)', $withoutPrimarykey->select()->getLastSql());

        $withoutPrimarykey->name = 'new name';
        static::assertSame(1, $withoutPrimarykey->update()->flush());
        static::assertSame('SQL: [288] UPDATE `without_primarykey` SET `without_primarykey`.`name` = :named_param_name WHERE `without_primarykey`.`goods_id` = :without_primarykey_goods_id AND `without_primarykey`.`description` = :without_primarykey_description AND `without_primarykey`.`name` = :without_primarykey_name LIMIT 1 | Params:  4 | Key: Name: [17] :named_param_name | paramno=0 | name=[17] ":named_param_name" | is_param=1 | param_type=2 | Key: Name: [28] :without_primarykey_goods_id | paramno=1 | name=[28] ":without_primarykey_goods_id" | is_param=1 | param_type=1 | Key: Name: [31] :without_primarykey_description | paramno=2 | name=[31] ":without_primarykey_description" | is_param=1 | param_type=2 | Key: Name: [24] :without_primarykey_name | paramno=3 | name=[24] ":without_primarykey_name" | is_param=1 | param_type=2 (UPDATE `without_primarykey` SET `without_primarykey`.`name` = \'new name\' WHERE `without_primarykey`.`goods_id` = 1 AND `without_primarykey`.`description` = \'my\' AND `without_primarykey`.`name` = \'php\' LIMIT 1)', $withoutPrimarykey->select()->getLastSql());

        $withoutPrimarykey->name = 'new and new';
        $withoutPrimarykey->update();
        $withoutPrimarykey->name = 'new and new2';
        static::assertSame(1, $withoutPrimarykey->update()->flush());
        static::assertSame('SQL: [288] UPDATE `without_primarykey` SET `without_primarykey`.`name` = :named_param_name WHERE `without_primarykey`.`goods_id` = :without_primarykey_goods_id AND `without_primarykey`.`description` = :without_primarykey_description AND `without_primarykey`.`name` = :without_primarykey_name LIMIT 1 | Params:  4 | Key: Name: [17] :named_param_name | paramno=0 | name=[17] ":named_param_name" | is_param=1 | param_type=2 | Key: Name: [28] :without_primarykey_goods_id | paramno=1 | name=[28] ":without_primarykey_goods_id" | is_param=1 | param_type=1 | Key: Name: [31] :without_primarykey_description | paramno=2 | name=[31] ":without_primarykey_description" | is_param=1 | param_type=2 | Key: Name: [24] :without_primarykey_name | paramno=3 | name=[24] ":without_primarykey_name" | is_param=1 | param_type=2 (UPDATE `without_primarykey` SET `without_primarykey`.`name` = \'new and new2\' WHERE `without_primarykey`.`goods_id` = 1 AND `without_primarykey`.`description` = \'my\' AND `without_primarykey`.`name` = \'new name\' LIMIT 1)', $withoutPrimarykey->select()->getLastSql());
    }

    #[Api([
        'zh-CN:title' => '__clone 实体克隆',
        'zh-CN:description' => <<<'EOT'
复制的实体没有主键值，保存数据时将会在数据库新增一条记录。
EOT,
    ])]
    public function testEntityClone(): void
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

        $post = Post::find()->where('id', 1)->findOne();
        static::assertSame(1, $post->id);
        static::assertSame('hello world', $post->title);
        static::assertSame(1, $post->userId);
        static::assertSame('post summary', $post->summary);

        $postClone = clone $post;
        static::assertNull($postClone->id);
        static::assertSame('hello world', $postClone->title);
        static::assertSame(1, $postClone->userId);
        static::assertSame('post summary', $postClone->summary);

        $post->title = 'world';
        static::assertSame('hello world', $postClone->title);
        $postClone->title = 'goods';
        static::assertSame('world', $post->title);
    }

    #[Api([
        'zh-CN:title' => 'make 创建实例',
    ])]
    public function testEntityMake(): void
    {
        $post = Post::make([
            'title' => 'hello world',
            'user_id' => 1,
            'summary' => 'post summary',
            'delete_at' => 0,
        ]);

        static::assertTrue($post->newed());
        static::assertNull($post->id);
        static::assertSame('hello world', $post->title);
        static::assertSame(1, $post->userId);
        static::assertSame('post summary', $post->summary);
    }

    public function testEntityMakeNotNewed(): void
    {
        $post = Post::make([
            'id' => 1,
            'title' => 'hello world',
            'user_id' => 1,
            'summary' => 'post summary',
            'delete_at' => 0,
        ], true);

        static::assertFalse($post->newed());
        static::assertSame(1, $post->id);
        static::assertSame('hello world', $post->title);
        static::assertSame(1, $post->userId);
        static::assertSame('post summary', $post->summary);
    }

    #[Api([
        'zh-CN:title' => 'createAssign 新增批量赋值',
    ])]
    public function testEntityCreateAssign(): void
    {
        $post = Post::createAssign([
            'title' => 'hello world',
            'user_id' => 1,
            'summary' => 'post summary',
            'delete_at' => 0,
        ]);

        static::assertTrue($post->newed());
        static::assertNull($post->id);
        static::assertSame('hello world', $post->title);
        static::assertSame(1, $post->userId);
        static::assertSame('post summary', $post->summary);
    }

    #[Api([
        'zh-CN:title' => 'updateAssign 更新批量赋值',
    ])]
    public function testEntityUpdateAssign(): void
    {
        $post = Post::updateAssign([
            'id' => 1,
            'title' => 'hello world',
            'user_id' => 1,
            'summary' => 'post summary',
            'delete_at' => 0,
        ]);

        static::assertFalse($post->newed());
        static::assertSame(1, $post->id);
        static::assertSame('hello world', $post->title);
        static::assertSame(1, $post->userId);
        static::assertSame('post summary', $post->summary);
    }

    public function testEntityWithEnumButClassNotFound(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage(
            'Enum `NotFound` is not exists.'
        );

        new EntityWithEnumButClassNotFound();
    }

    #[Api([
        'zh-CN:title' => 'addGlobalScope 添加全局作用域',
    ])]
    public function testAddGlobalScope(): void
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

        Post::addGlobalScope('hello', function (Select $select): void {
            $select->where('id', 5);
        });
        Post::select()->findAll();
        $sql = Post::select()->getLastSql();
        static::assertSame($sql, 'SQL: [97] SELECT `post`.* FROM `post` WHERE `post`.`delete_at` = :post_delete_at AND `post`.`id` = :post_id | Params:  2 | Key: Name: [15] :post_delete_at | paramno=0 | name=[15] ":post_delete_at" | is_param=1 | param_type=1 | Key: Name: [8] :post_id | paramno=1 | name=[8] ":post_id" | is_param=1 | param_type=1 (SELECT `post`.* FROM `post` WHERE `post`.`delete_at` = 0 AND `post`.`id` = 5)');
    }

    #[Api([
        'zh-CN:title' => 'withoutGlobalScope 不带指定全局作用域查询',
    ])]
    public function testWithoutGlobalScope(): void
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

        Post::addGlobalScope('hello', function (Select $select): void {
            $select->where('id', 5);
        });
        Post::withoutGlobalScope(['hello'])->findAll();
        $sql = Post::select()->getLastSql();
        static::assertSame($sql, 'SQL: [70] SELECT `post`.* FROM `post` WHERE `post`.`delete_at` = :post_delete_at | Params:  1 | Key: Name: [15] :post_delete_at | paramno=0 | name=[15] ":post_delete_at" | is_param=1 | param_type=1 (SELECT `post`.* FROM `post` WHERE `post`.`delete_at` = 0)');
    }

    #[Api([
        'zh-CN:title' => 'findEntity 通过主键或条件查找实体',
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

        $post = Post::findEntity(1);
        static::assertSame(1, $post->id);
        static::assertSame('hello world', $post->title);
        static::assertSame('post summary', $post->summary);
    }

    #[Api([
        'zh-CN:title' => 'findMany 通过主键或条件查找多个实体',
    ])]
    public function testFindMany(): void
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

        $collection = Post::findMany([1]);
        static::assertInstanceOf(\Leevel\Database\Ddd\EntityCollection::class, $collection);
        $post = $collection[0];
        static::assertSame(1, $post->id);
        static::assertSame('hello world', $post->title);
        static::assertSame('post summary', $post->summary);
    }

    #[Api([
        'zh-CN:title' => 'findOrFail 通过主键或条件查找实体，未找到则抛出异常',
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

        $post = Post::findOrFail(1);
        static::assertSame(1, $post->id);
        static::assertSame('hello world', $post->title);
        static::assertSame('post summary', $post->summary);
    }

    #[Api([
        'zh-CN:title' => 'meta 返回实体类的元对象(虚拟实体)',
    ])]
    public function testMeta1(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('The virtual entity does not support select');

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

        DemoVirtualEntity::meta();
    }

    #[Api([
        'zh-CN:title' => 'delete 删除实体(虚拟实体)',
    ])]
    public function testVirtualDelete(): void
    {
        $demo = new DemoVirtualEntity(['id' => 1]);
        $demo->delete();
        static::assertSame(0, $demo->flush());
    }

    protected function initI18n(): void
    {
        $container = Container::singletons();
        $container->clear();

        $container->singleton('i18n', function (): \I18nMock {
            return new \I18nMock();
        });
    }

    protected function getDatabaseTable(): array
    {
        return ['post', 'composite_id', 'test_version', 'without_primarykey'];
    }
}
