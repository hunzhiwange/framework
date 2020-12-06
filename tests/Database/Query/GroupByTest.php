<?php

declare(strict_types=1);

namespace Tests\Database\Query;

use Leevel\Database\Condition;
use Tests\Database\DatabaseTestCase as TestCase;

/**
 * @api(
 *     title="Query lang.groupBy",
 *     zh-CN:title="查询语言.groupBy",
 *     path="database/query/groupby",
 *     zh-CN:description="
 * ## groupBy 函数原型
 *
 * ``` php
 * public function groupBy($expression);
 * ```
 *
 *  - 参数支持字符串以及它们构成的一维数组，用法和 《查询语言.orderBy》 非常相似。
 * ",
 * )
 */
class GroupByTest extends TestCase
{
    /**
     * @api(
     *     zh-CN:title="groupBy 基础用法",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testBaseUse(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.`tid` AS `id`,`test_query`.`tname` AS `value` FROM `test_query` GROUP BY `test_query`.`id`,`test_query`.`name`",
                [],
                false
            ]
            eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->table('test_query', 'tid as id,tname as value')
                    ->groupBy('id')
                    ->groupBy('name')
                    ->findAll(true)
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="groupBy 字段指定表名",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testWithTable(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.`tid` AS `id`,`test_query`.`tname` AS `value` FROM `test_query` GROUP BY `test_query`.`id`",
                [],
                false
            ]
            eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->table('test_query', 'tid as id,tname as value')
                    ->groupBy('test_query.id')
                    ->findAll(true),
                1
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="groupBy 字段表达式",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testWithExpression(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.`tid` AS `id`,`test_query`.`tname` AS `value` FROM `test_query` GROUP BY `test_query`.`num` HAVING SUM(`test_query`.`num`) > :SUM_test_query_num",
                {
                    "SUM_test_query_num": [
                        9
                    ]
                },
                false
            ]
            eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->table('test_query', 'tid as id,tname as value')
                    ->groupBy(Condition::raw('[num]'))
                    ->having(Condition::raw('SUM([num])'), '>', 9)
                    ->findAll(true),
                2
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="groupBy 复合型",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testWithComposite(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.`tid` AS `id`,`test_query`.`tname` AS `value` FROM `test_query` GROUP BY `test_query`.`title`,`test_query`.`id`,concat('1234',`test_query`.`id`,'ttt')",
                [],
                false
            ]
            eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->table('test_query', 'tid as id,tname as value')
                    ->groupBy('title,id,'.Condition::raw("concat('1234',[id],'ttt')"))
                    ->findAll(true),
                3
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="groupBy 字段数组支持",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testWithArray(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.`tid` AS `id`,`test_query`.`tname` AS `value` FROM `test_query` GROUP BY `test_query`.`title`,`test_query`.`id`,`test_query`.`ttt`,`test_query`.`value`",
                [],
                false
            ]
            eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->table('test_query', 'tid as id,tname as value')
                    ->groupBy(['title,id,ttt', 'value'])
                    ->findAll(true),
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
                "SELECT `test_query`.* FROM `test_query` GROUP BY `test_query`.`name`",
                [],
                false
            ]
            eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->table('test_query')
                    ->if($condition)
                    ->groupBy('id')
                    ->else()
                    ->groupBy('name')
                    ->fi()
                    ->findAll(true)
            )
        );
    }

    public function testGroupByFlow2(): void
    {
        $condition = true;

        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` GROUP BY `test_query`.`id`",
                [],
                false
            ]
            eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->table('test_query')
                    ->if($condition)
                    ->groupBy('id')
                    ->else()
                    ->groupBy('name')
                    ->fi()
                    ->findAll(true)
            )
        );
    }
}
