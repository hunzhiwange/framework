<?php

declare(strict_types=1);

namespace Tests\Database\Read;

use Tests\Database\DatabaseTestCase as TestCase;

/**
 * @api(
 *     zh-CN:title="查询多条数据.findAll",
 *     path="database/read/findall",
 *     zh-CN:description="",
 * )
 *
 * @internal
 *
 * @coversNothing
 */
final class FindAllTest extends TestCase
{
    /**
     * @api(
     *     zh-CN:title="findAll 查询多条数据",
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
                    ->findAll()
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="all.find 查询多条数据",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testAllFind(): void
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
                    ->all()
                    ->find()
            )
        );
    }

    public function testFromOneToAll(): void
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
                    ->one()
                    ->findAll(true)
            )
        );
    }
}
