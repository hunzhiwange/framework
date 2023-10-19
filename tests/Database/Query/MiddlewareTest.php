<?php

declare(strict_types=1);

namespace Tests\Database\Query;

use Leevel\Database\Condition;
use Leevel\Di\Container;
use Tests\Database\DatabaseTestCase as TestCase;
use Tests\Database\Query\Database\Demo;
use Tests\Database\Query\Database\ForceMaster;

#[Api([
    'title' => 'Query lang.middleware',
    'zh-CN:title' => '查询语言.middleware',
    'path' => 'database/query/middleware',
])]
/**
 * @internal
 */
final class MiddlewareTest extends TestCase
{
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
}
