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

namespace Tests\Database\Ddd;

use I18nMock;
use Leevel\Database\Condition;
use Leevel\Di\Container;
use Tests\Database\DatabaseTestCase as TestCase;
use Tests\Database\Ddd\Entity\CompositeId;
use Tests\Database\Ddd\Entity\DemoPropErrorEntity;
use Tests\Database\Ddd\Entity\DemoVersion;
use Tests\Database\Ddd\Entity\EntityWithEnum;
use Tests\Database\Ddd\Entity\EntityWithEnum2;
use Tests\Database\Ddd\Entity\EntityWithInvalidEnum;
use Tests\Database\Ddd\Entity\EntityWithoutPrimaryKey;
use Tests\Database\Ddd\Entity\Relation\Post;
use Tests\Database\Ddd\Entity\Relation\PostForReplace;

/**
 * @api(
 *     title="实体",
 *     path="orm/entity",
 *     description="
 * 实体是整个系统最为核心的基本单位，实体封装了一些常用的功能。
 * ",
 * )
 */
class EntityTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        Container::singletons()->clear();
    }

    public function testPropNotDefined(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Entity `Tests\\Database\\Ddd\\Entity\\DemoPropErrorEntity` prop or field of struct `_name` was not defined.'
        );

        $entity = new DemoPropErrorEntity();
        $entity->name = 5;
    }

    public function testPropNotDefinedWhenNew(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Entity `Tests\\Database\\Ddd\\Entity\\DemoPropErrorEntity` prop or field of struct `_name` was not defined.'
        );

        $entity = new DemoPropErrorEntity(['name' => 5]);
    }

    public function testSetPropManyTimesDoNothing(): void
    {
        $entity = new Post();
        $entity->title = 5;
        $entity->title = 5;
        $entity->title = 5;
        $entity->title = 5;

        $this->assertSame(5, $entity->title);
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

    /**
     * @api(
     *     title="withProps 批量设置属性数据",
     *     description="",
     *     note="",
     * )
     */
    public function testWithProps(): void
    {
        $entity = new Post();
        $entity->withProps([
            'title'   => 'foo',
            'summary' => 'bar',
        ]);

        $this->assertSame('foo', $entity->title);
        $this->assertSame('bar', $entity->summary);
        $this->assertSame(['title', 'summary'], $entity->changed());
    }

    /**
     * @api(
     *     title="enum 获取枚举",
     *     description="
     * **fixture 定义**
     *
     * **Tests\Database\Ddd\Entity\EntityWithEnum**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Database\Ddd\Entity\EntityWithEnum::class)]}
     * ```
     * ",
     *     note="",
     * )
     */
    public function testEntityWithEnum(): void
    {
        $this->initI18n();

        $entity = new EntityWithEnum([
            'title'   => 'foo',
            'status'  => '1',
        ]);

        $this->assertSame('foo', $entity->title);
        $this->assertSame('1', $entity->status);

        $data = <<<'eot'
            {
                "title": "foo"
            }
            eot;

        $this->assertSame(
            $data,
            $this->varJson(
                $entity->toArray(['title'])
            )
        );

        $data = <<<'eot'
            {
                "title": "foo",
                "status": "1",
                "status_enum": "启用"
            }
            eot;

        $this->assertSame(
            $data,
            $this->varJson(
                $entity->toArray(),
                2
            )
        );

        $this->assertSame('启用', $entity->enum('status', '1'));
        $this->assertSame('禁用', $entity->enum('status', '0'));
        $this->assertFalse($entity->enum('not', '0'));
        $this->assertFalse($entity->enum('not'));

        $data = <<<'eot'
            [
                [
                    0,
                    "禁用"
                ],
                [
                    1,
                    "启用"
                ]
            ]
            eot;

        $this->assertSame(
            $data,
            $this->varJson(
                $entity->enum('status'),
                3
            )
        );
    }

    /**
     * @api(
     *     title="enum 获取枚举字符例子",
     *     description="
     * **fixture 定义**
     *
     * **Tests\Database\Ddd\Entity\EntityWithEnum2**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Database\Ddd\Entity\EntityWithEnum2::class)]}
     * ```
     * ",
     *     note="",
     * )
     */
    public function testEntityWithEnum2(): void
    {
        $this->initI18n();

        $entity = new EntityWithEnum2([
            'title'   => 'foo',
            'status'  => 't',
        ]);

        $data = <<<'eot'
            [
                [
                    "f",
                    "禁用"
                ],
                [
                    "t",
                    "启用"
                ]
            ]
            eot;

        $this->assertSame(
            $data,
            $this->varJson(
                $entity->enum('status')
            )
        );
    }

    public function testEntityWithEnumItemNotFound(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Value not a enum in the field `status` of entity `Tests\\Database\\Ddd\\Entity\\EntityWithEnum`.'
        );

        $entity = new EntityWithEnum([
            'title'   => 'foo',
            'status'  => '1',
        ]);

        $entity->enum('status', '5');
    }

    public function testEntityWithEnumItemNotFound2(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Value not a enum in the field `status` of entity `Tests\\Database\\Ddd\\Entity\\EntityWithEnum`.'
        );

        $entity = new EntityWithEnum([
            'title'   => 'foo',
            'status'  => '5',
        ]);

        $entity->toArray();
    }

    public function testEntityWithInvalidEnum(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Invalid enum in the field `status` of entity `Tests\\Database\\Ddd\\Entity\\EntityWithInvalidEnum`.'
        );

        $this->initI18n();

        $entity = new EntityWithInvalidEnum([
            'title'   => 'foo',
            'status'  => '1',
        ]);

        $this->assertSame('foo', $entity->title);
        $this->assertSame('1', $entity->status);

        $data = <<<'eot'
            {
                "title": "foo"
            }
            eot;

        $this->assertSame(
            $data,
            $this->varJson(
                $entity->toArray(['title'])
            )
        );

        $entity->toArray();
    }

    /**
     * @api(
     *     title="hasChanged 检测属性是否已经改变",
     *     description="",
     *     note="",
     * )
     */
    public function testHasChanged(): void
    {
        $entity = new Post();
        $this->assertFalse($entity->hasChanged('title'));
        $entity->title = 'change';
        $this->assertTrue($entity->hasChanged('title'));
    }

    /**
     * @api(
     *     title="addChanged 添加指定属性为已改变",
     *     description="",
     *     note="",
     * )
     */
    public function testAddChanged(): void
    {
        $entity = new Post();
        $data = <<<'eot'
            []
            eot;

        $this->assertSame(
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

        $this->assertSame(
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

        $this->assertSame(
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

        $this->assertSame(
            $data,
            $this->varJson(
                $entity->changed(),
                1
            )
        );
    }

    /**
     * @api(
     *     title="deleteChanged 删除已改变属性",
     *     description="",
     *     note="",
     * )
     */
    public function testDeleteChanged(): void
    {
        $entity = new Post();
        $data = <<<'eot'
            []
            eot;

        $this->assertSame(
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

        $this->assertSame(
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

        $this->assertSame(
            $data,
            $this->varJson(
                $entity->changed(),
                2,
            )
        );
    }

    /**
     * @api(
     *     title="clearChanged 清空已改变属性",
     *     description="",
     *     note="",
     * )
     */
    public function testClearChanged(): void
    {
        $entity = new Post();
        $data = <<<'eot'
            []
            eot;

        $this->assertSame(
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

        $this->assertSame(
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

        $this->assertSame(
            $data,
            $this->varJson(
                $entity->changed(),
                2,
            )
        );
    }

    public function testSinglePrimaryKeyNotFound(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Entity Tests\\Database\\Ddd\\Entity\\EntityWithoutPrimaryKey do not have primary key or composite id not supported.'
        );

        $entity = new EntityWithoutPrimaryKey();
        $entity->singlePrimaryKey();
    }

    public function testSinglePrimaryKeyNotSupportComposite(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Entity Tests\\Database\\Ddd\\Entity\\CompositeId do not have primary key or composite id not supported.'
        );

        $entity = new CompositeId();
        $entity->singlePrimaryKey();
    }

    /**
     * @api(
     *     title="singleId 返回供查询的主键字段值",
     *     description="",
     *     note="",
     * )
     */
    public function testSingleId(): void
    {
        $entity = new Post();
        $this->assertNull($entity->singleId());

        $entity = new Post(['id' => 5]);
        $this->assertSame(5, $entity->singleId());
    }

    /**
     * @api(
     *     title="idCondition 获取查询主键条件",
     *     description="",
     *     note="",
     * )
     */
    public function testIdCondition(): void
    {
        $entity = new Post(['id' => 5]);
        $this->assertSame(['id' => 5], $entity->idCondition());
    }

    public function testIdConditionHasNoPrimaryKeyData(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Entity Tests\\Database\\Ddd\\Entity\\Relation\\Post has no primary key data.'
        );

        $entity = new Post();
        $this->assertNull($entity->idCondition());
    }

    /**
     * @api(
     *     title="实体属性数组访问 ArrayAccess.offsetExists 支持",
     *     description="",
     *     note="",
     * )
     */
    public function testArrayAccessOffsetExists(): void
    {
        $entity = new Post(['id' => 5, 'title' => 'hello']);
        $this->assertTrue(isset($entity['title']));
        $this->assertFalse(isset($entity['user_id']));
    }

    /**
     * @api(
     *     title="实体属性数组访问 ArrayAccess.offsetSet 支持",
     *     description="",
     *     note="",
     * )
     */
    public function testArrayAccessOffsetSet(): void
    {
        $entity = new Post(['id' => 5]);
        $this->assertFalse(isset($entity['title']));
        $this->assertNull($entity->title);
        $entity['title'] = 'world';
        $this->assertTrue(isset($entity['title']));
        $this->assertSame('world', $entity->title);
    }

    /**
     * @api(
     *     title="实体属性数组访问 ArrayAccess.offsetGet 支持",
     *     description="",
     *     note="",
     * )
     */
    public function testArrayAccessOffsetGet(): void
    {
        $entity = new Post(['id' => 5]);
        $this->assertNull($entity['title']);
        $entity['title'] = 'world';
        $this->assertSame('world', $entity['title']);
    }

    /**
     * @api(
     *     title="实体属性数组访问 ArrayAccess.offsetUnset 支持",
     *     description="",
     *     note="",
     * )
     */
    public function testArrayAccessOffsetUnset(): void
    {
        $entity = new Post(['id' => 5]);
        $this->assertNull($entity['title']);
        $entity['title'] = 'world';
        $this->assertSame('world', $entity['title']);
        unset($entity['title']);
        $this->assertNull($entity['title']);
    }

    /**
     * @api(
     *     title="实体属性访问魔术方法 __isset 支持",
     *     description="",
     *     note="",
     * )
     */
    public function testMagicIsset(): void
    {
        $entity = new Post(['id' => 5, 'title' => 'hello']);
        $this->assertTrue(isset($entity->title));
        $this->assertFalse(isset($entity->userId));
    }

    /**
     * @api(
     *     title="实体属性访问魔术方法 __set 支持",
     *     description="",
     *     note="",
     * )
     */
    public function testMagicSet(): void
    {
        $entity = new Post(['id' => 5]);
        $this->assertFalse(isset($entity->title));
        $this->assertNull($entity->title);
        $entity->title = 'world';
        $this->assertTrue(isset($entity->title));
        $this->assertSame('world', $entity->title);
    }

    /**
     * @api(
     *     title="实体属性访问魔术方法 __get 支持",
     *     description="",
     *     note="",
     * )
     */
    public function testMagicGet(): void
    {
        $entity = new Post(['id' => 5]);
        $this->assertNull($entity->title);
        $entity->title = 'world';
        $this->assertSame('world', $entity->title);
    }

    /**
     * @api(
     *     title="实体属性访问魔术方法 __unset 支持",
     *     description="",
     *     note="",
     * )
     */
    public function testMagicUnset(): void
    {
        $entity = new Post(['id' => 5]);
        $this->assertNull($entity->title);
        $entity->title = 'world';
        $this->assertSame('world', $entity->title);
        unset($entity->title);
        $this->assertNull($entity->title);
    }

    /**
     * @api(
     *     title="setter 设置属性值",
     *     description="",
     *     note="",
     * )
     */
    public function testCallSetter(): void
    {
        $entity = new Post(['id' => 5]);
        $this->assertNull($entity->title);
        $this->assertNull($entity->userId);
        $entity->setTitle('hello');
        $entity->setUserId(5);
        $this->assertSame('hello', $entity->title);
        $this->assertSame(5, $entity->userId);
    }

    /**
     * @api(
     *     title="getter 获取属性值",
     *     description="",
     *     note="",
     * )
     */
    public function testCallGetter(): void
    {
        $entity = new Post(['id' => 5]);
        $this->assertNull($entity->getTitle());
        $this->assertNull($entity->getUserId());
        $entity->setTitle('hello');
        $entity->setUserId(5);
        $this->assertSame('hello', $entity->getTitle());
        $this->assertSame(5, $entity->getUserId());
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

    /**
     * @api(
     *     title="find 获取实体查询对象",
     *     description="",
     *     note="",
     * )
     */
    public function testStaticFind(): void
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

        $post = Post::find()->where('id', 1)->findOne();
        $this->assertSame('hello world', $post->title);
        $this->assertSame(1, $post->userId);
        $this->assertSame('post summary', $post->summary);
    }

    /**
     * @api(
     *     title="connectSandbox 数据库连接沙盒",
     *     description="",
     *     note="",
     * )
     */
    public function testConnectSandbox(): void
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

        $post = Post::connectSandbox('password_right', function () {
            return Post::find()->where('id', 1)->findOne();
        });

        $this->assertSame('hello world', $post->title);
        $this->assertSame(1, $post->userId);
        $this->assertSame('post summary', $post->summary);
    }

    public function testConnectSandboxAndPasswordIsError(): void
    {
        // 因为消息 IP 有变，所有这里不测试异常消息
        // SQLSTATE[HY000] [1045] Access denied for user 'root'@'10.0.2.2' (using password: YES)
        $this->expectException(\PDOException::class);

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

        Post::connectSandbox('password_not_right', function () {
            return Post::find()->where('id', 1)->findOne();
        });
    }

    /**
     * @api(
     *     title="newed 确定对象是否对应数据库中的一条记录",
     *     description="",
     *     note="",
     * )
     */
    public function testNewed(): void
    {
        $entity = new Post();
        $this->assertTrue($entity->newed());

        $entity = new Post(['id' => 5]);
        $this->assertTrue($entity->newed());

        $entity = new Post(['id' => 5], true);
        $this->assertFalse($entity->newed());
    }

    /**
     * @api(
     *     title="id 获取主键值",
     *     description="",
     *     note="",
     * )
     */
    public function testId(): void
    {
        $entity = new Post();
        $this->assertNull($entity->id());

        $entity = new Post(['id' => 5]);
        $this->assertSame(5, $entity->id());
    }

    /**
     * @api(
     *     title="id 获取复合主键值",
     *     description="
     * **fixture 定义**
     *
     * **Tests\Database\Ddd\Entity\CompositeId**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Database\Ddd\Entity\CompositeId::class)]}
     * ```
     * ",
     *     note="",
     * )
     */
    public function testCompositeId(): void
    {
        $entity = new CompositeId();
        $this->assertNull($entity->id());

        $entity = new CompositeId(['id1' => 5]);
        $this->assertNull($entity->id());

        $entity = new CompositeId(['id1' => 5, 'id2' => 8]);
        $this->assertSame(['id1' => 5, 'id2' => 8], $entity->id());
    }

    /**
     * @api(
     *     title="refresh 从数据库重新读取当前对象的属性",
     *     description="",
     *     note="",
     * )
     */
    public function testRefresh(): void
    {
        $post1 = new Post();
        $post1->create()->flush();
        $this->assertInstanceof(Post::class, $post1);
        $this->assertSame(1, $post1->id);
        $this->assertNull($post1->userId);
        $this->assertNull($post1->title);
        $this->assertNull($post1->summary);
        $this->assertNull($post1->delete_at);

        $post1->refresh();
        $this->assertSame(1, $post1->id);
        $this->assertSame(0, $post1->userId);
        $this->assertSame('', $post1->title);
        $this->assertSame('', $post1->summary);
        $this->assertSame(0, $post1->delete_at);
    }

    /**
     * @api(
     *     title="refresh 从数据库重新读取当前对象的属性支持复合主键",
     *     description="",
     *     note="",
     * )
     */
    public function testRefreshWithCompositeId(): void
    {
        $entity = new CompositeId(['id1' => 1, 'id2' => 3]);
        $entity->create()->flush();
        $this->assertInstanceof(CompositeId::class, $entity);
        $this->assertSame(1, $entity->id1);
        $this->assertSame(3, $entity->id2);
        $this->assertNull($entity->name);

        $entity->refresh();
        $this->assertSame(1, $entity->id1);
        $this->assertSame(3, $entity->id2);
        $this->assertSame('', $entity->name);
    }

    public function testRefreshButNoPrimaryKeyData(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Entity Tests\\Database\\Ddd\\Entity\\Relation\\Post has no primary key data.'
        );

        $entity = new Post();
        $this->assertInstanceof(Post::class, $entity);
        $this->assertNull($entity->id);

        $entity->refresh();
    }

    public function testRefreshWithCompositeIdButNoPrimaryKeyData(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Entity Tests\\Database\\Ddd\\Entity\\CompositeId has no primary key data.'
        );

        $entity = new CompositeId();
        $entity->create()->flush();
        $this->assertInstanceof(CompositeId::class, $entity);
        $this->assertNull($entity->id1);
        $this->assertNull($entity->id2);
        $this->assertNull($entity->name);

        $entity->refresh();
    }

    public function testRefreshWithoutPrimaryKey(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Entity Tests\\Database\\Ddd\\Entity\\EntityWithoutPrimaryKey has no primary key.'
        );

        $entity = new EntityWithoutPrimaryKey();
        $entity->refresh();
    }

    public function testFlushWithoutData(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(
            'Entity `Tests\\Database\\Ddd\\Entity\\Relation\\Post` has no data need to be flush.'
        );

        $post1 = new Post();
        $post1->flush();
    }

    public function testFlushTwiceWithoutData(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(
            'Entity `Tests\\Database\\Ddd\\Entity\\Relation\\Post` has no data need to be flush.'
        );

        $post1 = new Post();
        $post1->create();
        $post1->flush();
        $post1->flush();
    }

    public function testReplaceAndThrowException(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage(
            'Update error'
        );

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

        $post = new PostForReplace(['id' => 1, 'title' => 'hello', 'delete_at' => 0]);
        $post->replace();
        $post->flush();
    }

    /**
     * @api(
     *     title="构造器支持忽略未定义属性",
     *     description="
     * `$ignoreUndefinedProp` 用于数据库添加了字段，但是我们的实体并没有更新字段，查询得到的实体对象将会忽略掉新增的字段而不报错。
     * ",
     *     note="",
     * )
     */
    public function testIgnoreUndefinedProp(): void
    {
        $entity = new Post(['undefined_prop' => 5], true, true);
        $this->assertSame([], $entity->toArray());
    }

    /**
     * @api(
     *     title="update 更新数据带上版本号",
     *     description="
     * 可以用于并发控制，例如商品库存，客户余额等。
     *
     * **fixture 定义**
     *
     * **Tests\Database\Ddd\Entity\DemoVersion**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Database\Ddd\Entity\DemoVersion::class)]}
     * ```
     * ",
     *     note="",
     * )
     */
    public function testUpdateWithVersion(): void
    {
        $connect = $this->createDatabaseConnect();

        $this->assertSame(
            1,
            $connect
                ->table('test_version')
                ->insert([
                    'name'     => 'xiaoniuge',
                ]));

        $testVersion = DemoVersion::select()->findEntity(1);

        $this->assertInstanceof(DemoVersion::class, $testVersion);
        $this->assertSame(1, $testVersion->id);
        $this->assertSame('xiaoniuge', $testVersion->name);
        $this->assertSame('0.0000', $testVersion->availableNumber);
        $this->assertSame('0.0000', $testVersion->realNumber);

        $testVersion->name = 'aniu';
        $testVersion->availableNumber = Condition::raw('[available_number]+1');
        $testVersion->realNumber = Condition::raw('[real_number]+3');
        $this->assertSame(1, $testVersion->update()->flush());
        $this->assertSame('SQL: [491] UPDATE `test_version` SET `test_version`.`name` = :pdonamedparameter_name,`test_version`.`available_number` = `test_version`.`available_number`+1,`test_version`.`real_number` = `test_version`.`real_number`+3,`test_version`.`version` = `test_version`.`version`+1 WHERE `test_version`.`id` = :test_version_id AND `test_version`.`available_number` = :test_version_available_number AND `test_version`.`real_number` = :test_version_real_number AND `test_version`.`version` = :test_version_version | Params:  5 | Key: Name: [23] :pdonamedparameter_name | paramno=0 | name=[23] ":pdonamedparameter_name" | is_param=1 | param_type=2 | Key: Name: [16] :test_version_id | paramno=1 | name=[16] ":test_version_id" | is_param=1 | param_type=1 | Key: Name: [30] :test_version_available_number | paramno=2 | name=[30] ":test_version_available_number" | is_param=1 | param_type=2 | Key: Name: [25] :test_version_real_number | paramno=3 | name=[25] ":test_version_real_number" | is_param=1 | param_type=2 | Key: Name: [21] :test_version_version | paramno=4 | name=[21] ":test_version_version" | is_param=1 | param_type=1 (UPDATE `test_version` SET `test_version`.`name` = \'aniu\',`test_version`.`available_number` = `test_version`.`available_number`+1,`test_version`.`real_number` = `test_version`.`real_number`+3,`test_version`.`version` = `test_version`.`version`+1 WHERE `test_version`.`id` = 1 AND `test_version`.`available_number` = \'0.0000\' AND `test_version`.`real_number` = \'0.0000\' AND `test_version`.`version` = 0)', $testVersion->select()->getLastSql());

        $testVersion->name = 'hello';
        $this->assertSame(1, $testVersion->update()->flush());
        $this->assertSame('SQL: [225] UPDATE `test_version` SET `test_version`.`name` = :pdonamedparameter_name,`test_version`.`version` = `test_version`.`version`+1 WHERE `test_version`.`id` = :test_version_id AND `test_version`.`version` = :test_version_version | Params:  3 | Key: Name: [23] :pdonamedparameter_name | paramno=0 | name=[23] ":pdonamedparameter_name" | is_param=1 | param_type=2 | Key: Name: [16] :test_version_id | paramno=1 | name=[16] ":test_version_id" | is_param=1 | param_type=1 | Key: Name: [21] :test_version_version | paramno=2 | name=[21] ":test_version_version" | is_param=1 | param_type=1 (UPDATE `test_version` SET `test_version`.`name` = \'hello\',`test_version`.`version` = `test_version`.`version`+1 WHERE `test_version`.`id` = 1 AND `test_version`.`version` = 1)', $testVersion->select()->getLastSql());
    }

    /**
     * @api(
     *     title="version 设置允许乐观锁查询条件字段",
     *     description="",
     *     note="",
     * )
     */
    public function testUpdateWithVersionAndWithVersionCondition(): void
    {
        $connect = $this->createDatabaseConnect();

        $this->assertSame(
            1,
            $connect
                ->table('test_version')
                ->insert([
                    'name'     => 'xiaoniuge',
                ]));

        $testVersion = DemoVersion::select()->findEntity(1);

        $this->assertInstanceof(DemoVersion::class, $testVersion);
        $this->assertSame(1, $testVersion->id);
        $this->assertSame('xiaoniuge', $testVersion->name);
        $this->assertSame('0.0000', $testVersion->availableNumber);
        $this->assertSame('0.0000', $testVersion->realNumber);

        $testVersion->name = 'aniu';
        $testVersion->availableNumber = Condition::raw('[available_number]+1');
        $testVersion->realNumber = Condition::raw('[real_number]+3');
        $this->assertSame(1, $testVersion->version([])->update()->flush());
        $this->assertSame('SQL: [359] UPDATE `test_version` SET `test_version`.`name` = :pdonamedparameter_name,`test_version`.`available_number` = `test_version`.`available_number`+1,`test_version`.`real_number` = `test_version`.`real_number`+3,`test_version`.`version` = `test_version`.`version`+1 WHERE `test_version`.`id` = :test_version_id AND `test_version`.`version` = :test_version_version | Params:  3 | Key: Name: [23] :pdonamedparameter_name | paramno=0 | name=[23] ":pdonamedparameter_name" | is_param=1 | param_type=2 | Key: Name: [16] :test_version_id | paramno=1 | name=[16] ":test_version_id" | is_param=1 | param_type=1 | Key: Name: [21] :test_version_version | paramno=2 | name=[21] ":test_version_version" | is_param=1 | param_type=1 (UPDATE `test_version` SET `test_version`.`name` = \'aniu\',`test_version`.`available_number` = `test_version`.`available_number`+1,`test_version`.`real_number` = `test_version`.`real_number`+3,`test_version`.`version` = `test_version`.`version`+1 WHERE `test_version`.`id` = 1 AND `test_version`.`version` = 0)', $testVersion->select()->getLastSql());

        $testVersion->name = 'hello';
        $testVersion->availableNumber = Condition::raw('[available_number]+8');
        $this->assertSame(1, $testVersion->version(['available_number'])->update()->flush());
        $this->assertSame('SQL: [368] UPDATE `test_version` SET `test_version`.`name` = :pdonamedparameter_name,`test_version`.`available_number` = `test_version`.`available_number`+8,`test_version`.`version` = `test_version`.`version`+1 WHERE `test_version`.`id` = :test_version_id AND `test_version`.`available_number` = :test_version_available_number AND `test_version`.`version` = :test_version_version | Params:  4 | Key: Name: [23] :pdonamedparameter_name | paramno=0 | name=[23] ":pdonamedparameter_name" | is_param=1 | param_type=2 | Key: Name: [16] :test_version_id | paramno=1 | name=[16] ":test_version_id" | is_param=1 | param_type=1 | Key: Name: [30] :test_version_available_number | paramno=2 | name=[30] ":test_version_available_number" | is_param=1 | param_type=2 | Key: Name: [21] :test_version_version | paramno=3 | name=[21] ":test_version_version" | is_param=1 | param_type=1 (UPDATE `test_version` SET `test_version`.`name` = \'hello\',`test_version`.`available_number` = `test_version`.`available_number`+8,`test_version`.`version` = `test_version`.`version`+1 WHERE `test_version`.`id` = 1 AND `test_version`.`available_number` = \'1.0000\' AND `test_version`.`version` = 1)', $testVersion->select()->getLastSql());
    }

    /**
     * @api(
     *     title="version 设置允许乐观锁查询条件字段支持 NULL 值取消乐观锁",
     *     description="",
     *     note="",
     * )
     */
    public function testUpdateWithVersionAndWithoutVersionCondition(): void
    {
        $connect = $this->createDatabaseConnect();

        $this->assertSame(
            1,
            $connect
                ->table('test_version')
                ->insert([
                    'name'     => 'xiaoniuge',
                ]));

        $testVersion = DemoVersion::select()->findEntity(1);

        $this->assertInstanceof(DemoVersion::class, $testVersion);
        $this->assertSame(1, $testVersion->id);
        $this->assertSame('xiaoniuge', $testVersion->name);
        $this->assertSame('0.0000', $testVersion->availableNumber);
        $this->assertSame('0.0000', $testVersion->realNumber);

        $testVersion->name = 'aniu';
        $testVersion->availableNumber = Condition::raw('[available_number]+1');
        $testVersion->realNumber = Condition::raw('[real_number]+3');
        $this->assertSame(1, $testVersion->version(null)->update()->flush());
        $this->assertSame('SQL: [252] UPDATE `test_version` SET `test_version`.`name` = :pdonamedparameter_name,`test_version`.`available_number` = `test_version`.`available_number`+1,`test_version`.`real_number` = `test_version`.`real_number`+3 WHERE `test_version`.`id` = :test_version_id | Params:  2 | Key: Name: [23] :pdonamedparameter_name | paramno=0 | name=[23] ":pdonamedparameter_name" | is_param=1 | param_type=2 | Key: Name: [16] :test_version_id | paramno=1 | name=[16] ":test_version_id" | is_param=1 | param_type=1 (UPDATE `test_version` SET `test_version`.`name` = \'aniu\',`test_version`.`available_number` = `test_version`.`available_number`+1,`test_version`.`real_number` = `test_version`.`real_number`+3 WHERE `test_version`.`id` = 1)', $testVersion->select()->getLastSql());

        $testVersion->name = 'hello';
        $this->assertSame(1, $testVersion->update()->flush());
        $this->assertSame('SQL: [118] UPDATE `test_version` SET `test_version`.`name` = :pdonamedparameter_name WHERE `test_version`.`id` = :test_version_id | Params:  2 | Key: Name: [23] :pdonamedparameter_name | paramno=0 | name=[23] ":pdonamedparameter_name" | is_param=1 | param_type=2 | Key: Name: [16] :test_version_id | paramno=1 | name=[16] ":test_version_id" | is_param=1 | param_type=1 (UPDATE `test_version` SET `test_version`.`name` = \'hello\' WHERE `test_version`.`id` = 1)', $testVersion->select()->getLastSql());
    }

    protected function initI18n(): void
    {
        $container = Container::singletons();
        $container->clear();

        $container->singleton('i18n', function (): I18nMock {
            return new I18nMock();
        });
    }

    protected function getDatabaseTable(): array
    {
        return ['post', 'composite_id', 'test_version'];
    }
}
