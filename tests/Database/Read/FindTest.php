<?php

declare(strict_types=1);

namespace Tests\Database\Read;

use Tests\Database\DatabaseTestCase as TestCase;

/**
 * @api(
 *     zh-CN:title="查询数据.find",
 *     path="database/read/find",
 *     zh-CN:description="",
 * )
 *
 * @internal
 *
 * @coversNothing
 */
final class FindTest extends TestCase
{
    /**
     * @api(
     *     zh-CN:title="find 查询基础用法",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testBaseUse(): void
    {
        $connect = $this->createDatabaseConnectMock();
        $sql = <<<'eot'
            [
                "SELECT `test`.* FROM `test`",
                [],
                false
            ]
            eot;

        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect

                    ->table('test')
                    ->find(),
                $connect
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="find 查询指定数量",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testFindLimit(): void
    {
        $connect = $this->createDatabaseConnectMock();
        $sql = <<<'eot'
            [
                "SELECT `test`.* FROM `test` LIMIT 0,5",
                [],
                false
            ]
            eot;

        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect

                    ->table('test')
                    ->find(5),
                $connect,
                1
            )
        );
    }
}
