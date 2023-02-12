<?php

declare(strict_types=1);

namespace Tests\Database\Ddd;

use Leevel\Database\Ddd\Entity;
use Tests\Database\DatabaseTestCase as TestCase;
use Tests\Database\Ddd\Entity\DemoToArrayBlackEntity;
use Tests\Database\Ddd\Entity\DemoToArrayEntity;
use Tests\Database\Ddd\Entity\DemoToArrayShowPropNullEntity;
use Tests\Database\Ddd\Entity\DemoToArrayShowPropNullRelationEntity;
use Tests\Database\Ddd\Entity\DemoToArrayShowPropNullRelationTargetEntity;
use Tests\Database\Ddd\Entity\DemoToArrayWhiteEntity;
use Tests\Database\Ddd\Entity\Relation\Post;
use Tests\Database\Ddd\Entity\Relation\User;

/**
 * @api(
 *     zh-CN:title="实体导出数组",
 *     path="orm/toarray",
 *     zh-CN:description="
 * 我们可以将实体导出为数组来方便处理数据。
 * ",
 * )
 *
 * @internal
 *
 * @coversNothing
 */
final class EntityToArrayTest extends TestCase
{
    /**
     * @api(
     *     zh-CN:title="toArray 基本使用方法",
     *     zh-CN:description="
     * **fixture 定义**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getMethodBody(\Tests\Database\Ddd\EntityToArrayTest::class, 'makeEntity')]}
     * ```
     *
     * **Tests\Database\Ddd\Entity\DemoToArrayEntity**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Database\Ddd\Entity\DemoToArrayEntity::class)]}
     * ```
     * ",
     *     zh-CN:note="",
     * )
     */
    public function testBaseUse(): void
    {
        $entity = $this->makeEntity();

        $data = <<<'eot'
            {
                "name": "实体名字",
                "description": "goods name",
                "address": "四川成都",
                "foo_bar": "foo",
                "hello": "hello world"
            }
            eot;

        static::assertSame(
            $data,
            $this->varJsonSql(
                $entity->toArray()
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="toArray 实体对象转数组支持白名单",
     *     zh-CN:description="
     * `toArray` 第一个参数为白名单，设置了白名单，只有白名单的字段才能够转换为数组数据。
     * ",
     *     zh-CN:note="",
     * )
     */
    public function testWithWhite(): void
    {
        $entity = $this->makeEntity();

        $data = <<<'eot'
            {
                "name": "实体名字",
                "description": "goods name",
                "address": "四川成都",
                "foo_bar": "foo",
                "hello": "hello world"
            }
            eot;

        static::assertSame(
            $data,
            $this->varJsonSql(
                $entity->toArray()
            )
        );

        $data = <<<'eot'
            {
                "name": "实体名字"
            }
            eot;

        static::assertSame(
            $data,
            $this->varJsonSql(
                $entity
                    ->only(['name'])
                    ->toArray(),
                1
            )
        );

        $data = <<<'eot'
            {
                "name": "实体名字",
                "description": "goods name"
            }
            eot;

        static::assertSame(
            $data,
            $this->varJsonSql(
                $entity
                    ->only(['name', 'description'])
                    ->toArray(),
                2
            )
        );

        $data = <<<'eot'
            {
                "name": "实体名字",
                "description": "goods name",
                "hello": "hello world"
            }
            eot;

        static::assertSame(
            $data,
            $this->varJsonSql(
                $entity
                    ->only(['name', 'description', 'hello'])
                    ->toArray(),
                3
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="toArray 实体对象转数组支持黑名单",
     *     zh-CN:description="
     * `toArray` 第二个参数为白名单，设置了黑名单但是没有设置白名单，只有不属于黑名单的字段才能够转换为数组数据。
     * ",
     *     zh-CN:note="",
     * )
     */
    public function testWithBlack(): void
    {
        $entity = $this->makeEntity();

        $data = <<<'eot'
            {
                "name": "实体名字",
                "description": "goods name",
                "address": "四川成都",
                "foo_bar": "foo",
                "hello": "hello world"
            }
            eot;

        static::assertSame(
            $data,
            $this->varJsonSql(
                $entity->toArray()
            )
        );

        $data = <<<'eot'
            {
                "description": "goods name",
                "address": "四川成都",
                "foo_bar": "foo",
                "hello": "hello world"
            }
            eot;

        static::assertSame(
            $data,
            $this->varJsonSql(
                $entity
                    ->except(['name'])
                    ->toArray(),
                1
            )
        );

        $data = <<<'eot'
            {
                "address": "四川成都",
                "foo_bar": "foo",
                "hello": "hello world"
            }
            eot;

        static::assertSame(
            $data,
            $this->varJsonSql(
                $entity
                    ->except(['name', 'description'])
                    ->toArray(),
                2
            )
        );

        $data = <<<'eot'
            {
                "description": "goods name",
                "hello": "hello world"
            }
            eot;

        static::assertSame(
            $data,
            $this->varJsonSql(
                $entity
                    ->except(['foo_bar', 'name', 'address'])
                    ->toArray(),
                3
            )
        );
    }

    public function testWithWhiteAndBlack(): void
    {
        $entity = $this->makeEntity();

        $data = <<<'eot'
            {
                "name": "实体名字",
                "description": "goods name",
                "address": "四川成都",
                "foo_bar": "foo",
                "hello": "hello world"
            }
            eot;

        static::assertSame(
            $data,
            $this->varJsonSql(
                $entity->toArray()
            )
        );

        $data = <<<'eot'
            {
                "hello": "hello world"
            }
            eot;

        static::assertSame(
            $data,
            $this->varJsonSql(
                $entity
                    ->only(['hello'])
                    ->except(['description'])
                    ->toArray(),
                1
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="toArray 实体对象转数组支持字段设置为白名单",
     *     zh-CN:description="
     * 可以通过 `STRUCT` 中的定义 `\Leevel\Database\Ddd\Entity::SHOW_PROP_WHITE` 来设置字段白名单。
     *
     * 值得注意的是， `toArray` 的第一个参数白名单优先级更高。
     *
     * 如果设置了白名单，只有白名单的字段才能够转换为数组数据。
     *
     * **fixture 定义**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getMethodBody(\Tests\Database\Ddd\EntityToArrayTest::class, 'makeWhiteEntity')]}
     * ```
     *
     * **Tests\Database\Ddd\Entity\DemoToArrayWhiteEntity**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Database\Ddd\Entity\DemoToArrayWhiteEntity::class)]}
     * ```
     * ",
     *     zh-CN:note="",
     * )
     */
    public function testWithWhiteEntity(): void
    {
        $entity = $this->makeWhiteEntity();

        $data = <<<'eot'
            {
                "description": "goods name",
                "foo_bar": "foo"
            }
            eot;

        static::assertSame(
            $data,
            $this->varJsonSql(
                $entity->toArray()
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="toArray 实体对象转数组支持字段设置为黑名单",
     *     zh-CN:description="
     * 可以通过 `STRUCT` 中的定义 `\Leevel\Database\Ddd\Entity::SHOW_PROP_BLACK` 来设置字段黑名单。
     *
     * 值得注意的是， `toArray` 的第二个参数黑名单优先级更高。
     *
     * 如果设置了黑名单，设置了黑名单但是没有设置白名单，只有不属于黑名单的字段才能够转换为数组数据。
     *
     * **fixture 定义**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getMethodBody(\Tests\Database\Ddd\EntityToArrayTest::class, 'makeBlackEntity')]}
     * ```
     *
     * **Tests\Database\Ddd\Entity\DemoToArrayBlackEntity**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Database\Ddd\Entity\DemoToArrayBlackEntity::class)]}
     * ```
     * ",
     *     zh-CN:note="",
     * )
     */
    public function testWithBlackEntity(): void
    {
        $entity = $this->makeBlackEntity();

        $data = <<<'eot'
            {
                "name": "实体名字",
                "address": "四川成都",
                "hello": "hello world"
            }
            eot;

        static::assertSame(
            $data,
            $this->varJsonSql(
                $entity->toArray()
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="toArray 实体对象转数组支持转换关联实体数据",
     *     zh-CN:description="
     * **fixture 定义**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getMethodBody(\Tests\Database\Ddd\EntityToArrayTest::class, 'makeRelationEntity')]}
     * ```
     *
     * **Tests\Database\Ddd\Entity\Relation\User**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Database\Ddd\Entity\Relation\User::class)]}
     * ```
     *
     * **Tests\Database\Ddd\Entity\Relation\Post**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Database\Ddd\Entity\Relation\Post::class)]}
     * ```
     * ",
     *     zh-CN:note="",
     * )
     */
    public function testWithRelation(): void
    {
        $entity = $this->makeRelationEntity();

        $data = <<<'eot'
            {
                "id": 5,
                "title": "I am title",
                "user_id": 7,
                "summary": "I am summary",
                "user": {
                    "id": 7,
                    "name": "xiaoniuge"
                }
            }
            eot;

        static::assertSame(
            $data,
            $this->varJsonSql(
                $entity->toArray()
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="toArray 实体对象转数组支持转换关联实体数据（黑白名单）",
     *     zh-CN:description="
     * `toArray` 第三个参数为关联实体的黑白名单。
     * ",
     *     zh-CN:note="",
     * )
     */
    public function testWithRelationWhiteAndBlack(): void
    {
        $entity = $this->makeRelationEntity();

        $data = <<<'eot'
            {
                "id": 5,
                "title": "I am title",
                "user_id": 7,
                "summary": "I am summary",
                "user": {
                    "name": "xiaoniuge"
                }
            }
            eot;

        static::assertSame(
            $data,
            $this->varJsonSql(
                $entity
                    ->each(function ($value, $k) {
                        if ('user' === $k) {
                            $value = $value->only(['name']);
                        }

                        return $value;
                    })
                    ->toArray()
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="toArray 实体对象转数组支持 NULL 值字段默认指定数据",
     *     zh-CN:description="
     * **fixture 定义**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getMethodBody(\Tests\Database\Ddd\EntityToArrayTest::class, 'makeShowPropNullEntity')]}
     * ```
     *
     * **Tests\Database\Ddd\Entity\DemoToArrayShowPropNullEntity**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Database\Ddd\Entity\DemoToArrayShowPropNullEntity::class)]}
     * ```
     * ",
     *     zh-CN:note="",
     * )
     */
    public function testWithShowPropNull(): void
    {
        $entity = $this->makeShowPropNullEntity();

        $data = <<<'eot'
            {
                "name": "实体名字",
                "description": "goods name",
                "address": "",
                "foo_bar": null,
                "hello": "default_value"
            }
            eot;

        static::assertSame(
            $data,
            $this->varJsonSql(
                $entity->toArray()
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="toArray 实体对象转数组支持 NULL 值字段默认指定数据（关联模型）",
     *     zh-CN:description="
     * **fixture 定义**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getMethodBody(\Tests\Database\Ddd\EntityToArrayTest::class, 'makeRelationShowPropNullEntity')]}
     * ```
     *
     * **Tests\Database\Ddd\Entity\DemoToArrayShowPropNullRelationTargetEntity**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Database\Ddd\Entity\DemoToArrayShowPropNullRelationTargetEntity::class)]}
     * ```
     *
     * **Tests\Database\Ddd\Entity\DemoToArrayShowPropNullRelationEntity**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Database\Ddd\Entity\DemoToArrayShowPropNullRelationEntity::class)]}
     * ```
     * ",
     *     zh-CN:note="",
     * )
     */
    public function testWithShowPropNullForRelation(): void
    {
        $entity = $this->makeRelationShowPropNullEntity();

        $data = <<<'eot'
            {
                "id": 5,
                "name": "I am name",
                "description": "I am description",
                "address": "",
                "foo_bar": null,
                "hello": "default_value",
                "target": {
                    "id": 5,
                    "name": "xiaoniuge"
                }
            }
            eot;

        static::assertSame(
            $data,
            $this->varJsonSql(
                $entity->toArray()
            )
        );
    }

    protected function makeWhiteEntity(): DemoToArrayWhiteEntity
    {
        $entity = new DemoToArrayWhiteEntity();
        $this->assertInstanceof(Entity::class, $entity);
        $entity->name = '实体名字';
        $entity->description = 'goods name';
        $entity->address = '四川成都';
        $entity->foo_bar = 'foo';
        $entity->hello = 'hello world';

        return $entity;
    }

    protected function makeBlackEntity(): DemoToArrayBlackEntity
    {
        $entity = new DemoToArrayBlackEntity();
        $this->assertInstanceof(Entity::class, $entity);
        $entity->name = '实体名字';
        $entity->description = 'goods name';
        $entity->address = '四川成都';
        $entity->foo_bar = 'foo';
        $entity->hello = 'hello world';

        return $entity;
    }

    protected function makeEntity(): DemoToArrayEntity
    {
        $entity = new DemoToArrayEntity();
        $this->assertInstanceof(Entity::class, $entity);
        $entity->name = '实体名字';
        $entity->description = 'goods name';
        $entity->address = '四川成都';
        $entity->foo_bar = 'foo';
        $entity->hello = 'hello world';

        return $entity;
    }

    protected function makeRelationEntity(): Post
    {
        $user = new User(['id' => 7]);
        $user->name = 'xiaoniuge';

        $entity = new Post(['id' => 5]);
        $this->assertInstanceof(Post::class, $entity);
        $entity->title = 'I am title';
        $entity->summary = 'I am summary';
        $entity->userId = 7;
        $entity->withRelationProp('user', $user);

        return $entity;
    }

    protected function makeShowPropNullEntity(): DemoToArrayShowPropNullEntity
    {
        $entity = new DemoToArrayShowPropNullEntity();
        $this->assertInstanceof(Entity::class, $entity);
        $entity->name = '实体名字';
        $entity->description = 'goods name';

        return $entity;
    }

    protected function makeRelationShowPropNullEntity(): DemoToArrayShowPropNullRelationEntity
    {
        $target = new DemoToArrayShowPropNullRelationTargetEntity(['id' => 5]);
        $target->name = 'xiaoniuge';

        $entity = new DemoToArrayShowPropNullRelationEntity(['id' => 5]);
        $this->assertInstanceof(DemoToArrayShowPropNullRelationEntity::class, $entity);
        $entity->name = 'I am name';
        $entity->description = 'I am description';
        $entity->withRelationProp('target', $target);

        return $entity;
    }
}
