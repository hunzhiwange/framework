<?php

declare(strict_types=1);

namespace Tests\Database\Read;

use Tests\Database\DatabaseTestCase as TestCase;

/**
 * @api(
 *     zh-CN:title="查询数据.select",
 *     path="database/read/select",
 *     zh-CN:description="",
 * )
 *
 * @internal
 *
 * @coversNothing
 */
final class SelectTest extends TestCase
{
    /**
     * @api(
     *     zh-CN:title="select 查询指定 SQL",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testBaseUse(): void
    {
        $connect = $this->createDatabaseConnectMock();
        $sql = <<<'eot'
            [
                "select *from test where id = ?",
                [
                    1
                ],
                false
            ]
            eot;

        static::assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->sql()
                    ->table('test')
                    ->select('select *from test where id = ?', [1])
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="select 直接查询",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testSelect(): void
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
            $this->varJson(
                $connect
                    ->sql()
                    ->table('test')
                    ->select(),
                1
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="select 查询支持闭包",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testSelectClosure(): void
    {
        $connect = $this->createDatabaseConnectMock();
        $sql = <<<'eot'
            [
                "SELECT `test`.* FROM `test` WHERE `test`.`id` = :test_id",
                {
                    "test_id": [
                        1
                    ]
                },
                false
            ]
            eot;

        static::assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->sql()
                    ->table('test')
                    ->select(function ($select): void {
                        $select->where('id', 1);
                    }),
                2
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="select 查询支持 \Leevel\Database\Select 对象",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testSelectObject(): void
    {
        $connect = $this->createDatabaseConnectMock();
        $sql = <<<'eot'
            [
                "SELECT `test`.* FROM `test` WHERE `test`.`id` = :test_id",
                {
                    "test_id": [
                        5
                    ]
                },
                false
            ]
            eot;

        $select = $connect
            ->table('test')
            ->where('id', 5)
        ;

        static::assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->sql()
                    ->select($select),
                3
            )
        );
    }
}
