<?php

declare(strict_types=1);

namespace Tests\Database\Query;

use Leevel\Database\Condition;
use Leevel\Di\Container;
use Leevel\Kernel\Utils\Api;
use Tests\Database\DatabaseTestCase as TestCase;
use Tests\Database\Query\Database\Demo;
use Tests\Database\Query\Database\Demo2;
use Tests\Database\Query\Database\Demo3;
use Tests\Database\Query\Database\ForceMaster;

#[Api([
    'title' => 'Query lang.middleware',
    'zh-CN:title' => '查询语言.middleware',
    'path' => 'database/query/middleware',
])]
final class MiddlewareTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();

        Condition::withContainer(null);
    }

    #[Api([
        'zh-CN:title' => 'middleware 基础用法',
        'zh-CN:description' => <<<'EOT'
**fixture 定义**

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Database\Query\Database\ForceMaster::class)]}
```
EOT,
    ])]
    public function testBaseUse(): void
    {
        $connect = $this->createDatabaseConnectMock();
        Condition::withContainer(new Container());

        $sql = <<<'eot'
            [
                "\/*FORCE_MASTER*\/ SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`id` = :test_query_id",
                {
                    "test_query_id": [
                        5
                    ]
                },
                false
            ]
            eot;

        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect
                    ->table('test_query')
                    ->middlewares(ForceMaster::class)
                    ->where('id', '=', 5)
                    ->findAll(),
                $connect
            )
        );
    }

    public function test1(): void
    {
        $connect = $this->createDatabaseConnectMock();
        Condition::withContainer(new Container());

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`id` = :test_query_id",
                {
                    "test_query_id": [
                        5
                    ]
                },
                false
            ]
            eot;

        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect
                    ->table('test_query')
                    ->middlewares()
                    ->where('id', '=', 5)
                    ->findAll(),
                $connect
            )
        );
    }

    public function test2(): void
    {
        $connect = $this->createDatabaseConnectMock();
        Condition::withContainer(new Container());

        $sql = <<<'eot'
            [
                "\/*FORCE_MASTER*\/ SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`id` = :test_query_id",
                {
                    "test_query_id": [
                        5
                    ]
                },
                false
            ]
            eot;

        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect
                    ->table('test_query')
                    ->middlewares(ForceMaster::class, ForceMaster::class)
                    ->where('id', '=', 5)
                    ->findAll(),
                $connect
            )
        );
    }

    public function test3(): void
    {
        $connect = $this->createDatabaseConnectMock();
        Condition::withContainer(new Container());

        $sql = <<<'eot'
            [
                "\/*hello comment*\/ SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`id` > :test_query_id AND `test_query`.`id` <= :test_query_id_1 AND `test_query`.`id` = :test_query_id_2",
                {
                    "test_query_id": [
                        5
                    ],
                    "test_query_id_1": [
                        90
                    ],
                    "test_query_id_2": [
                        5
                    ]
                },
                false
            ]
            eot;

        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect
                    ->table('test_query')
                    ->middlewares(Demo::class, Demo::class)
                    ->where('id', '=', 5)
                    ->findAll(),
                $connect
            )
        );
    }

    public function test4(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Condition middleware must be string.'
        );

        $this->createDatabaseConnectMock();
        Condition::withContainer(new Container());
        Condition::registerMiddlewares([1]);
    }

    public function test5(): void
    {
        $connect = $this->createDatabaseConnectMock();
        Condition::withContainer(new Container());

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`id` > :test_query_id AND `test_query`.`id` <= :test_query_id_1 AND `test_query`.`id` = :test_query_id_2",
                {
                    "test_query_id": [
                        5
                    ],
                    "test_query_id_1": [
                        90
                    ],
                    "test_query_id_2": [
                        5
                    ]
                },
                false
            ]
            eot;

        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect
                    ->table('test_query')
                    ->middlewares(Demo2::class, Demo2::class)
                    ->where('id', '=', 5)
                    ->findAll(),
                $connect
            )
        );
    }

    public function test6(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Condition middleware Tests\\Database\\Query\\Database\\Demo3 was invalid.'
        );

        $this->createDatabaseConnectMock();
        Condition::withContainer(new Container());
        Condition::registerMiddlewares([Demo3::class]);
    }

    #[Api([
        'zh-CN:title' => 'registerMiddlewares 注册查询中间件',
    ])]
    public function testRegisterMiddlewares(): void
    {
        $this->createDatabaseConnectMock();
        Condition::withContainer(new Container());
        $data = Condition::registerMiddlewares([Demo::class]);

        $result = <<<'eot'
            [
                [
                    "Tests\\Database\\Query\\Database\\Demo@terminate"
                ],
                [
                    "Tests\\Database\\Query\\Database\\Demo@handle"
                ]
            ]
            eot;

        static::assertSame(
            $result,
            $this->varJson($data)
        );
    }

    public function testRegisterMiddlewares2(): void
    {
        $this->createDatabaseConnectMock();
        Condition::withContainer(new Container());
        $data = Condition::registerMiddlewares([Demo::class, Demo::class], true);

        $result = <<<'eot'
            [
                [
                    "Tests\\Database\\Query\\Database\\Demo@terminate"
                ],
                [
                    "Tests\\Database\\Query\\Database\\Demo@handle"
                ]
            ]
            eot;

        static::assertSame(
            $result,
            $this->varJson($data)
        );
    }

    #[Api([
        'zh-CN:title' => 'middleware 支持参数传递',
        'zh-CN:description' => <<<'EOT'
**fixture 定义**

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Database\Query\Database\Demo::class)]}
```
EOT,
    ])]
    public function testBaseUseWithArgs(): void
    {
        $connect = $this->createDatabaseConnectMock();
        Condition::withContainer(new Container());

        $sql = <<<'eot'
            [
                "\/*hello comment*\/ SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`id` > :test_query_id AND `test_query`.`id` <= :test_query_id_1 AND `test_query`.`id` = :test_query_id_2",
                {
                    "test_query_id": [
                        5
                    ],
                    "test_query_id_1": [
                        90
                    ],
                    "test_query_id_2": [
                        5
                    ]
                },
                false
            ]
            eot;

        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect
                    ->table('test_query')
                    ->middlewares(Demo::class)
                    ->where('id', '=', 5)
                    ->findAll(),
                $connect
            )
        );
    }

    public function test7(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage(
            'Container was not set.'
        );

        $connect = $this->createDatabaseConnectMock();

        $connect
            ->table('test_query')
            ->middlewares(ForceMaster::class)
            ->where('id', '=', 5)
            ->findAll();
    }

    public function test8(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage(
            'Container was not set.'
        );

        $connect = $this->createDatabaseConnectMock();
        $condition = new Condition($connect);
        $this->invokeTestMethod($condition, 'throughMiddlewareTerminate', [[], [], function():void {
        }]);
    }
}
