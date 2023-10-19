<?php

declare(strict_types=1);

namespace Tests\Database\Query;

use Leevel\Database\Condition;
use Leevel\Kernel\Utils\Api;
use Tests\Database\DatabaseTestCase as TestCase;

#[Api([
    'title' => 'Query lang.bind',
    'zh-CN:title' => '查询语言.bind',
    'path' => 'database/query/bind',
])]
final class BindTest extends TestCase
{
    #[Api([
        'zh-CN:title' => '命名参数绑定',
    ])]
    public function testBaseUse(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`id` = :id",
                {
                    "id": [
                        1
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
                    ->bind('id', 1)
                    ->where('id', '=', Condition::raw(':id'))
                    ->findAll(),
                $connect
            )
        );
    }

    #[Api([
        'zh-CN:title' => '命名参数绑定，支持绑定类型',
    ])]
    public function testBindWithType(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`id` = :id",
                {
                    "id": [
                        1,
                        "PDO::PARAM_INT"
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
                    ->bind('id', 1, \PDO::PARAM_INT)
                    ->where('id', '=', Condition::raw(':id'))
                    ->findAll(),
                $connect,
                1
            )
        );
    }

    #[Api([
        'zh-CN:title' => '命名参数绑定，绑定值支持类型定义',
    ])]
    public function testWithTypeAndValueCanBeArray(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`id` = :id",
                {
                    "id": [
                        1,
                        "PDO::PARAM_INT"
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
                    ->bind('id', [1, \PDO::PARAM_INT])
                    ->where('id', '=', Condition::raw(':id'))
                    ->findAll(),
                $connect,
                2
            )
        );
    }

    #[Api([
        'zh-CN:title' => '命名参数绑定，支持多个字段绑定',
    ])]
    public function testNameBind(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`id` = :id AND `test_query`.`hello` LIKE :name",
                {
                    "id": [
                        1,
                        "PDO::PARAM_INT"
                    ],
                    "name": [
                        "小鸭子"
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
                    ->bind(['id' => [1, \PDO::PARAM_INT], 'name' => '小鸭子'])
                    ->where('id', '=', Condition::raw(':id'))
                    ->where('hello', 'like', Condition::raw(':name'))
                    ->findAll(),
                $connect,
                3
            )
        );
    }

    #[Api([
        'zh-CN:title' => '问号 `?` 参数绑定，支持多个字段绑定',
    ])]
    public function testQuestionMarkBind(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`id` = ? AND `test_query`.`hello` LIKE ?",
                [
                    [
                        5,
                        "PDO::PARAM_INT"
                    ],
                    [
                        "小鸭子"
                    ]
                ],
                false
            ]
            eot;

        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect
                    ->table('test_query')
                    ->bind([[5, \PDO::PARAM_INT], '小鸭子'])
                    ->where('id', '=', Condition::raw('?'))
                    ->where('hello', 'like', Condition::raw('?'))
                    ->findAll(),
                $connect,
                4
            )
        );
    }

    public function testBindFlow(): void
    {
        $condition = false;

        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`name` = :name",
                {
                    "name": [
                        1
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
                    ->bind('id', 1)
                    ->where('id', '=', Condition::raw(':id'))
                    ->else()
                    ->bind('name', 1)
                    ->where('name', '=', Condition::raw(':name'))
                    ->fi()
                    ->findAll(),
                $connect
            )
        );
    }

    public function testBindFlow2(): void
    {
        $condition = true;

        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`id` = :id",
                {
                    "id": [
                        1
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
                    ->bind('id', 1)
                    ->where('id', '=', Condition::raw(':id'))
                    ->else()
                    ->bind('name', 1)
                    ->where('name', '=', Condition::raw(':name'))
                    ->fi()
                    ->findAll(),
                $connect
            )
        );
    }
}
