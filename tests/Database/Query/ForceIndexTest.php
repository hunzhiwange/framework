<?php

declare(strict_types=1);

namespace Tests\Database\Query;

use Tests\Database\DatabaseTestCase as TestCase;

/**
 * @api(
 *     title="Query lang.forceIndex",
 *     zh-CN:title="查询语言.forceIndex",
 *     path="database/query/forceindex",
 *     zh-CN:description="",
 * )
 */
class ForceIndexTest extends TestCase
{
    /**
     * @api(
     *     zh-CN:title="forceIndex,ignoreIndex 基础用法",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testBaseUse(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` FORCE INDEX(nameindex,statusindex) IGNORE INDEX(testindex) WHERE `test_query`.`id` = :test_query_id",
                {
                    "test_query_id": [
                        5
                    ]
                },
                false
            ]
            eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->table('test_query')
                    ->forceIndex('nameindex,statusindex')
                    ->ignoreIndex('testindex')
                    ->where('id', '=', 5)
                    ->findAll(true)
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="forceIndex 数组支持",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testForceIndexWithArray(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` FORCE INDEX(nameindex,statusindex) WHERE `test_query`.`id` = :test_query_id",
                {
                    "test_query_id": [
                        2
                    ]
                },
                false
            ]
            eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->table('test_query')
                    ->forceIndex(['nameindex', 'statusindex'])
                    ->where('id', '=', 2)
                    ->findAll(true)
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="ignoreIndex 数组支持",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testIgnoreIndexWithArray(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` IGNORE INDEX(nameindex,statusindex) WHERE `test_query`.`id` = :test_query_id",
                {
                    "test_query_id": [
                        6
                    ]
                },
                false
            ]
            eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->table('test_query')
                    ->ignoreIndex(['nameindex', 'statusindex'])
                    ->where('id', '=', 6)
                    ->findAll(true)
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
            ->findAll(true);
    }

    public function testForceIndexFlow(): void
    {
        $condition = false;
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` IGNORE INDEX(testindex) WHERE `test_query`.`id` = :test_query_id",
                {
                    "test_query_id": [
                        5
                    ]
                },
                false
            ]
            eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->table('test_query')
                    ->if($condition)
                    ->forceIndex('nameindex,statusindex')
                    ->else()
                    ->ignoreIndex('testindex')
                    ->fi()
                    ->where('id', '=', 5)
                    ->findAll(true)
            )
        );
    }

    public function testForceIndexFlow2(): void
    {
        $condition = true;
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` FORCE INDEX(nameindex,statusindex) WHERE `test_query`.`id` = :test_query_id",
                {
                    "test_query_id": [
                        5
                    ]
                },
                false
            ]
            eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->table('test_query')
                    ->if($condition)
                    ->forceIndex('nameindex,statusindex')
                    ->else()
                    ->ignoreIndex('testindex')
                    ->fi()
                    ->where('id', '=', 5)
                    ->findAll(true)
            )
        );
    }
}
