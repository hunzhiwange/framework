<?php

declare(strict_types=1);

namespace Tests\Database\Query;

use Tests\Database\DatabaseTestCase as TestCase;

/**
 * @api(
 *     title="Query lang.distinct",
 *     zh-CN:title="查询语言.distinct",
 *     path="database/query/distinct",
 *     zh-CN:description="
 * **函数原型**
 *
 * ``` php
 * {[\Leevel\Kernel\Utils\Doc::getMethodBody(\Leevel\Database\Condition::class, 'distinct', 'define')]}
 * ```
 * ",
 * )
 *
 * @internal
 *
 * @coversNothing
 */
final class DistinctTest extends TestCase
{
    /**
     * @api(
     *     zh-CN:title="查询去重",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
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
            $this->varJson(
                $connect
                    ->table('test_query')
                    ->distinct()
                    ->findAll(true)
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="取消查询去重",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
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
            $this->varJson(
                $connect
                    ->table('test_query')
                    ->distinct()
                    ->distinct(false)
                    ->findAll(true),
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
            $this->varJson(
                $connect
                    ->table('test_query')
                    ->if($condition)
                    ->distinct()
                    ->else()
                    ->distinct(false)
                    ->fi()
                    ->findAll(true)
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
            $this->varJson(
                $connect
                    ->table('test_query')
                    ->if($condition)
                    ->distinct()
                    ->else()
                    ->distinct(false)
                    ->fi()
                    ->findAll(true)
            )
        );
    }
}
