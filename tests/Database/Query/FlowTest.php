<?php

declare(strict_types=1);

namespace Tests\Database\Query;

use Tests\Database\DatabaseTestCase as TestCase;

/**
 * @api(
 *     title="Query lang.flow",
 *     zh-CN:title="查询语言.flow",
 *     path="database/query/flow",
 *     zh-CN:description="QueryPHP 数据构造器支持条件运算符，可以根据不同条件做不同的事情，支持所有的构造器函数，即返回 `$this`。",
 * )
 */
class FlowTest extends TestCase
{
    /**
     * @api(
     *     zh-CN:title="limit 限制条数",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testBaseUse(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`id` = :test_query_id ORDER BY `test_query`.`name` DESC LIMIT 1",
                {
                    "test_query_id": [
                        2
                    ]
                },
                false
            ]
            eot;

        $id = 2;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->table('test_query')
                    ->if(1 === $id)
                    ->where('id', 1)
                    ->elif(2 === $id)
                    ->where('id', 2)
                    ->orderBy('name DESC')
                    ->elif(3 === $id)
                    ->where('id', 3)
                    ->where('id', 1111)
                    ->elif(4 === $id)
                    ->where('id', 4)
                    ->fi()
                    ->findOne(true)
            )
        );

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`id` = :test_query_id LIMIT 1",
                {
                    "test_query_id": [
                        1
                    ]
                },
                false
            ]
            eot;

        $id = 1;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->table('test_query')
                    ->if(1 === $id)
                    ->where('id', 1)
                    ->elif(2 === $id)
                    ->where('id', 2)
                    ->orderBy('name DESC')
                    ->elif(3 === $id)
                    ->where('id', 3)
                    ->where('id', 1111)
                    ->elif(4 === $id)
                    ->where('id', 4)
                    ->fi()
                    ->findOne(true),
                1
            )
        );

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`id` = :test_query_id AND `test_query`.`id` = :test_query_id_1 LIMIT 1",
                {
                    "test_query_id": [
                        3
                    ],
                    "test_query_id_1": [
                        1111
                    ]
                },
                false
            ]
            eot;

        $id = 3;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->table('test_query')
                    ->if(1 === $id)
                    ->where('id', 1)
                    ->elif(2 === $id)
                    ->where('id', 2)
                    ->orderBy('name DESC')
                    ->elif(3 === $id)
                    ->where('id', 3)
                    ->where('id', 1111)
                    ->elif(4 === $id)
                    ->where('id', 4)
                    ->fi()
                    ->findOne(true),
                2
            )
        );

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`id` = :test_query_id LIMIT 1",
                {
                    "test_query_id": [
                        4
                    ]
                },
                false
            ]
            eot;

        $id = 4;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->table('test_query')
                    ->if(1 === $id)
                    ->where('id', 1)
                    ->elif(2 === $id)
                    ->where('id', 2)
                    ->orderBy('name DESC')
                    ->elif(3 === $id)
                    ->where('id', 3)
                    ->where('id', 1111)
                    ->elif(4 === $id)
                    ->where('id', 4)
                    ->fi()
                    ->findOne(true),
                3
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="else 浅记忆",
     *     zh-CN:description="else 仅仅能记忆上一次 if,elif 的结果，上一次的反向结果就是 else 的条件值，我们建议不要在 SQL 链式中使用过度的条件判断。",
     *     zh-CN:note="命令遵循 shell 命令风格，即 if,elif,else,fi。",
     * )
     */
    public function testElse(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`id` = :test_query_id AND `test_query`.`id` = :test_query_id_1 ORDER BY `test_query`.`name` DESC LIMIT 1",
                {
                    "test_query_id": [
                        2
                    ],
                    "test_query_id_1": [
                        4
                    ]
                },
                false
            ]
            eot;

        $id = 2;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->table('test_query')
                    ->if(1 === $id)
                    ->where('id', 1)
                    ->elif(2 === $id)
                    ->where('id', 2)
                    ->orderBy('name DESC')
                    ->elif(3 === $id)
                    ->where('id', 3)
                    ->where('id', 1111)
                    ->else() // else 仅仅能记忆上一次 if,elif 的结果，上一次的反向结果就是 else 的条件值,其等价于 elif($id != 3)
                    ->where('id', 4)
                    ->fi()
                    ->findOne(true)
            )
        );

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`id` = :test_query_id AND `test_query`.`id` = :test_query_id_1 LIMIT 1",
                {
                    "test_query_id": [
                        3
                    ],
                    "test_query_id_1": [
                        1111
                    ]
                },
                false
            ]
            eot;

        $id = 3;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->table('test_query')
                    ->if(1 === $id)
                    ->where('id', 1)
                    ->elif(2 === $id)
                    ->where('id', 2)
                    ->orderBy('name DESC')
                    ->elif(3 === $id)
                    ->where('id', 3)
                    ->where('id', 1111)
                    ->else() // else 仅仅能记忆上一次 if,elif 的结果，上一次的反向结果就是 else 的条件值,其等价于 elif($id != 3)
                    ->where('id', 4)
                    ->fi()
                    ->findOne(true),
                1
            )
        );

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`id` = :test_query_id LIMIT 1",
                {
                    "test_query_id": [
                        4
                    ]
                },
                false
            ]
            eot;

        $id = 5;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->table('test_query')
                    ->if(1 === $id)
                    ->where('id', 1)
                    ->elif(2 === $id)
                    ->where('id', 2)
                    ->orderBy('name DESC')
                    ->elif(3 === $id)
                    ->where('id', 3)
                    ->where('id', 1111)
                    ->else() // else 仅仅能记忆上一次 if,elif 的结果，上一次的反向结果就是 else 的条件值,其等价于 elif($id != 3)
                    ->where('id', 4)
                    ->fi()
                    ->findOne(true),
                2
            )
        );
    }
}
