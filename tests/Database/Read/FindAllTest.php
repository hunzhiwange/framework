<?php

declare(strict_types=1);

namespace Tests\Database\Read;

use Tests\Database\DatabaseTestCase as TestCase;

#[Api([
    'zh-CN:title' => '查询多条数据.findAll',
    'path' => 'database/read/findall',
])]
/**
 * @internal
 */
final class FindAllTest extends TestCase
{
    #[Api([
        'zh-CN:title' => 'findAll 查询多条数据',
    ])]
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
                $connect->table('test')
                    ->findAll(),
                $connect
            )
        );
    }

    #[Api([
        'zh-CN:title' => 'findArray 以数组返回所有记录',
    ])]
    public function testFindArray(): void
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
                    ->findArray(),
                $connect
            )
        );
    }

    #[Api([
        'zh-CN:title' => 'findCollection 以集合返回所有记录',
    ])]
    public function testFindCollection(): void
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
                    ->findCollection(),
                $connect
            )
        );
    }

    #[Api([
        'zh-CN:title' => 'all.find 查询多条数据',
    ])]
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
                $connect->table('test')
                    ->all()
                    ->find(),
                $connect
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
                    ->findAll(),
                $connect
            )
        );
    }
}
