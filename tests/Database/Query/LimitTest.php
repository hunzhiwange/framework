<?php

declare(strict_types=1);

namespace Tests\Database\Query;

use Leevel\Kernel\Utils\Api;
use Tests\Database\DatabaseTestCase as TestCase;

#[Api([
    'title' => 'Query lang.limit',
    'zh-CN:title' => '查询语言.limit',
    'path' => 'database/query/limit',
])]
final class LimitTest extends TestCase
{
    #[Api([
        'zh-CN:title' => 'limit 限制条数',
    ])]
    public function testBaseUse(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` LIMIT 5,10",
                [],
                false
            ]
            eot;

        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect
                    ->table('test_query')
                    ->limit(5, 10)
                    ->find(),
                $connect
            )
        );
    }

    #[Api([
        'zh-CN:title' => '指示仅查询第一个符合条件的记录',
    ])]
    public function testOne(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` LIMIT 1",
                [],
                false
            ]
            eot;

        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect
                    ->table('test_query')
                    ->one()
                    ->find(),
                $connect,
                1
            )
        );
    }

    #[Api([
        'zh-CN:title' => '指示查询所有符合条件的记录',
    ])]
    public function testAll(): void
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
                    ->all()
                    ->find(),
                $connect,
                2
            )
        );
    }

    #[Api([
        'zh-CN:title' => '查询几条记录',
    ])]
    public function testTop(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` LIMIT 0,15",
                [],
                false
            ]
            eot;

        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect
                    ->table('test_query')
                    ->top(15)
                    ->find(),
                $connect,
                3
            )
        );
    }

    public function testTopFlow(): void
    {
        $condition = false;
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` LIMIT 0,6",
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
                    ->top(5)
                    ->else()
                    ->top(6)
                    ->fi()
                    ->findAll(),
                $connect
            )
        );
    }

    public function testTopFlow2(): void
    {
        $condition = true;
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` LIMIT 0,5",
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
                    ->top(5)
                    ->else()
                    ->top(6)
                    ->fi()
                    ->findAll(),
                $connect
            )
        );
    }

    public function testLimitFlow(): void
    {
        $condition = false;
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` LIMIT 2,3",
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
                    ->limit(0, 5)
                    ->else()
                    ->limit(2, 3)
                    ->fi()
                    ->findAll(),
                $connect
            )
        );
    }

    public function testLimitFlow2(): void
    {
        $condition = true;
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` LIMIT 0,5",
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
                    ->limit(0, 5)
                    ->else()
                    ->limit(2, 3)
                    ->fi()
                    ->findAll(),
                $connect
            )
        );
    }
}
