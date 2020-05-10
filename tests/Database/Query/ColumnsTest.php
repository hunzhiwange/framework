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
use Tests\Database\DatabaseTestCase as TestCase;

/**
 * @api(
 *     title="Query lang.columns",
 *     zh-CN:title="查询语言.columns",
 *     path="database/query/columns",
 *     description="",
 * )
 */
class ColumnsTest extends TestCase
{
    /**
     * @api(
     *     zh-CN:title="Columns 添加字段",
     *     zh-CN:description="字段条件用法和 table 中的字段用法一致，详情可以查看《查询语言.table》。",
     *     note="",
     * )
     */
    public function testBaseUse(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.*,`test_query`.`id`,`test_query`.`name`,`test_query`.`value` FROM `test_query`",
                [],
                false
            ]
            eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->table('test_query')
                    ->columns('id')
                    ->columns('name,value')
                    ->findAll(true)
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="SetColumns 设置字段",
     *     description="清空原有字段，然后添加新的字段。",
     *     note="",
     * )
     */
    public function testSetColumns(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.`remark` FROM `test_query`",
                [],
                false
            ]
            eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->table('test_query')
                    ->columns('id')
                    ->columns('name,value')
                    ->setColumns('remark')
                    ->findAll(true)
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="Columns 字段支持表达式",
     *     description="",
     *     note="",
     * )
     */
    public function testColumnsExpressionForSelectString(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                [
                    "SELECT 'foo'",
                    [],
                    false
                ]
            ]
            eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                [
                    $connect
                        ->columns("{'foo'}")
                        ->findAll(true),
                ]
            )
        );
    }

    public function testColumnsFlow(): void
    {
        $condition = false;

        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.*,`test_query`.`name`,`test_query`.`value` FROM `test_query`",
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
                    ->columns('id')
                    ->else()
                    ->columns('name,value')
                    ->fi()
                    ->findAll(true)
            )
        );
    }

    public function testColumnsFlow2(): void
    {
        $condition = true;

        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.*,`test_query`.`id` FROM `test_query`",
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
                    ->columns('id')
                    ->else()
                    ->columns('name,value')
                    ->fi()
                    ->findAll(true)
            )
        );
    }

    public function testSetColumnsFlow(): void
    {
        $condition = false;

        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.`name`,`test_query`.`value` FROM `test_query`",
                [],
                false
            ]
            eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->table('test_query')
                    ->setColumns('foo')
                    ->if($condition)
                    ->setColumns('id')
                    ->else()
                    ->setColumns('name,value')
                    ->fi()
                    ->findAll(true)
            )
        );
    }

    public function testSetColumnsFlow2(): void
    {
        $condition = true;

        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.`id` FROM `test_query`",
                [],
                false
            ]
            eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->table('test_query')
                    ->setColumns('foo')
                    ->if($condition)
                    ->setColumns('id')
                    ->else()
                    ->setColumns('name,value')
                    ->fi()
                    ->findAll(true)
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="Columns 字段在连表中的查询",
     *     description="",
     *     note="",
     * )
     */
    public function testSetColumnsWithTableName(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.`name`,`test_query`.`value`,`test_query_subsql`.`name`,`test_query_subsql`.`value` FROM `test_query` INNER JOIN `test_query_subsql` ON `test_query_subsql`.`name` = `test_query`.`name`",
                [],
                false
            ]
            eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->table('test_query')
                    ->setColumns('test_query.name,test_query.value')
                    ->join('test_query_subsql', 'name,value', 'name', '=', Condition::raw('[test_query.name]'))
                    ->findAll(true)
            )
        );
    }
}
