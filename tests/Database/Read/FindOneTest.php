<?php

declare(strict_types=1);

namespace Tests\Database\Read;

use Tests\Database\DatabaseTestCase as TestCase;

/**
 * @api(
 *     zh-CN:title="查询单条数据.findOne",
 *     path="database/read/findone",
 *     zh-CN:description="",
 * )
 */
final class FindOneTest extends TestCase
{
    /**
     * @api(
     *     zh-CN:title="findOne 查询单条数据",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testBaseUse(): void
    {
        $connect = $this->createDatabaseConnectMock();
        $sql = <<<'eot'
            [
                "SELECT `test`.* FROM `test` LIMIT 1",
                [],
                false
            ]
            eot;

        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect->table('test')
                    ->findOne(),
                $connect
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="one.find 查询单条数据",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testOneFind(): void
    {
        $connect = $this->createDatabaseConnectMock();
        $sql = <<<'eot'
            [
                "SELECT `test`.* FROM `test` LIMIT 1",
                [],
                false
            ]
            eot;

        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect->table('test')
                    ->one()
                    ->find(),
                $connect
            )
        );
    }

    public function testOneFlow(): void
    {
        $condition = false;
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

                    ->if($condition)
                    ->one()
                    ->else()
                    ->all()
                    ->fi()
                    ->find(),
                $connect
            )
        );
    }

    public function testOneFlow2(): void
    {
        $condition = true;
        $connect = $this->createDatabaseConnectMock();
        $sql = <<<'eot'
            [
                "SELECT `test`.* FROM `test` LIMIT 1",
                [],
                false
            ]
            eot;

        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect
                    ->table('test')

                    ->if($condition)
                    ->one()
                    ->else()
                    ->all()
                    ->fi()
                    ->find(),
                $connect
            )
        );
    }
}
