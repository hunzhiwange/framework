<?php

declare(strict_types=1);

namespace Tests\Database\Query;

use Leevel\Kernel\Utils\Api;
use Tests\Database\DatabaseTestCase as TestCase;

#[Api([
    'title' => 'Query lang.forceIndex',
    'zh-CN:title' => '查询语言.forceIndex',
    'path' => 'database/query/forceindex',
])]
final class ForceIndexTest extends TestCase
{
    #[Api([
        'zh-CN:title' => 'forceIndex,ignoreIndex 基础用法',
    ])]
    public function testBaseUse(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` FORCE INDEX(idx_nameindex,idx_statusindex) IGNORE INDEX(idx_testindex) WHERE `test_query`.`id` = :test_query_id",
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
                    ->forceIndex('idx_nameindex,idx_statusindex')
                    ->ignoreIndex('idx_testindex')
                    ->where('id', '=', 5)
                    ->findAll(),
                $connect
            )
        );
    }

    #[Api([
        'zh-CN:title' => 'forceIndex 数组支持',
    ])]
    public function testForceIndexWithArray(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` FORCE INDEX(idx_nameindex,idx_statusindex) WHERE `test_query`.`id` = :test_query_id",
                {
                    "test_query_id": [
                        2
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
                    ->forceIndex(['idx_nameindex', 'idx_statusindex'])
                    ->where('id', '=', 2)
                    ->findAll(),
                $connect
            )
        );
    }

    #[Api([
        'zh-CN:title' => 'ignoreIndex 数组支持',
    ])]
    public function testIgnoreIndexWithArray(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` IGNORE INDEX(idx_nameindex,idx_statusindex) WHERE `test_query`.`id` = :test_query_id",
                {
                    "test_query_id": [
                        6
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
                    ->ignoreIndex(['idx_nameindex', 'idx_statusindex'])
                    ->where('id', '=', 6)
                    ->findAll(),
                $connect
            )
        );
    }

    public function testForceIndexTypeNotSupported(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Invalid Index type `NOT_SUPPORT`.'
        );

        $connect = $this->createDatabaseConnectMock();

        $connect
            ->table('test_query')
            ->forceIndex('foo', 'NOT_SUPPORT')
            ->findAll()
        ;
    }

    public function testForceIndexFlow(): void
    {
        $condition = false;
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` IGNORE INDEX(idx_testindex) WHERE `test_query`.`id` = :test_query_id",
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
                    ->if($condition)
                    ->forceIndex('idx_nameindex,idx_statusindex')
                    ->else()
                    ->ignoreIndex('idx_testindex')
                    ->fi()
                    ->where('id', '=', 5)
                    ->findAll(),
                $connect
            )
        );
    }

    public function testForceIndexFlow2(): void
    {
        $condition = true;
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` FORCE INDEX(idx_nameindex,idx_statusindex) WHERE `test_query`.`id` = :test_query_id",
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
                    ->if($condition)
                    ->forceIndex('idx_nameindex,idx_statusindex')
                    ->else()
                    ->ignoreIndex('idx_testindex')
                    ->fi()
                    ->where('id', '=', 5)
                    ->findAll(),
                $connect
            )
        );
    }
}
