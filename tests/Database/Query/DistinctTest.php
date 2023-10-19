<?php

declare(strict_types=1);

namespace Tests\Database\Query;

use Tests\Database\DatabaseTestCase as TestCase;

#[Api([
    'title' => 'Query lang.distinct',
    'zh-CN:title' => '查询语言.distinct',
    'path' => 'database/query/distinct',
    'zh-CN:description' => <<<'EOT'
**函数原型**

``` php
{[\Leevel\Kernel\Utils\Doc::getMethodBody(\Leevel\Database\Condition::class, 'distinct', 'define')]}
```
EOT,
])]
final class DistinctTest extends TestCase
{
    #[Api([
        'zh-CN:title' => '查询去重',
    ])]
    public function testBaseUse(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT DISTINCT `test_query`.* FROM `test_query`",
                [],
                false
            ]
            eot;

        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect
                    ->table('test_query')
                    ->distinct()
                    ->findAll(),
                $connect
            )
        );
    }

    #[Api([
        'zh-CN:title' => '取消查询去重',
    ])]
    public function testCancelDistinct(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query`",
                [],
                false
            ]
            eot;

        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect
                    ->table('test_query')
                    ->distinct()
                    ->distinct(false)
                    ->findAll(),
                $connect,
                1
            )
        );
    }

    public function testFlow(): void
    {
        $condition = false;
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query`",
                [],
                false
            ]
            eot;

        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect
                    ->table('test_query')
                    ->if($condition)
                    ->distinct()
                    ->else()
                    ->distinct(false)
                    ->fi()
                    ->findAll(),
                $connect
            )
        );
    }

    public function testFlow2(): void
    {
        $condition = true;

        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT DISTINCT `test_query`.* FROM `test_query`",
                [],
                false
            ]
            eot;

        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect
                    ->table('test_query')
                    ->if($condition)
                    ->distinct()
                    ->else()
                    ->distinct(false)
                    ->fi()
                    ->findAll(),
                $connect
            )
        );
    }
}
