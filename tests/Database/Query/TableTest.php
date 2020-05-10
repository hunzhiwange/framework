<?php

declare(strict_types=1);

/*
 * This file is part of the ************************ package.
 * _____________                           _______________
 *  ______/     \__  _____  ____  ______  / /_  _________
 *   ____/ __   / / / / _ \/ __`\/ / __ \/ __ \/ __ \___
 *    __/ / /  / /_/ /  __/ /  \  / /_/ / / / / /_/ /__
 *      \_\ \_/\____/\___/_/   / / .___/_/ /_/ .___/
 *         \_\                /_/_/         /_/
 *
 * The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
 * (c) 2010-2020 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Database\Query;

use Leevel\Database\Condition;
use stdClass;
use Tests\Database\DatabaseTestCase as TestCase;

/**
 * @api(
 *     title="Query lang.table",
 *     zh-CN:title="查询语言.table",
 *     path="database/query/table",
 *     description="",
 * )
 */
class TableTest extends TestCase
{
    /**
     * @api(
     *     zh-CN:title="Table 查询数据库表",
     *     description="",
     *     note="",
     * )
     */
    public function testBaseUse(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query`",
                [],
                false
            ]
            eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->table('test_query')
                    ->findAll(true)
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="Table 查询指定数据库的表",
     *     description="",
     *     note="",
     * )
     */
    public function testWithDatabaseName(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test`.`test_query`",
                [],
                false
            ]
            eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->table('test.test_query')
                    ->findAll(true),
                1
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="Table 查询数据库表，表支持别名",
     *     description="",
     *     note="",
     * )
     */
    public function testWithAlias(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `p`.* FROM `test`.`test_query` `p`",
                [],
                false
            ]
            eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->table(['p' => 'test.test_query'])
                    ->findAll(true),
                2
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="Table 查询数据库表指定字段",
     *     description="",
     *     note="",
     * )
     */
    public function testField(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.`title`,`test_query`.`body` FROM `test_query`",
                [],
                false
            ]
            eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->table('test_query', 'title,body')
                    ->findAll(true)
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="Table 查询数据库表指定字段，字段支持别名",
     *     description="",
     *     note="",
     * )
     */
    public function testWithFieldAlias(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.`title` AS `t`,`test_query`.`name`,`test_query`.`remark`,`test_query`.`value` FROM `test`.`test_query`",
                [],
                false
            ]
            eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->table('test.test_query', [
                        't' => 'title', 'name', 'remark,value',
                    ])
                    ->findAll(true),
                1
            )
        );
    }

    public function testTableFlow(): void
    {
        $condition = false;
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query_subsql`.* FROM `test_query_subsql`",
                [],
                false
            ]
            eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->if($condition)
                    ->table('test_query')
                    ->else()
                    ->table('test_query_subsql')
                    ->fi()
                    ->findAll(true)
            )
        );
    }

    public function testTableFlow2(): void
    {
        $condition = true;
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query`",
                [],
                false
            ]
            eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->if($condition)
                    ->table('test_query')
                    ->else()
                    ->table('test_query_subsql')
                    ->fi()
                    ->findAll(true)
            )
        );
    }

    public function testTableIsInvalid(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
           'Invalid table name.'
        );

        $connect = $this->createDatabaseConnectMock();

        $connect
            ->table(new stdClass())
            ->findAll(true);
    }

    /**
     * @api(
     *     zh-CN:title="Table 查询数据库表支持子查询",
     *     description="",
     *     note="",
     * )
     */
    public function testSub(): void
    {
        $connect = $this->createDatabaseConnectMock();
        $subSql = $connect->table('test_query')->makeSql(true);

        $sql = <<<'eot'
            [
                "SELECT `a`.* FROM (SELECT `test_query`.* FROM `test_query`) a",
                [],
                false
            ]
            eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->table($subSql.' as a')
                    ->findAll(true)
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="Table 查询数据库表支持子查询,子查询可以为数据库查询对象",
     *     description="",
     *     note="",
     * )
     */
    public function testSubIsSelect(): void
    {
        $connect = $this->createDatabaseConnectMock();
        $subSql = $connect->table('test_query');

        $sql = <<<'eot'
            [
                "SELECT `bb`.* FROM (SELECT `test_query`.* FROM `test_query`) bb",
                [],
                false
            ]
            eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->table(['bb' => $subSql])
                    ->findAll(true)
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="Table 查询数据库表支持子查询,子查询可以为数据库条件对象",
     *     description="",
     *     note="",
     * )
     */
    public function testSubIsCondition(): void
    {
        $connect = $this->createDatabaseConnectMock();
        $subSql = $connect->table('test_query')->databaseCondition();

        $sql = <<<'eot'
            [
                "SELECT `bb`.* FROM (SELECT `test_query`.* FROM `test_query`) bb",
                [],
                false
            ]
            eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->table(['bb' => $subSql])
                    ->findAll(true)
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="Table 查询数据库表支持子查询,子查询可以为闭包",
     *     description="",
     *     note="",
     * )
     */
    public function testSubIsClosure(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `b`.* FROM (SELECT `test_query`.* FROM `test_query`) b",
                [],
                false
            ]
            eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->table(['b'=> function ($select) {
                        $select->table('test_query');
                    }])
                    ->findAll(true)
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="Table 查询数据库表支持子查询,子查询可以为闭包,未指定别名默认为自身",
     *     description="",
     *     note="",
     * )
     */
    public function testSubIsClosureWithItSeltAsAlias(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `guest_book`.* FROM (SELECT `guest_book`.* FROM `guest_book`) guest_book",
                [],
                false
            ]
            eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->table(function ($select) {
                        $select->table('guest_book');
                    })
                    ->findAll(true)
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="Table 查询数据库表支持子查询,子查询可以为闭包,还可以进行 join 查询",
     *     description="",
     *     note="",
     * )
     */
    public function testSubIsClosureWithJoin(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.`remark`,`test_query_subsql`.`name`,`test_query_subsql`.`value` FROM (SELECT `test_query`.* FROM `test_query`) test_query INNER JOIN `test_query_subsql` ON `test_query_subsql`.`name` = `test_query`.`name`",
                [],
                false
            ]
            eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->table(function ($select) {
                        $select->table('test_query');
                    }, 'remark')
                    ->join('test_query_subsql', 'name,value', 'name', '=', Condition::raw('[test_query.name]'))
                    ->findAll(true)
            )
        );
    }
}
