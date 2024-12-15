<?php

declare(strict_types=1);

namespace Tests\Database\Ddd;

use Leevel\Database\Condition;
use Leevel\Database\Ddd\EntityCollection;
use Leevel\Database\Ddd\EntityIdentifyConditionException;
use Leevel\Database\Ddd\Meta;
use Leevel\Database\DuplicateKeyException;
use Leevel\Database\Select;
use Leevel\Di\Container;
use Leevel\Kernel\Utils\Api;
use Leevel\Validate\Validator;
use Tests\Database\DatabaseTestCase as TestCase;
use Tests\Database\Ddd\Entity\CompositeId;
use Tests\Database\Ddd\Entity\DemoToArrayShowPropNullRelationEntity;
use Tests\Database\Ddd\Entity\DemoUnique;
use Tests\Database\Ddd\Entity\DemoUniqueNew;
use Tests\Database\Ddd\Entity\DemoVersion;
use Tests\Database\Ddd\Entity\DemoVirtualEntity;
use Tests\Database\Ddd\Entity\EntityWithEnum;
use Tests\Database\Ddd\Entity\EntityWithEnum2;
use Tests\Database\Ddd\Entity\EntityWithEnumButClassNotFound;
use Tests\Database\Ddd\Entity\EntityWithEnumValidator;
use Tests\Database\Ddd\Entity\EntityWithEnumValidator2;
use Tests\Database\Ddd\Entity\EntityWithEnumValidator3;
use Tests\Database\Ddd\Entity\EntityWithoutAnyField;
use Tests\Database\Ddd\Entity\EntityWithoutPrimaryKey;
use Tests\Database\Ddd\Entity\EntityWithoutPrimaryKeyNullInArray;
use Tests\Database\Ddd\Entity\PostNew;
use Tests\Database\Ddd\Entity\PostNew2;
use Tests\Database\Ddd\Entity\PostNew3;
use Tests\Database\Ddd\Entity\PostNew4;
use Tests\Database\Ddd\Entity\PostNew5;
use Tests\Database\Ddd\Entity\PostNew6;
use Tests\Database\Ddd\Entity\PostNew7;
use Tests\Database\Ddd\Entity\PostNew8;
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
    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();

        $container = Container::singletons();
        $container->clear();
        $container->singleton('i18n', static function (): \I18nMock {
            return new \I18nMock();
        });
    }

    #[\Override]
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

        self::assertSame('5', $entity->title);
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
        // 修复单元测试受到其它测试用例的影响,这个数据库连接会被其他地方代码给修改到
        $this->setTestProperty($instance, 'databaseConnect', null);
        $instance->select();
    }

    #[Api([
        'zh-CN:title' => 'setProps 批量设置属性数据',
    ])]
    public function testWithProps(): void
    {
        $entity = new Post();
        $entity->setProps([
            'title' => 'foo',
            'summary' => 'bar',
        ]);

        self::assertSame('foo', $entity->title);
        self::assertSame('bar', $entity->summary);
        self::assertSame(['title', 'summary'], $entity->getChanged());
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

        self::assertSame('foo', $entity->title);
        self::assertSame(1, $entity->status);

        $data = <<<'eot'
            {
                "title": "foo"
            }
            eot;

        self::assertSame(
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

        self::assertSame(
            $data,
            $this->varJson(
                $entity->toArray(),
                2
            )
        );

        self::assertSame('启用', StatusEnum::description('1'));
        self::assertSame('禁用', StatusEnum::description('0'));

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

        self::assertSame(
            $data,
            $this->varJson(
                StatusEnum::descriptions(),
                3
            )
        );
    }

    #[Api([
        'zh-CN:title' => '多枚举值',
        'zh-CN:description' => <<<'EOT'
**fixture 定义**

**Tests\Database\Ddd\Entity\EntityWithEnum2**

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Database\Ddd\Entity\EntityWithEnum2::class)]}
```
EOT,
    ])]
    public function testEntityWithEnumDescription2(): void
    {
        $this->initI18n();

        $entity = new EntityWithEnum2([
            'title' => 'foo',
            'status' => '0,1',
        ]);

        $data = <<<'eot'
            {
                "title": "foo",
                "status": "0,1",
                "status_enum": "禁用,启用"
            }
            eot;

        self::assertSame(
            $data,
            $this->varJson(
                $entity->toArray(),
                2
            )
        );
    }

    #[Api([
        'zh-CN:title' => '虚拟属性保存丢弃',
        'zh-CN:description' => <<<'EOT'
**fixture 定义**

**Tests\Database\Ddd\Entity\PostNew7**

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Database\Ddd\Entity\PostNew7::class)]}
```
EOT,
    ])]
    public function testVirtualColumnData(): void
    {
        $entity = new PostNew7([
            'title' => 'foo',
            'content' => 'content',
        ]);
        $result = $entity->save()->flush();
        self::assertSame(1, $result);
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

        [$validatorRules, $validatorMessages] = EntityWithEnum::getColumnValidators('store');
        $validator = new Validator($entity->toArray(), $validatorRules, $validatorMessages);
        $result = $validator->fail();
        self::assertTrue($result);

        $data = <<<'eot'
{
    "status": [
        "status 必须在 0,1 范围内"
    ]
}
eot;

        self::assertSame(
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
        [$validatorRules, $validatorMessages] = EntityWithEnum::getColumnValidators('store');

        $validator = new Validator($entity->toArray(), $validatorRules, $validatorMessages);
        $result = $validator->fail();
        self::assertTrue($result);

        $data = <<<'eot'
{
    "status": [
        "status 必须在 0,1 范围内"
    ]
}
eot;

        self::assertSame(
            $data,
            $this->varJson(
                $validator->error()
            )
        );
    }

    #[Api([
        'zh-CN:title' => 'getColumnValidators 返回字段验证规则(默认场景)',
        'zh-CN:description' => <<<'EOT'
**fixture 定义**

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Database\Ddd\Entity\EntityWithEnumValidator::class)]}
```
EOT,
    ])]
    public function testColumnValidators(): void
    {
        [$validatorRules, $validatorMessages] = EntityWithEnumValidator::getColumnValidators(EntityWithEnumValidator::VALIDATOR_SCENES);

        $data = <<<'eot'
{
    "title": [
        "required",
        "max_length:30"
    ],
    "status": [
        [
            "in",
            [
                0,
                1
            ]
        ],
        "optional"
    ]
}
eot;

        self::assertSame(
            $data,
            $this->varJson(
                $validatorRules
            )
        );

        $data = <<<'eot'
[]
eot;

        self::assertSame(
            $data,
            $this->varJson(
                $validatorMessages
            )
        );

        $data = <<<'eot'
[]
eot;

        self::assertSame(
            $data,
            $this->varJson(
                $validatorMessages
            )
        );
    }

    #[Api([
        'zh-CN:title' => 'getColumnValidators 返回字段验证规则(继承场景)',
        'zh-CN:description' => <<<'EOT'
**fixture 定义**

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Database\Ddd\Entity\EntityWithEnumValidator::class)]}
```

继承场景用法:

``` php
 'store' => null
```
EOT,
    ])]
    public function testColumnValidators2(): void
    {
        [$validatorRules, $validatorMessages] = EntityWithEnumValidator::getColumnValidators('store');

        $data = <<<'eot'
{
    "title": [
        "required",
        "max_length:30"
    ],
    "status": [
        [
            "in",
            [
                0,
                1
            ]
        ],
        "optional"
    ]
}
eot;

        self::assertSame(
            $data,
            $this->varJson(
                $validatorRules
            )
        );

        $data = <<<'eot'
[]
eot;

        self::assertSame(
            $data,
            $this->varJson(
                $validatorMessages
            )
        );
    }

    #[Api([
        'zh-CN:title' => 'getColumnValidators 返回字段验证规则(合并场景)',
        'zh-CN:description' => <<<'EOT'
**fixture 定义**

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Database\Ddd\Entity\EntityWithEnumValidator::class)]}
```

继承场景用法:

``` php
':update' => [
    \Leevel\Validate\IValidator::OPTIONAL,
],
```
EOT,
    ])]
    public function testColumnValidators3(): void
    {
        [$validatorRules, $validatorMessages] = EntityWithEnumValidator::getColumnValidators('update');

        $data = <<<'eot'
{
    "title": [
        "required",
        "max_length:30",
        "optional"
    ],
    "status": [
        [
            "in",
            [
                0,
                1
            ]
        ],
        "optional"
    ]
}
eot;

        self::assertSame(
            $data,
            $this->varJson(
                $validatorRules
            )
        );

        $data = <<<'eot'
[]
eot;

        self::assertSame(
            $data,
            $this->varJson(
                $validatorMessages
            )
        );
    }

    #[Api([
        'zh-CN:title' => 'getColumnValidators 返回字段验证规则(替换场景)',
        'zh-CN:description' => <<<'EOT'
**fixture 定义**

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Database\Ddd\Entity\EntityWithEnumValidator::class)]}
```

继承场景用法:

``` php
'update_new' => [
    'required',
    'max_length:10',
],
```
EOT,
    ])]
    public function testColumnValidators4(): void
    {
        [$validatorRules, $validatorMessages] = EntityWithEnumValidator::getColumnValidators('update_new');

        $data = <<<'eot'
{
    "title": [
        "required",
        "max_length:10"
    ],
    "status": [
        [
            "in",
            [
                0,
                1
            ]
        ],
        "optional"
    ]
}
eot;

        self::assertSame(
            $data,
            $this->varJson(
                $validatorRules
            )
        );

        $data = <<<'eot'
[]
eot;

        self::assertSame(
            $data,
            $this->varJson(
                $validatorMessages
            )
        );
    }

    #[Api([
        'zh-CN:title' => 'getColumnValidators 返回字段验证规则(指定字段)',
        'zh-CN:description' => <<<'EOT'
**fixture 定义**

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Database\Ddd\Entity\EntityWithEnumValidator::class)]}
```
EOT,
    ])]
    public function testColumnValidators5(): void
    {
        [$validatorRules, $validatorMessages] = EntityWithEnumValidator::getColumnValidators('update_new', ['title']);

        $data = <<<'eot'
{
    "title": [
        "required",
        "max_length:10"
    ]
}
eot;

        self::assertSame(
            $data,
            $this->varJson(
                $validatorRules
            )
        );

        $data = <<<'eot'
[]
eot;

        self::assertSame(
            $data,
            $this->varJson(
                $validatorMessages
            )
        );
    }

    #[Api([
        'zh-CN:title' => 'getColumnValidators 返回字段验证规则(支持自定义消息)',
        'zh-CN:description' => <<<'EOT'
**fixture 定义**

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Database\Ddd\Entity\EntityWithEnumValidator2::class)]}
```
EOT,
    ])]
    public function testColumnValidators6(): void
    {
        [$validatorRules, $validatorMessages] = EntityWithEnumValidator2::getColumnValidators('update_new', ['title']);

        $data = <<<'eot'
{
    "title": [
        "required",
        "max_length:10"
    ]
}
eot;

        self::assertSame(
            $data,
            $this->varJson(
                $validatorRules
            )
        );

        $data = <<<'eot'
{
    "title": {
        "required": "{field} 不能为空 new",
        "max_length": "{field} 不满足最大长度 {rule} new"
    }
}
eot;

        self::assertSame(
            $data,
            $this->varJson(
                $validatorMessages
            )
        );
    }

    public function testColumnValidators7(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Enum `EnumClassNotFound` is not exists.');

        EntityWithEnumValidator3::getColumnValidators('update_new', ['title']);
    }

    #[Api([
        'zh-CN:title' => 'hasChanged 检测属性是否已经改变',
    ])]
    public function testHasChanged(): void
    {
        $entity = new Post();
        self::assertFalse($entity->hasChanged('title'));
        $entity->title = 'change';
        self::assertTrue($entity->hasChanged('title'));
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

        self::assertSame(
            $data,
            $this->varJson(
                $entity->getChanged()
            )
        );

        $entity->addChanged(['user_id', 'title']);

        $data = <<<'eot'
            [
                "user_id",
                "title"
            ]
            eot;

        self::assertSame(
            $data,
            $this->varJson(
                $entity->getChanged(),
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

        self::assertSame(
            $data,
            $this->varJson(
                $entity->getChanged()
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

        self::assertSame(
            $data,
            $this->varJson(
                $entity->getChanged(),
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

        self::assertSame(
            $data,
            $this->varJson(
                $entity->getChanged()
            )
        );

        $entity->addChanged(['user_id', 'title']);

        $data = <<<'eot'
            [
                "user_id",
                "title"
            ]
            eot;

        self::assertSame(
            $data,
            $this->varJson(
                $entity->getChanged(),
                1,
            )
        );

        $entity->deleteChanged(['user_id']);

        $data = <<<'eot'
            [
                "title"
            ]
            eot;

        self::assertSame(
            $data,
            $this->varJson(
                $entity->getChanged(),
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

        self::assertSame(
            $data,
            $this->varJson(
                $entity->getChanged()
            )
        );

        $entity->addChanged(['user_id', 'title']);

        $data = <<<'eot'
            [
                "user_id",
                "title"
            ]
            eot;

        self::assertSame(
            $data,
            $this->varJson(
                $entity->getChanged(),
                1,
            )
        );

        $entity->clearChanged(['user_id']);

        $data = <<<'eot'
            []
            eot;

        self::assertSame(
            $data,
            $this->varJson(
                $entity->getChanged(),
                2,
            )
        );
    }

    public function testPrimaryKeyHasNullInArrayIsEmpty(): void
    {
        $entity = new EntityWithoutPrimaryKeyNullInArray();
        self::assertSame(['name'], $entity->getPrimaryKey());
    }

    public function testSinglePrimaryKeyNotFound(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Entity Tests\\Database\\Ddd\\Entity\\EntityWithoutAnyField has no primary key.'
        );

        $entity = new EntityWithoutAnyField();
        self::assertNull($entity->getPrimaryKey());
        $entity->getSinglePrimaryKey();
    }

    public function testSinglePrimaryKeyNotFoundAndAllIsKeyAndFoundOne(): void
    {
        $entity = new EntityWithoutPrimaryKey();
        self::assertSame(['name'], $entity->getPrimaryKey());
        self::assertSame('name', $entity->getSinglePrimaryKey());
    }

    public function testSinglePrimaryKeyNotFoundAndFoundMoreThanOne(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Entity Tests\\Database\\Ddd\\Entity\\WithoutPrimarykeyAndAllAreKey does not support composite primary keys.'
        );

        $entity = new WithoutPrimarykeyAndAllAreKey();
        self::assertSame(['goods_id', 'description', 'name'], $entity->getPrimaryKey());
        $entity->getSinglePrimaryKey();
    }

    public function testSinglePrimaryKeyNotSupportComposite(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Entity Tests\\Database\\Ddd\\Entity\\CompositeId does not support composite primary keys.'
        );

        $entity = new CompositeId();
        $entity->getSinglePrimaryKey();
    }

    #[Api([
        'zh-CN:title' => 'getIdCondition 获取查询主键条件',
    ])]
    public function testIdCondition(): void
    {
        $entity = new Post(['id' => 5]);
        self::assertSame(['id' => 5], $entity->getIdCondition());
    }

    public function testIdConditionHasNoPrimaryKeyData(): void
    {
        $this->expectException(EntityIdentifyConditionException::class);
        $this->expectExceptionMessage(
            'Entity Tests\\Database\\Ddd\\Entity\\Relation\\Post has no identify condition data.'
        );

        $entity = new Post();
        self::assertNull($entity->getIdCondition());
    }

    #[Api([
        'zh-CN:title' => '实体属性访问魔术方法 __set 支持',
    ])]
    public function testMagicSet(): void
    {
        $entity = new Post(['id' => 5]);
        self::assertFalse(isset($entity->title));
        self::assertNull($entity->title);
        $entity->title = 'world';
        self::assertTrue(isset($entity->title));
        self::assertSame('world', $entity->title);
    }

    #[Api([
        'zh-CN:title' => '实体属性访问魔术方法 __get 支持',
    ])]
    public function testMagicGet(): void
    {
        $entity = new Post(['id' => 5]);
        self::assertNull($entity->title);
        $entity->title = 'world';
        self::assertSame('world', $entity->title);
    }

    #[Api([
        'zh-CN:title' => '实体属性访问魔术方法 __unset 支持',
    ])]
    public function testMagicUnset(): void
    {
        $entity = new Post(['id' => 5]);
        self::assertNull($entity->title);
        $entity->title = 'world';
        self::assertSame('world', $entity->title);
        $entity->title = null;
        self::assertNull($entity->title);
    }

    public function testCallTryLoadRelation(): void
    {
        $this->expectException(\BadMethodCallException::class);
        $this->expectExceptionMessage(
            'Method `user` is not exits,maybe you can try `Tests\\Database\\Ddd\\Entity\\Relation\\Post::make()->getRelation(\'user\')`.'
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

        self::assertSame(
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
        self::assertSame('hello world', $post->title);
        self::assertSame(1, $post->userId);
        self::assertSame('post summary', $post->summary);
    }

    #[Api([
        'zh-CN:title' => 'connectSandbox 数据库连接沙盒',
    ])]
    public function testConnectSandbox(): void
    {
        $connect = $this->createDatabaseConnect();

        self::assertSame(
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

        $post = Post::connectSandbox('password_right', static function () {
            return Post::find()->where('id', 1)->findOne();
        });

        self::assertSame('hello world', $post->title);
        self::assertSame(1, $post->userId);
        self::assertSame('post summary', $post->summary);
    }

    public function testConnectSandboxAndPasswordIsError(): void
    {
        // 因为消息 IP 有变，所有这里不测试异常消息
        // SQLSTATE[HY000] [1045] Access denied for user 'root'@'10.0.2.2' (using password: YES)
        $this->expectException(\PDOException::class);

        $connect = $this->createDatabaseConnect();

        self::assertSame(
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

        Post::connectSandbox('password_not_right', static function () {
            return Post::find()->where('id', 1)->findOne();
        });
    }

    #[Api([
        'zh-CN:title' => 'getNewed 确定对象是否对应数据库中的一条记录',
    ])]
    public function testGetNewed(): void
    {
        $entity = new Post();
        self::assertTrue($entity->getNewed());

        $entity = new Post(['id' => 5]);
        self::assertTrue($entity->getNewed());

        $entity = new Post(['id' => 5], true);
        self::assertFalse($entity->getNewed());
    }

    #[Api([
        'zh-CN:title' => 'setNewed 设置确定对象是否对应数据库中的一条记录',
    ])]
    public function testSethNewed(): void
    {
        $entity = new Post();
        self::assertTrue($entity->getNewed());
        $entity->setNewed(false);
        self::assertFalse($entity->getNewed());

        $entity = new Post(['id' => 5]);
        self::assertTrue($entity->getNewed());
        $entity->setNewed(false);
        self::assertFalse($entity->getNewed());

        $entity = new Post(['id' => 5], true);
        self::assertFalse($entity->getNewed());
        $entity->setNewed(true);
        self::assertTrue($entity->getNewed());
    }

    #[Api([
        'zh-CN:title' => 'getOriginal 获取原始数据',
    ])]
    public function testGetOriginal(): void
    {
        $entity = new Post();
        self::assertSame([], $entity->getOriginal());

        $entity = new Post($data = [
            'title' => 'hello',
            'summary' => 'world',
            'foo' => 'bar',
        ], false, true);
        self::assertSame($data, $entity->getOriginal());
        self::assertSame('hello', $entity->title);
        self::assertSame('world', $entity->summary);
    }

    #[Api([
        'zh-CN:title' => 'getId 获取主键值',
    ])]
    public function testId(): void
    {
        $entity = new Post();
        self::assertFalse($entity->getId());

        $entity = new Post(['id' => 5]);
        self::assertSame(['id' => 5], $entity->getId());
    }

    #[Api([
        'zh-CN:title' => 'getId 获取复合主键值',
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
        self::assertFalse($entity->getId(false));
        self::assertFalse($entity->getId());

        $entity = new CompositeId(['id1' => 5]);
        self::assertFalse($entity->getId(false));
        self::assertFalse($entity->getId());

        $entity = new CompositeId(['id1' => 5, 'id2' => 8]);
        self::assertSame(['id1' => 5, 'id2' => 8], $entity->getId(false));
        self::assertSame(['id1' => 5, 'id2' => 8], $entity->getId());
    }

    #[Api([
        'zh-CN:title' => 'refresh 从数据库重新读取当前对象的属性',
    ])]
    public function testRefresh(): void
    {
        $post1 = new Post();
        $post1->create()->flush();
        $this->assertInstanceof(Post::class, $post1);
        self::assertSame(1, $post1->id);
        self::assertNull($post1->userId);
        self::assertNull($post1->title);
        self::assertNull($post1->summary);
        self::assertNull($post1->deleteAt);

        $post1->refresh();
        self::assertSame(1, $post1->id);
        self::assertSame(0, $post1->userId);
        self::assertSame('', $post1->title);
        self::assertSame('', $post1->summary);
        self::assertSame(0, $post1->deleteAt);
    }

    #[Api([
        'zh-CN:title' => 'refresh 支持指定字段',
    ])]
    public function testRefreshCols(): void
    {
        $post1 = new Post();
        $post1->create()->flush();
        $this->assertInstanceof(Post::class, $post1);
        self::assertSame(1, $post1->id);
        self::assertNull($post1->userId);
        self::assertNull($post1->title);
        self::assertNull($post1->summary);
        self::assertNull($post1->deleteAt);

        $post1->refresh(['id', 'title']);
        self::assertSame(1, $post1->id);
        self::assertNull($post1->userId);
        self::assertSame('', $post1->title);
        self::assertNull($post1->summary);
        self::assertNull($post1->deleteAt);
    }

    #[Api([
        'zh-CN:title' => 'refresh 从数据库重新读取当前对象的属性支持复合主键',
    ])]
    public function testRefreshWithCompositeId(): void
    {
        $entity = new CompositeId(['id1' => 1, 'id2' => 3]);
        $entity->create()->flush();
        $this->assertInstanceof(CompositeId::class, $entity);
        self::assertSame(1, $entity->id1);
        self::assertSame(3, $entity->id2);
        self::assertNull($entity->name);

        $entity->refresh();
        self::assertSame(1, $entity->id1);
        self::assertSame(3, $entity->id2);
        self::assertSame('', $entity->name);
    }

    public function testRefreshButNoPrimaryKeyData(): void
    {
        $this->expectException(EntityIdentifyConditionException::class);
        $this->expectExceptionMessage(
            'Entity Tests\\Database\\Ddd\\Entity\\Relation\\Post has no identify condition data.'
        );

        $entity = new Post();
        $this->assertInstanceof(Post::class, $entity);
        self::assertNull($entity->id);

        $entity->refresh();
    }

    public function testRefreshWithCompositeIdButNoPrimaryKeyData(): void
    {
        $this->expectException(EntityIdentifyConditionException::class);
        $this->expectExceptionMessage(
            'Entity Tests\\Database\\Ddd\\Entity\\CompositeId has no identify condition data.'
        );

        $entity = new CompositeId();
        $entity->create()->flush();
        $this->assertInstanceof(CompositeId::class, $entity);
        self::assertNull($entity->id1);
        self::assertNull($entity->id2);
        self::assertNull($entity->name);

        $entity->refresh();
    }

    public function testRefreshWithoutPrimaryKey(): void
    {
        $this->expectException(EntityIdentifyConditionException::class);
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
        self::assertSame(0, $post1->flush());
    }

    public function testFlushTwiceWithoutDataAndDoNothing(): void
    {
        $post1 = new Post();
        $post1->create();
        self::assertSame(1, $post1->flush());
        self::assertSame(0, $post1->flush());
    }

    public function testReplaceAndThrowException(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage(
            'Update error'
        );

        $connect = $this->createDatabaseConnect();

        self::assertSame(
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
        self::assertSame([], $entity->toArray());
    }

    public function test4(): void
    {
        $entity = new Post(['title' => 'foo']);
        $newEntity = $entity->each(static function (mixed $value, string $field): mixed {
            if ('title' === $field) {
                return 'bar';
            }

            return $value;
        });
        self::assertSame(['title' => 'bar'], $newEntity->toArray());
    }

    public function test5(): void
    {
        $entity = new DemoToArrayShowPropNullRelationEntity(['address' => 'foo', 'hello' => 'new']);
        $newEntity = $entity->each(static function (mixed $value, string $field): mixed {
            if ('address' === $field) {
                return 'bar';
            }

            return $value;
        });
        self::assertSame([
            'address' => 'bar',
            'foo_bar' => null,
            'hello' => 'new',
        ], $newEntity->toArray());
    }

    #[Api([
        'zh-CN:title' => '字段支持格式化',
        'zh-CN:description' => <<<'EOT'
**fixture 定义**

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Database\Ddd\Entity\PostNew2::class)]}
```
EOT,
    ])]
    public function test6(): void
    {
        $entity = new PostNew2(['title' => 'foo']);
        self::assertSame([
            'title' => 'foo',
            'title_formated' => 'foo_new',
        ], $entity->toArray());
    }

    public function test7(): void
    {
        $entity = new PostNew2(['user_id' => 1]);
        self::assertSame([
            'user_id' => 1,
        ], $entity->toArray());
    }

    public function test8(): void
    {
        $entity = new PostNew2(['summary' => 'title']);
        self::assertSame([
            'summary' => 'title',
        ], $entity->toArray());
    }

    #[Api([
        'zh-CN:title' => '字符串支持默认值函数',
        'zh-CN:description' => <<<'EOT'
**fixture 定义**

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Database\Ddd\Entity\PostNew3::class)]}
```
EOT,
    ])]
    public function test9(): void
    {
        $entity = new PostNew3();
        self::assertSame([
            'title' => 'hello world',
        ], $entity->toArray());
    }

    #[Api([
        'zh-CN:title' => '浮点数填充数据',
        'zh-CN:description' => <<<'EOT'
**fixture 定义**

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Database\Ddd\Entity\PostNew4::class)]}
```
EOT,
    ])]
    public function test10(): void
    {
        $entity = new PostNew4([
            'user_id' => '1.3',
        ]);
        self::assertSame([
            'user_id' => 1.3,
        ], $entity->toArray());
    }

    #[Api([
        'zh-CN:title' => '自定义转换优先',
        'zh-CN:description' => <<<'EOT'
**fixture 定义**

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Database\Ddd\Entity\PostNew8::class)]}
```
EOT,
    ])]
    public function test15(): void
    {
        $entity = new PostNew8([
            'user_id' => '1.3',
        ]);
        self::assertSame([
            'user_id' => 6.3,
        ], $entity->toArray());
    }

    public function test11(): void
    {
        $entity = new PostNew5();
        self::assertSame([], $entity->toArray());
    }

    #[Api([
        'zh-CN:title' => '默认格式化',
        'zh-CN:description' => <<<'EOT'
**fixture 定义**

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Database\Ddd\Entity\PostNew6::class)]}
```
EOT,
    ])]
    public function test12(): void
    {
        $entity = new PostNew6([
            'title' => 'hello',
        ]);
        self::assertSame([
            'title' => 'hello',
            'title_formated' => 'hello_new',
        ], $entity->toArray());
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

        self::assertSame(
            1,
            $connect
                ->table('test_version')
                ->insert([
                    'name' => 'xiaoniuge',
                ])
        );

        $testVersion = DemoVersion::select()->findEntity(1);

        $this->assertInstanceof(DemoVersion::class, $testVersion);
        self::assertSame(1, $testVersion->id);
        self::assertSame('xiaoniuge', $testVersion->name);
        self::assertSame('0.0000', $testVersion->availableNumber);
        self::assertSame('0.0000', $testVersion->realNumber);

        $condition = [
            'available_number' => $testVersion->availableNumber,
            'real_number' => $testVersion->realNumber,
        ];
        $testVersion->name = 'aniu';
        $testVersion->availableNumber = Condition::raw('[available_number]+1');
        $testVersion->realNumber = Condition::raw('[real_number]+3');
        self::assertSame(
            1,
            $testVersion
                ->setCondition($condition)
                ->update()
                ->flush()
        );
        self::assertSame('SQL: [493] UPDATE `test_version` SET `test_version`.`name` = :named_param_name,`test_version`.`available_number` = `test_version`.`available_number`+1,`test_version`.`real_number` = `test_version`.`real_number`+3,`test_version`.`version` = `test_version`.`version`+1 WHERE `test_version`.`available_number` = :test_version_available_number AND `test_version`.`real_number` = :test_version_real_number AND `test_version`.`id` = :test_version_id AND `test_version`.`version` = :test_version_version LIMIT 1 | Params:  5 | Key: Name: [17] :named_param_name | paramno=0 | name=[17] ":named_param_name" | is_param=1 | param_type=2 | Key: Name: [30] :test_version_available_number | paramno=1 | name=[30] ":test_version_available_number" | is_param=1 | param_type=2 | Key: Name: [25] :test_version_real_number | paramno=2 | name=[25] ":test_version_real_number" | is_param=1 | param_type=2 | Key: Name: [16] :test_version_id | paramno=3 | name=[16] ":test_version_id" | is_param=1 | param_type=1 | Key: Name: [21] :test_version_version | paramno=4 | name=[21] ":test_version_version" | is_param=1 | param_type=1 (UPDATE `test_version` SET `test_version`.`name` = \'aniu\',`test_version`.`available_number` = `test_version`.`available_number`+1,`test_version`.`real_number` = `test_version`.`real_number`+3,`test_version`.`version` = `test_version`.`version`+1 WHERE `test_version`.`available_number` = \'0.0000\' AND `test_version`.`real_number` = \'0.0000\' AND `test_version`.`id` = 1 AND `test_version`.`version` = 0 LIMIT 1)', $testVersion->select()->getLastSql());

        $testVersion->name = 'hello';
        self::assertSame(1, $testVersion->update()->flush());
        self::assertSame('SQL: [227] UPDATE `test_version` SET `test_version`.`version` = `test_version`.`version`+1,`test_version`.`name` = :named_param_name WHERE `test_version`.`id` = :test_version_id AND `test_version`.`version` = :test_version_version LIMIT 1 | Params:  3 | Key: Name: [17] :named_param_name | paramno=0 | name=[17] ":named_param_name" | is_param=1 | param_type=2 | Key: Name: [16] :test_version_id | paramno=1 | name=[16] ":test_version_id" | is_param=1 | param_type=1 | Key: Name: [21] :test_version_version | paramno=2 | name=[21] ":test_version_version" | is_param=1 | param_type=1 (UPDATE `test_version` SET `test_version`.`version` = `test_version`.`version`+1,`test_version`.`name` = \'hello\' WHERE `test_version`.`id` = 1 AND `test_version`.`version` = 1 LIMIT 1)', $testVersion->select()->getLastSql());
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

        self::assertSame(
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
        self::assertSame(1, $testVersion->id);
        self::assertNull($testVersion->version);
        self::assertSame('xiaoniuge', $testVersion->name);
        self::assertSame('0.0000', $testVersion->availableNumber);
        self::assertSame('0.0000', $testVersion->realNumber);

        $condition = [
            'available_number' => $testVersion->availableNumber,
            'real_number' => $testVersion->realNumber,
        ];
        $testVersion->name = 'aniu';
        $testVersion->availableNumber = Condition::raw('[available_number]+1');
        $testVersion->realNumber = Condition::raw('[real_number]+3');
        self::assertSame(
            1,
            $testVersion
                ->setCondition($condition)
                ->update()
                ->flush()
        );
        self::assertSame('SQL: [386] UPDATE `test_version` SET `test_version`.`name` = :named_param_name,`test_version`.`available_number` = `test_version`.`available_number`+1,`test_version`.`real_number` = `test_version`.`real_number`+3 WHERE `test_version`.`available_number` = :test_version_available_number AND `test_version`.`real_number` = :test_version_real_number AND `test_version`.`id` = :test_version_id LIMIT 1 | Params:  4 | Key: Name: [17] :named_param_name | paramno=0 | name=[17] ":named_param_name" | is_param=1 | param_type=2 | Key: Name: [30] :test_version_available_number | paramno=1 | name=[30] ":test_version_available_number" | is_param=1 | param_type=2 | Key: Name: [25] :test_version_real_number | paramno=2 | name=[25] ":test_version_real_number" | is_param=1 | param_type=2 | Key: Name: [16] :test_version_id | paramno=3 | name=[16] ":test_version_id" | is_param=1 | param_type=1 (UPDATE `test_version` SET `test_version`.`name` = \'aniu\',`test_version`.`available_number` = `test_version`.`available_number`+1,`test_version`.`real_number` = `test_version`.`real_number`+3 WHERE `test_version`.`available_number` = \'0.0000\' AND `test_version`.`real_number` = \'0.0000\' AND `test_version`.`id` = 1 LIMIT 1)', $testVersion->select()->getLastSql());

        $testVersion->name = 'hello';
        self::assertSame(1, $testVersion->update()->flush());
        self::assertSame('SQL: [120] UPDATE `test_version` SET `test_version`.`name` = :named_param_name WHERE `test_version`.`id` = :test_version_id LIMIT 1 | Params:  2 | Key: Name: [17] :named_param_name | paramno=0 | name=[17] ":named_param_name" | is_param=1 | param_type=2 | Key: Name: [16] :test_version_id | paramno=1 | name=[16] ":test_version_id" | is_param=1 | param_type=1 (UPDATE `test_version` SET `test_version`.`name` = \'hello\' WHERE `test_version`.`id` = 1 LIMIT 1)', $testVersion->select()->getLastSql());
    }

    #[Api([
        'zh-CN:title' => 'version.condition 设置是否启用乐观锁版本字段配合设置扩展查询条件',
    ])]
    public function testUpdateWithVersionAndWithCondition(): void
    {
        $connect = $this->createDatabaseConnect();

        self::assertSame(
            1,
            $connect
                ->table('test_version')
                ->insert([
                    'name' => 'xiaoniuge',
                ])
        );

        $testVersion = DemoVersion::select()->findEntity(1);

        $this->assertInstanceof(DemoVersion::class, $testVersion);
        self::assertSame(1, $testVersion->id);
        self::assertSame('xiaoniuge', $testVersion->name);
        self::assertSame('0.0000', $testVersion->availableNumber);
        self::assertSame('0.0000', $testVersion->realNumber);

        $testVersion->name = 'aniu';
        $testVersion->availableNumber = Condition::raw('[available_number]+1');
        $testVersion->realNumber = Condition::raw('[real_number]+3');
        self::assertSame(1, $testVersion->setVersion(true)->update()->flush());
        self::assertSame('SQL: [361] UPDATE `test_version` SET `test_version`.`name` = :named_param_name,`test_version`.`available_number` = `test_version`.`available_number`+1,`test_version`.`real_number` = `test_version`.`real_number`+3,`test_version`.`version` = `test_version`.`version`+1 WHERE `test_version`.`id` = :test_version_id AND `test_version`.`version` = :test_version_version LIMIT 1 | Params:  3 | Key: Name: [17] :named_param_name | paramno=0 | name=[17] ":named_param_name" | is_param=1 | param_type=2 | Key: Name: [16] :test_version_id | paramno=1 | name=[16] ":test_version_id" | is_param=1 | param_type=1 | Key: Name: [21] :test_version_version | paramno=2 | name=[21] ":test_version_version" | is_param=1 | param_type=1 (UPDATE `test_version` SET `test_version`.`name` = \'aniu\',`test_version`.`available_number` = `test_version`.`available_number`+1,`test_version`.`real_number` = `test_version`.`real_number`+3,`test_version`.`version` = `test_version`.`version`+1 WHERE `test_version`.`id` = 1 AND `test_version`.`version` = 0 LIMIT 1)', $testVersion->select()->getLastSql());

        $testVersion->refresh();
        $condition = ['available_number' => $testVersion->availableNumber];
        $testVersion->name = 'hello';
        $testVersion->availableNumber = Condition::raw('[available_number]+8');
        self::assertSame(1, $testVersion->setCondition($condition)->update()->flush());
        self::assertSame('SQL: [370] UPDATE `test_version` SET `test_version`.`version` = `test_version`.`version`+1,`test_version`.`name` = :named_param_name,`test_version`.`available_number` = `test_version`.`available_number`+8 WHERE `test_version`.`available_number` = :test_version_available_number AND `test_version`.`id` = :test_version_id AND `test_version`.`version` = :test_version_version LIMIT 1 | Params:  4 | Key: Name: [17] :named_param_name | paramno=0 | name=[17] ":named_param_name" | is_param=1 | param_type=2 | Key: Name: [30] :test_version_available_number | paramno=1 | name=[30] ":test_version_available_number" | is_param=1 | param_type=2 | Key: Name: [16] :test_version_id | paramno=2 | name=[16] ":test_version_id" | is_param=1 | param_type=1 | Key: Name: [21] :test_version_version | paramno=3 | name=[21] ":test_version_version" | is_param=1 | param_type=1 (UPDATE `test_version` SET `test_version`.`version` = `test_version`.`version`+1,`test_version`.`name` = \'hello\',`test_version`.`available_number` = `test_version`.`available_number`+8 WHERE `test_version`.`available_number` = \'1.0000\' AND `test_version`.`id` = 1 AND `test_version`.`version` = 1 LIMIT 1)', $testVersion->select()->getLastSql());
    }

    #[Api([
        'zh-CN:title' => 'version 设置是否启用乐观锁版本字段支持取消',
    ])]
    public function testUpdateWithVersionAndWithoutVersionCondition(): void
    {
        $connect = $this->createDatabaseConnect();

        self::assertSame(
            1,
            $connect
                ->table('test_version')
                ->insert([
                    'name' => 'xiaoniuge',
                ])
        );

        $testVersion = DemoVersion::select()->findEntity(1);

        $this->assertInstanceof(DemoVersion::class, $testVersion);
        self::assertSame(1, $testVersion->id);
        self::assertSame('xiaoniuge', $testVersion->name);
        self::assertSame('0.0000', $testVersion->availableNumber);
        self::assertSame('0.0000', $testVersion->realNumber);

        $testVersion->name = 'aniu';
        $testVersion->availableNumber = Condition::raw('[available_number]+1');
        $testVersion->realNumber = Condition::raw('[real_number]+3');
        self::assertSame(1, $testVersion->setVersion(false)->update()->flush());
        self::assertSame('SQL: [254] UPDATE `test_version` SET `test_version`.`name` = :named_param_name,`test_version`.`available_number` = `test_version`.`available_number`+1,`test_version`.`real_number` = `test_version`.`real_number`+3 WHERE `test_version`.`id` = :test_version_id LIMIT 1 | Params:  2 | Key: Name: [17] :named_param_name | paramno=0 | name=[17] ":named_param_name" | is_param=1 | param_type=2 | Key: Name: [16] :test_version_id | paramno=1 | name=[16] ":test_version_id" | is_param=1 | param_type=1 (UPDATE `test_version` SET `test_version`.`name` = \'aniu\',`test_version`.`available_number` = `test_version`.`available_number`+1,`test_version`.`real_number` = `test_version`.`real_number`+3 WHERE `test_version`.`id` = 1 LIMIT 1)', $testVersion->select()->getLastSql());

        $testVersion->name = 'hello';
        self::assertSame(1, $testVersion->update()->flush());
        self::assertSame('SQL: [120] UPDATE `test_version` SET `test_version`.`name` = :named_param_name WHERE `test_version`.`id` = :test_version_id LIMIT 1 | Params:  2 | Key: Name: [17] :named_param_name | paramno=0 | name=[17] ":named_param_name" | is_param=1 | param_type=2 | Key: Name: [16] :test_version_id | paramno=1 | name=[16] ":test_version_id" | is_param=1 | param_type=1 (UPDATE `test_version` SET `test_version`.`name` = \'hello\' WHERE `test_version`.`id` = 1 LIMIT 1)', $testVersion->select()->getLastSql());
    }

    #[Api([
        'zh-CN:title' => 'condition 设置扩展查询条件支持直接设置版本查询条件',
    ])]
    public function testUpdateWithCondition(): void
    {
        $connect = $this->createDatabaseConnect();

        self::assertSame(
            1,
            $connect
                ->table('test_version')
                ->insert([
                    'name' => 'xiaoniuge',
                ])
        );

        $testVersion = DemoVersion::select()->findEntity(1);

        $this->assertInstanceof(DemoVersion::class, $testVersion);
        self::assertSame(1, $testVersion->id);
        self::assertSame('xiaoniuge', $testVersion->name);
        self::assertSame('0.0000', $testVersion->availableNumber);
        self::assertSame('0.0000', $testVersion->realNumber);

        $testVersion->name = 'aniu';
        $testVersion->availableNumber = Condition::raw('[available_number]+1');
        $testVersion->realNumber = Condition::raw('[real_number]+3');
        self::assertSame(1, $testVersion->setVersion(true)->update()->flush());
        self::assertSame('SQL: [361] UPDATE `test_version` SET `test_version`.`name` = :named_param_name,`test_version`.`available_number` = `test_version`.`available_number`+1,`test_version`.`real_number` = `test_version`.`real_number`+3,`test_version`.`version` = `test_version`.`version`+1 WHERE `test_version`.`id` = :test_version_id AND `test_version`.`version` = :test_version_version LIMIT 1 | Params:  3 | Key: Name: [17] :named_param_name | paramno=0 | name=[17] ":named_param_name" | is_param=1 | param_type=2 | Key: Name: [16] :test_version_id | paramno=1 | name=[16] ":test_version_id" | is_param=1 | param_type=1 | Key: Name: [21] :test_version_version | paramno=2 | name=[21] ":test_version_version" | is_param=1 | param_type=1 (UPDATE `test_version` SET `test_version`.`name` = \'aniu\',`test_version`.`available_number` = `test_version`.`available_number`+1,`test_version`.`real_number` = `test_version`.`real_number`+3,`test_version`.`version` = `test_version`.`version`+1 WHERE `test_version`.`id` = 1 AND `test_version`.`version` = 0 LIMIT 1)', $testVersion->select()->getLastSql());

        $testVersion->refresh();
        $condition = ['available_number' => $testVersion->availableNumber, DemoVersion::VERSION => 9999];
        $testVersion->name = 'hello';
        $testVersion->availableNumber = Condition::raw('[available_number]+8');
        self::assertSame(0, $testVersion->setCondition($condition)->update()->flush());
        self::assertSame('SQL: [370] UPDATE `test_version` SET `test_version`.`version` = `test_version`.`version`+1,`test_version`.`name` = :named_param_name,`test_version`.`available_number` = `test_version`.`available_number`+8 WHERE `test_version`.`available_number` = :test_version_available_number AND `test_version`.`version` = :test_version_version AND `test_version`.`id` = :test_version_id LIMIT 1 | Params:  4 | Key: Name: [17] :named_param_name | paramno=0 | name=[17] ":named_param_name" | is_param=1 | param_type=2 | Key: Name: [30] :test_version_available_number | paramno=1 | name=[30] ":test_version_available_number" | is_param=1 | param_type=2 | Key: Name: [21] :test_version_version | paramno=2 | name=[21] ":test_version_version" | is_param=1 | param_type=1 | Key: Name: [16] :test_version_id | paramno=3 | name=[16] ":test_version_id" | is_param=1 | param_type=1 (UPDATE `test_version` SET `test_version`.`version` = `test_version`.`version`+1,`test_version`.`name` = \'hello\',`test_version`.`available_number` = `test_version`.`available_number`+8 WHERE `test_version`.`available_number` = \'1.0000\' AND `test_version`.`version` = 9999 AND `test_version`.`id` = 1 LIMIT 1)', $testVersion->select()->getLastSql());
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

        self::assertSame(
            0,
            $connect
                ->table('without_primarykey')
                ->insert([
                    'goods_id' => 1,
                    'description' => 'hello',
                ])
        );

        $withoutPrimarykey = WithoutPrimarykey::select()->findEntity(1);
        self::assertSame(['goods_id'], WithoutPrimarykey::getPrimaryKey());

        $this->assertInstanceof(WithoutPrimarykey::class, $withoutPrimarykey);
        self::assertSame(1, $withoutPrimarykey->goodsId);
        self::assertSame('hello', $withoutPrimarykey->description);

        $withoutPrimarykey->description = 'world';
        self::assertSame(1, $withoutPrimarykey->update()->flush());
        self::assertSame('SQL: [170] UPDATE `without_primarykey` SET `without_primarykey`.`description` = :named_param_description WHERE `without_primarykey`.`goods_id` = :without_primarykey_goods_id LIMIT 1 | Params:  2 | Key: Name: [24] :named_param_description | paramno=0 | name=[24] ":named_param_description" | is_param=1 | param_type=2 | Key: Name: [28] :without_primarykey_goods_id | paramno=1 | name=[28] ":without_primarykey_goods_id" | is_param=1 | param_type=1 (UPDATE `without_primarykey` SET `without_primarykey`.`description` = \'world\' WHERE `without_primarykey`.`goods_id` = 1 LIMIT 1)', $withoutPrimarykey->select()->getLastSql());
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
    public function testUpdateWithoutPrimaryKeyAndAllAreKey(): void
    {
        $connect = $this->createDatabaseConnect();

        self::assertSame(
            0,
            $connect
                ->table('without_primarykey')
                ->insert([
                    'goods_id' => 1,
                    'description' => 'hello',
                    'name' => 'world',
                ])
        );

        $withoutPrimarykey = WithoutPrimarykeyAndAllAreKey::select()->findOne();
        self::assertSame(['goods_id', 'description', 'name'], WithoutPrimarykeyAndAllAreKey::getPrimaryKey());

        $this->assertInstanceof(WithoutPrimarykeyAndAllAreKey::class, $withoutPrimarykey);
        self::assertSame(1, $withoutPrimarykey->goodsId);
        self::assertSame('hello', $withoutPrimarykey->description);
        self::assertSame('world', $withoutPrimarykey->name);

        $withoutPrimarykey->description = 'my';
        $withoutPrimarykey->name = 'php';
        self::assertSame(1, $withoutPrimarykey->update()->flush());
        self::assertSame('SQL: [350] UPDATE `without_primarykey` SET `without_primarykey`.`description` = :named_param_description,`without_primarykey`.`name` = :named_param_name WHERE `without_primarykey`.`goods_id` = :without_primarykey_goods_id AND `without_primarykey`.`description` = :without_primarykey_description AND `without_primarykey`.`name` = :without_primarykey_name LIMIT 1 | Params:  5 | Key: Name: [24] :named_param_description | paramno=0 | name=[24] ":named_param_description" | is_param=1 | param_type=2 | Key: Name: [17] :named_param_name | paramno=1 | name=[17] ":named_param_name" | is_param=1 | param_type=2 | Key: Name: [28] :without_primarykey_goods_id | paramno=2 | name=[28] ":without_primarykey_goods_id" | is_param=1 | param_type=1 | Key: Name: [31] :without_primarykey_description | paramno=3 | name=[31] ":without_primarykey_description" | is_param=1 | param_type=2 | Key: Name: [24] :without_primarykey_name | paramno=4 | name=[24] ":without_primarykey_name" | is_param=1 | param_type=2 (UPDATE `without_primarykey` SET `without_primarykey`.`description` = \'my\',`without_primarykey`.`name` = \'php\' WHERE `without_primarykey`.`goods_id` = 1 AND `without_primarykey`.`description` = \'hello\' AND `without_primarykey`.`name` = \'world\' LIMIT 1)', $withoutPrimarykey->select()->getLastSql());

        $withoutPrimarykey->name = 'new name';
        self::assertSame(1, $withoutPrimarykey->update()->flush());
        self::assertSame('SQL: [288] UPDATE `without_primarykey` SET `without_primarykey`.`name` = :named_param_name WHERE `without_primarykey`.`goods_id` = :without_primarykey_goods_id AND `without_primarykey`.`description` = :without_primarykey_description AND `without_primarykey`.`name` = :without_primarykey_name LIMIT 1 | Params:  4 | Key: Name: [17] :named_param_name | paramno=0 | name=[17] ":named_param_name" | is_param=1 | param_type=2 | Key: Name: [28] :without_primarykey_goods_id | paramno=1 | name=[28] ":without_primarykey_goods_id" | is_param=1 | param_type=1 | Key: Name: [31] :without_primarykey_description | paramno=2 | name=[31] ":without_primarykey_description" | is_param=1 | param_type=2 | Key: Name: [24] :without_primarykey_name | paramno=3 | name=[24] ":without_primarykey_name" | is_param=1 | param_type=2 (UPDATE `without_primarykey` SET `without_primarykey`.`name` = \'new name\' WHERE `without_primarykey`.`goods_id` = 1 AND `without_primarykey`.`description` = \'my\' AND `without_primarykey`.`name` = \'php\' LIMIT 1)', $withoutPrimarykey->select()->getLastSql());

        $withoutPrimarykey->name = 'new and new';
        $withoutPrimarykey->update();
        $withoutPrimarykey->name = 'new and new2';
        self::assertSame(1, $withoutPrimarykey->update()->flush());
        self::assertSame('SQL: [288] UPDATE `without_primarykey` SET `without_primarykey`.`name` = :named_param_name WHERE `without_primarykey`.`goods_id` = :without_primarykey_goods_id AND `without_primarykey`.`description` = :without_primarykey_description AND `without_primarykey`.`name` = :without_primarykey_name LIMIT 1 | Params:  4 | Key: Name: [17] :named_param_name | paramno=0 | name=[17] ":named_param_name" | is_param=1 | param_type=2 | Key: Name: [28] :without_primarykey_goods_id | paramno=1 | name=[28] ":without_primarykey_goods_id" | is_param=1 | param_type=1 | Key: Name: [31] :without_primarykey_description | paramno=2 | name=[31] ":without_primarykey_description" | is_param=1 | param_type=2 | Key: Name: [24] :without_primarykey_name | paramno=3 | name=[24] ":without_primarykey_name" | is_param=1 | param_type=2 (UPDATE `without_primarykey` SET `without_primarykey`.`name` = \'new and new2\' WHERE `without_primarykey`.`goods_id` = 1 AND `without_primarykey`.`description` = \'my\' AND `without_primarykey`.`name` = \'new name\' LIMIT 1)', $withoutPrimarykey->select()->getLastSql());
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

        self::assertSame(
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
        self::assertSame(1, $post->id);
        self::assertSame('hello world', $post->title);
        self::assertSame(1, $post->userId);
        self::assertSame('post summary', $post->summary);

        $postClone = clone $post;
        self::assertNull($postClone->id);
        self::assertSame('hello world', $postClone->title);
        self::assertSame(1, $postClone->userId);
        self::assertSame('post summary', $postClone->summary);

        $post->title = 'world';
        self::assertSame('hello world', $postClone->title);
        $postClone->title = 'goods';
        self::assertSame('world', $post->title);
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

        self::assertTrue($post->getNewed());
        self::assertNull($post->id);
        self::assertSame('hello world', $post->title);
        self::assertSame(1, $post->userId);
        self::assertSame('post summary', $post->summary);
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

        self::assertFalse($post->getNewed());
        self::assertSame(1, $post->id);
        self::assertSame('hello world', $post->title);
        self::assertSame(1, $post->userId);
        self::assertSame('post summary', $post->summary);
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

        self::assertTrue($post->getNewed());
        self::assertNull($post->id);
        self::assertSame('hello world', $post->title);
        self::assertSame(1, $post->userId);
        self::assertSame('post summary', $post->summary);
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

        self::assertFalse($post->getNewed());
        self::assertSame(1, $post->id);
        self::assertSame('hello world', $post->title);
        self::assertSame(1, $post->userId);
        self::assertSame('post summary', $post->summary);
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

        self::assertSame(
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

        Post::addGlobalScope('hello', static function (Select $select): void {
            $select->where('id', 5);
        });
        Post::select()->findAll();
        $sql = Post::select()->getLastSql();
        self::assertSame($sql, 'SQL: [97] SELECT `post`.* FROM `post` WHERE `post`.`delete_at` = :post_delete_at AND `post`.`id` = :post_id | Params:  2 | Key: Name: [15] :post_delete_at | paramno=0 | name=[15] ":post_delete_at" | is_param=1 | param_type=1 | Key: Name: [8] :post_id | paramno=1 | name=[8] ":post_id" | is_param=1 | param_type=1 (SELECT `post`.* FROM `post` WHERE `post`.`delete_at` = 0 AND `post`.`id` = 5)');
    }

    #[Api([
        'zh-CN:title' => 'withoutGlobalScope 不带指定全局作用域查询',
    ])]
    public function testWithoutGlobalScope(): void
    {
        $connect = $this->createDatabaseConnect();

        self::assertSame(
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

        Post::addGlobalScope('hello', static function (Select $select): void {
            $select->where('id', 5);
        });
        Post::withoutGlobalScope(['hello'])->findAll();
        $sql = Post::select()->getLastSql();
        self::assertSame($sql, 'SQL: [70] SELECT `post`.* FROM `post` WHERE `post`.`delete_at` = :post_delete_at | Params:  1 | Key: Name: [15] :post_delete_at | paramno=0 | name=[15] ":post_delete_at" | is_param=1 | param_type=1 (SELECT `post`.* FROM `post` WHERE `post`.`delete_at` = 0)');
    }

    #[Api([
        'zh-CN:title' => 'findEntity 通过主键或条件查找实体',
    ])]
    public function testFindEntity(): void
    {
        $connect = $this->createDatabaseConnect();

        self::assertSame(
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
        self::assertSame(1, $post->id);
        self::assertSame('hello world', $post->title);
        self::assertSame('post summary', $post->summary);
    }

    #[Api([
        'zh-CN:title' => 'findMany 通过主键或条件查找多个实体',
    ])]
    public function testFindMany(): void
    {
        $connect = $this->createDatabaseConnect();

        self::assertSame(
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
        self::assertInstanceOf(EntityCollection::class, $collection);
        $post = $collection[0];
        self::assertSame(1, $post->id);
        self::assertSame('hello world', $post->title);
        self::assertSame('post summary', $post->summary);
    }

    #[Api([
        'zh-CN:title' => 'findOrFail 通过主键或条件查找实体，未找到则抛出异常',
    ])]
    public function testFindOrFail(): void
    {
        $connect = $this->createDatabaseConnect();

        self::assertSame(
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
        self::assertSame(1, $post->id);
        self::assertSame('hello world', $post->title);
        self::assertSame('post summary', $post->summary);
    }

    #[Api([
        'zh-CN:title' => 'meta 返回实体类的元对象(虚拟实体)',
    ])]
    public function testMeta1(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('The virtual entity does not support select');

        DemoVirtualEntity::meta();
    }

    #[Api([
        'zh-CN:title' => 'delete 删除实体(虚拟实体)',
    ])]
    public function testVirtualDelete(): void
    {
        $demo = new DemoVirtualEntity(['id' => 1]);
        $demo->delete();
        self::assertSame(0, $demo->flush());
    }

    #[Api([
        'zh-CN:title' => 'create 新增实体(虚拟实体)',
    ])]
    public function testVirtualCreate(): void
    {
        $demo = new DemoVirtualEntity(['id' => 1]);
        $demo->create();
        self::assertSame(1, $demo->flush());
    }

    #[Api([
        'zh-CN:title' => 'update 更新实体(虚拟实体)',
    ])]
    public function testVirtualUpdate(): void
    {
        $demo = new DemoVirtualEntity(['id' => 1, 'name' => 'new']);
        $demo->update();
        self::assertSame(0, $demo->flush());
    }

    public function testVirtualUpdate2(): void
    {
        $demo = new DemoVirtualEntity(['id' => 1]);
        $demo->update();
        self::assertSame(0, $demo->flush());
    }

    #[Api([
        'zh-CN:title' => 'select 查询实体(虚拟实体)',
    ])]
    public function testVirtualSelect(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(
            'The virtual entity does not support select.'
        );

        DemoVirtualEntity::select();
    }

    public function test1(): void
    {
        $this->expectException(DuplicateKeyException::class);
        $this->expectExceptionMessage(
            'SQLSTATE[23000]: Integrity constraint violation: 1062 Duplicate entry \'foo\' for key'
        );
        $this->expectExceptionMessage(
            'uniq_identity'
        );

        $connect = $this->createDatabaseConnect();

        self::assertSame(
            1,
            $connect
                ->table('test_unique')
                ->insert([
                    'name' => 'hello world',
                    'identity' => 'foo',
                ])
        );

        $post = new DemoUnique([
            'name' => 'hello world2',
            'identity' => 'foo',
        ]);
        $post->save()->flush();
    }

    public function test2(): void
    {
        $connect = $this->createDatabaseConnect();

        self::assertSame(
            1,
            $connect
                ->table('test_unique')
                ->insert([
                    'name' => 'hello world',
                    'identity' => 'foo',
                ])
        );

        $post = new DemoUniqueNew([
            'name' => 'hello world23',
            'identity' => 'foo',
        ]);
        $affectedRow = $post->update()->flush();
        self::assertSame(1, $affectedRow);
    }

    #[Api([
        'zh-CN:title' => 'getColumnNames 回所有字段名字',
    ])]
    public function testColumnNames(): void
    {
        $this->initI18n();

        $data = <<<'eot'
{
    "id": "ID",
    "title": "标题",
    "user_id": "用户ID",
    "summary": "文章摘要",
    "create_at": "创建时间",
    "delete_at": "删除时间"
}
eot;

        self::assertSame(
            $data,
            $this->varJson(
                Post::getColumnNames(),
            )
        );
    }

    #[Api([
        'zh-CN:title' => 'getColumnName 返回字段名字',
    ])]
    public function testColumnName(): void
    {
        $this->initI18n();

        self::assertSame('用户ID', Post::getColumnName('user_id'));
    }

    public function testColumnName2(): void
    {
        self::assertSame('', Post::getColumnName('user_id_not_found'));
    }

    #[Api([
        'zh-CN:title' => '构造器初始化数据自动转换',
        'zh-CN:description' => <<<'EOT'
**fixture 定义**

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Database\Ddd\Entity\PostNew::class)]}
```
EOT,
    ])]
    public function test3(): void
    {
        $entity = new PostNew(['title' => 'foo']);
        $entity->create()->flush();
    }

    public function test13(): void
    {
        $entity = new PostNew5();
        $this->invokeTestMethod($entity, 'formatPropValue', ['foo', 'bar']);
    }

    public function test14(): void
    {
        $entity = new PostNew5();
        $this->invokeTestMethod($entity, 'transformPropValue', ['foo', 'bar']);
    }

    protected function initI18n(): void
    {
        $container = Container::singletons();
        $container->clear();

        $container->singleton('i18n', static function (): \I18nMock {
            return new \I18nMock();
        });
    }

    protected function getDatabaseTable(): array
    {
        return ['post', 'test_unique', 'composite_id', 'test_version', 'without_primarykey'];
    }
}
