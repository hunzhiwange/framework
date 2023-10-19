<?php

declare(strict_types=1);

namespace Tests\Database\Query;

use Leevel\Database\Condition;
use Tests\Database\DatabaseTestCase as TestCase;

#[Api([
    'title' => 'Query lang.groupBy',
    'zh-CN:title' => '查询语言.groupBy',
    'path' => 'database/query/groupby',
    'zh-CN:description' => <<<'EOT'
## groupBy 函数原型

``` php
public function groupBy($expression);
```

 - 参数支持字符串以及它们构成的一维数组，用法和 《查询语言.orderBy》 非常相似。
EOT,
])]
final class GroupByTest extends TestCase
{
    #[Api([
        'zh-CN:title' => 'groupBy 基础用法',
    ])]
    public function testBaseUse(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.`name`,`test_query`.`id`,SUM(tid) as t FROM `test_query` GROUP BY `test_query`.`id`,`test_query`.`name`",
                [],
                false
            ]
            eot;

        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect
                    ->table('test_query', 'name,id')
                    ->columns(Condition::raw('SUM(tid) as t'))
                    ->groupBy('id')
                    ->groupBy('name')
                    ->findAll(),
                $connect
            )
        );
    }

    public function testBaseUse2(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.`id`,`test_query`.`name`,SUM(test_query.tid) as t2 FROM `test_query` GROUP BY `test_query`.`id`,`test_query`.`name`",
                [],
                false
            ]
            eot;

        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect
                    ->table('test_query', 'id,name')
                    ->columns(Condition::raw('SUM(test_query.tid) as t2'))
                    ->groupBy('id')
                    ->groupBy('name')
                    ->findAll(),
                $connect
            )
        );
    }

    #[Api([
        'zh-CN:title' => 'groupBy 字段指定表名',
    ])]
    public function testWithTable(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.`id` FROM `test_query` GROUP BY `test_query`.`id`",
                [],
                false
            ]
            eot;

        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect
                    ->table('test_query', 'id')
                    ->groupBy('test_query.id')
                    ->findAll(),
                $connect,
                1
            )
        );
    }

    #[Api([
        'zh-CN:title' => 'groupBy 字段表达式',
    ])]
    public function testWithExpression(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.`num` FROM `test_query` GROUP BY `test_query`.`num` HAVING SUM(`test_query`.`num`) > :SUM_test_query_num",
                {
                    "SUM_test_query_num": [
                        9
                    ]
                },
                false
            ]
            eot;

        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect
                    ->table('test_query', 'num')
                    ->groupBy(Condition::raw('[num]'))
                    ->having(Condition::raw('SUM([num])'), '>', 9)
                    ->findAll(),
                $connect,
                2
            )
        );
    }

    #[Api([
        'zh-CN:title' => 'groupBy 复合型',
    ])]
    public function testWithComposite(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.`title`,`test_query`.`id` FROM `test_query` GROUP BY `test_query`.`title`,`test_query`.`id`,concat('1234',`test_query`.`id`,'ttt')",
                [],
                false
            ]
            eot;

        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect
                    ->table('test_query', 'title,id')
                    ->groupBy('title,id,'.Condition::raw("concat('1234',[id],'ttt')"))
                    ->findAll(),
                $connect,
                3
            )
        );
    }

    #[Api([
        'zh-CN:title' => 'groupBy 字段数组支持',
    ])]
    public function testWithArray(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.`title`,`test_query`.`id`,`test_query`.`ttt`,`test_query`.`value` FROM `test_query` GROUP BY `test_query`.`title`,`test_query`.`id`,`test_query`.`ttt`,`test_query`.`value`",
                [],
                false
            ]
            eot;

        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect
                    ->table('test_query', 'title,id,ttt,value')
                    ->groupBy(['title,id,ttt', 'value'])
                    ->findAll(),
                $connect,
                4
            )
        );
    }

    public function testGroupByFlow(): void
    {
        $condition = false;
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.`name` FROM `test_query` GROUP BY `test_query`.`name`",
                [],
                false
            ]
            eot;

        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect
                    ->table('test_query')
                    ->setColumns('name')
                    ->if($condition)
                    ->groupBy('id')
                    ->else()
                    ->groupBy('name')
                    ->fi()
                    ->findAll(),
                $connect
            )
        );
    }

    public function testGroupByFlow2(): void
    {
        $condition = true;

        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.`id` FROM `test_query` GROUP BY `test_query`.`id`",
                [],
                false
            ]
            eot;

        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect
                    ->table('test_query')
                    ->setColumns('id')
                    ->if($condition)
                    ->groupBy('id')
                    ->else()
                    ->groupBy('name')
                    ->fi()
                    ->findAll(),
                $connect
            )
        );
    }
}
