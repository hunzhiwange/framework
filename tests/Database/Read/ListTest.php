<?php

declare(strict_types=1);

namespace Tests\Database\Read;

use Leevel\Kernel\Utils\Api;
use Tests\Database\DatabaseTestCase as TestCase;

#[Api([
    'zh-CN:title' => '查询一列数据.list',
    'path' => 'database/read/list',
])]
final class ListTest extends TestCase
{
    #[Api([
        'zh-CN:title' => 'list 查询基础用法',
    ])]
    public function testBaseUse(): void
    {
        $connect = $this->createDatabaseConnectMock();
        $sql = <<<'eot'
            [
                "SELECT `test`.`name` FROM `test`",
                [],
                false
            ]
            eot;

        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect->table('test')
                    ->list('name'),
                $connect
            )
        );
    }

    #[Api([
        'zh-CN:title' => 'list 查询字段逗号分隔',
    ])]
    public function testStringByCommaSeparation(): void
    {
        $connect = $this->createDatabaseConnectMock();
        $sql = <<<'eot'
            [
                "SELECT `test`.`name`,`test`.`id` FROM `test`",
                [],
                false
            ]
            eot;

        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect->table('test')
                    ->list('name,id'),
                $connect,
                1
            )
        );
    }

    #[Api([
        'zh-CN:title' => 'list 查询字段多个字符串',
    ])]
    public function testMoreString(): void
    {
        $connect = $this->createDatabaseConnectMock();
        $sql = <<<'eot'
            [
                "SELECT `test`.`name`,`test`.`id` FROM `test`",
                [],
                false
            ]
            eot;

        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect->table('test')
                    ->list('name', 'id'),
                $connect,
                2
            )
        );
    }

    #[Api([
        'zh-CN:title' => 'list 查询字段数组',
    ])]
    public function testArray(): void
    {
        $connect = $this->createDatabaseConnectMock();
        $sql = <<<'eot'
            [
                "SELECT `test`.`name`,`test`.`id` FROM `test`",
                [],
                false
            ]
            eot;

        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect->table('test')
                    ->list(['name', 'id']),
                $connect,
                3
            )
        );
    }

    #[Api([
        'zh-CN:title' => 'list 查询字段数组和字符串混合',
    ])]
    public function testArrayAndString(): void
    {
        $connect = $this->createDatabaseConnectMock();
        $sql = <<<'eot'
            [
                "SELECT `test`.`name`,`test`.`id` FROM `test`",
                [],
                false
            ]
            eot;

        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect->table('test')
                    ->list(['name'], 'id'),
                $connect,
                4
            )
        );
    }
}
