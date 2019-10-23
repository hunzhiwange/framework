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
 * (c) 2010-2019 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Database\Query;

use stdClass;
use Tests\Database\DatabaseTestCase as TestCase;

/**
 * table test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.06.10
 *
 * @version 1.0
 *
 * @api(
 *     title="查询语言.table",
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
                "SELECT `posts`.* FROM `posts`",
                [],
                false,
                null,
                null,
                []
            ]
            eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->table('posts')
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
                "SELECT `posts`.* FROM `mydb`.`posts`",
                [],
                false,
                null,
                null,
                []
            ]
            eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->table('mydb.posts')
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
                "SELECT `p`.* FROM `mydb`.`posts` `p`",
                [],
                false,
                null,
                null,
                []
            ]
            eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->table(['p' => 'mydb.posts'])
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
                "SELECT `posts`.`title`,`posts`.`body` FROM `posts`",
                [],
                false,
                null,
                null,
                []
            ]
            eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->table('posts', 'title,body')
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
                "SELECT `posts`.`title` AS `t`,`posts`.`name`,`posts`.`remark`,`posts`.`value` FROM `mydb`.`posts`",
                [],
                false,
                null,
                null,
                []
            ]
            eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->table('mydb.posts', [
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
                "SELECT `foo`.* FROM `foo`",
                [],
                false,
                null,
                null,
                []
            ]
            eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->if($condition)
                    ->table('test')
                    ->else()
                    ->table('foo')
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
                "SELECT `test`.* FROM `test`",
                [],
                false,
                null,
                null,
                []
            ]
            eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->if($condition)
                    ->table('test')
                    ->else()
                    ->table('foo')
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
        $subSql = $connect->table('test')->makeSql(true);

        $sql = <<<'eot'
            [
                "SELECT `a`.* FROM (SELECT `test`.* FROM `test`) a",
                [],
                false,
                null,
                null,
                []
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
        $subSql = $connect->table('test');

        $sql = <<<'eot'
            [
                "SELECT `bb`.* FROM (SELECT `test`.* FROM `test`) bb",
                [],
                false,
                null,
                null,
                []
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
        $subSql = $connect->table('test')->databaseCondition();

        $sql = <<<'eot'
            [
                "SELECT `bb`.* FROM (SELECT `test`.* FROM `test`) bb",
                [],
                false,
                null,
                null,
                []
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
                "SELECT `b`.* FROM (SELECT `world`.* FROM `world`) b",
                [],
                false,
                null,
                null,
                []
            ]
            eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->table(['b'=> function ($select) {
                        $select->table('world');
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
                false,
                null,
                null,
                []
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
                "SELECT `world`.`remark`,`hello`.`name`,`hello`.`value` FROM (SELECT `world`.* FROM `world`) world INNER JOIN `hello` ON `hello`.`name` = `world`.`name`",
                [],
                false,
                null,
                null,
                []
            ]
            eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->table(function ($select) {
                        $select->table('world');
                    }, 'remark')
                    ->join('hello', 'name,value', 'name', '=', '{[world.name]}')
                    ->findAll(true)
            )
        );
    }
}
