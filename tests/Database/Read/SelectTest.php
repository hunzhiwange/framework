<?php

declare(strict_types=1);

namespace Tests\Database\Read;

use Tests\Database\DatabaseTestCase as TestCase;

#[Api([
    'zh-CN:title' => '查询数据.select',
    'path' => 'database/read/select',
])]
final class SelectTest extends TestCase
{
    #[Api([
        'zh-CN:title' => 'select 查询指定 SQL',
    ])]
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
            $this->varJsonSql(
                $connect
                    ->table('test')
                    ->select('select *from test where id = ?', [1]),
                $connect
            )
        );
    }

    #[Api([
        'zh-CN:title' => 'select 直接查询',
    ])]
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
            $this->varJsonSql(
                $connect->table('test')
                    ->select(),
                $connect,
                1
            )
        );
    }

    #[Api([
        'zh-CN:title' => 'select 查询支持闭包',
    ])]
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
            $this->varJsonSql(
                $connect->table('test')
                    ->select(function ($select): void {
                        $select->where('id', 1);
                    }),
                $connect,
                2
            )
        );
    }

    #[Api([
        'zh-CN:title' => 'select 查询支持 \Leevel\Database\Select 对象',
    ])]
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
            $this->varJsonSql(
                $connect->select($select),
                $connect,
                3
            )
        );
    }
}
