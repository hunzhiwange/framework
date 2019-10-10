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

use Tests\Database\DatabaseTestCase as TestCase;

/**
 * join test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.06.17
 *
 * @version 1.0
 */
class JoinTest extends TestCase
{
    public function testBaseUse(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test`.*,`hello`.`name`,`hello`.`value` FROM `test` INNER JOIN `hello` ON `hello`.`name` = '小牛'",
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
                    ->table('test')
                    ->join('hello', 'name,value', 'name', '=', '小牛')
                    ->findAll(true)
            )
        );

        $sql = <<<'eot'
            [
                "SELECT `test`.*,`t`.`name` AS `nikename`,`t`.`value` AS `tt` FROM `test` INNER JOIN `hello` `t` ON `t`.`name` = '小牛'",
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
                    ->table('test')
                    ->join(['t' => 'hello'], ['name as nikename', 'tt' => 'value'], 'name', '=', '小牛')
                    ->findAll(true),
                1
            )
        );

        $sql = <<<'eot'
            [
                "SELECT `test`.*,`hello`.`name`,`hello`.`value` FROM `test` INNER JOIN `hello` ON `hello`.`hello` = 'world' AND `hello`.`test` > `hello`.`name`",
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
                    ->table('test')
                    ->join('hello', 'name,value', ['hello' => 'world', ['test', '>', '{[name]}']])
                    ->findAll(true),
                2
            )
        );

        $sql = <<<'eot'
            [
                "SELECT `test`.*,`hello`.`name`,`hello`.`value` FROM `test` INNER JOIN `hello` ON (`hello`.`id` < 5 AND `hello`.`name` LIKE 'hello')",
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
                    ->table('test')
                    ->join('hello', 'name,value', function ($select) {
                        $select
                            ->where('id', '<', 5)
                            ->where('name', 'like', 'hello');
                    })
                    ->findAll(true),
                3
            )
        );
    }

    public function testInnerJoin(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test`.*,`t`.`name` AS `nikename`,`t`.`value` AS `tt` FROM `test` INNER JOIN `hello` `t` ON `t`.`name` = '小牛'",
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
                    ->table('test')
                    ->innerJoin(['t' => 'hello'], ['name as nikename', 'tt' => 'value'], 'name', '=', '小牛')
                    ->findAll(true)
            )
        );
    }

    public function testLeftJoin(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test`.*,`t`.`name` AS `nikename`,`t`.`value` AS `tt` FROM `test` LEFT JOIN `hello` `t` ON `t`.`name` = '小牛'",
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
                    ->table('test')
                    ->leftJoin(['t' => 'hello'], ['name as nikename', 'tt' => 'value'], 'name', '=', '小牛')
                    ->findAll(true)
            )
        );
    }

    public function testRightJoin(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test`.*,`t`.`name` AS `nikename`,`t`.`value` AS `tt` FROM `test` RIGHT JOIN `hello` `t` ON `t`.`name` = '小牛'",
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
                    ->table('test')
                    ->rightJoin(['t' => 'hello'], ['name as nikename', 'tt' => 'value'], 'name', '=', '小牛')
                    ->findAll(true)
            )
        );
    }

    public function testFullJoin(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test`.*,`t`.`name` AS `nikename`,`t`.`value` AS `tt` FROM `test` FULL JOIN `hello` `t` ON `t`.`name` = '小牛'",
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
                    ->table('test')
                    ->fullJoin(['t' => 'hello'], ['name as nikename', 'tt' => 'value'], 'name', '=', '小牛')
                    ->findAll(true)
            )
        );
    }

    public function testCrossJoin(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test`.*,`t`.`name` AS `nikename`,`t`.`value` AS `tt` FROM `test` CROSS JOIN `hello` `t`",
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
                    ->table('test')
                    ->crossJoin(['t' => 'hello'], ['name as nikename', 'tt' => 'value'])
                    ->findAll(true)
            )
        );
    }

    public function testNaturalJoin(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test`.*,`t`.`name` AS `nikename`,`t`.`value` AS `tt` FROM `test` NATURAL JOIN `hello` `t`",
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
                    ->table('test')
                    ->naturalJoin(['t' => 'hello'], ['name as nikename', 'tt' => 'value'])
                    ->findAll(true)
            )
        );
    }

    public function testJsonFlow(): void
    {
        $condition = false;
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test`.*,`hello`.`name`,`hello`.`value` FROM `test` INNER JOIN `hello` ON `hello`.`name` = '哥'",
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
                    ->table('test')
                    ->if($condition)
                    ->join('hello', 'name,value', 'name', '=', '小牛')
                    ->else()
                    ->join('hello', 'name,value', 'name', '=', '哥')
                    ->fi()
                    ->findAll(true)
            )
        );
    }

    public function testJsonFlow2(): void
    {
        $condition = true;
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test`.*,`hello`.`name`,`hello`.`value` FROM `test` INNER JOIN `hello` ON `hello`.`name` = '小牛'",
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
                    ->table('test')
                    ->if($condition)
                    ->join('hello', 'name,value', 'name', '=', '小牛')
                    ->else()
                    ->join('hello', 'name,value', 'name', '=', '哥')
                    ->fi()
                    ->findAll(true)
            )
        );
    }

    public function testInnerJsonFlow(): void
    {
        $condition = false;
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test`.*,`t`.`name` AS `nikename`,`t`.`value` AS `tt` FROM `test` INNER JOIN `hello` `t` ON `t`.`name` = '仔'",
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
                    ->table('test')
                    ->if($condition)
                    ->innerJoin(['t' => 'hello'], ['name as nikename', 'tt' => 'value'], 'name', '=', '小牛')
                    ->else()
                    ->innerJoin(['t' => 'hello'], ['name as nikename', 'tt' => 'value'], 'name', '=', '仔')
                    ->fi()
                    ->findAll(true)
            )
        );
    }

    public function testInnerJsonFlow2(): void
    {
        $condition = true;
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test`.*,`t`.`name` AS `nikename`,`t`.`value` AS `tt` FROM `test` INNER JOIN `hello` `t` ON `t`.`name` = '小牛'",
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
                    ->table('test')
                    ->if($condition)
                    ->innerJoin(['t' => 'hello'], ['name as nikename', 'tt' => 'value'], 'name', '=', '小牛')
                    ->else()
                    ->innerJoin(['t' => 'hello'], ['name as nikename', 'tt' => 'value'], 'name', '=', '仔')
                    ->fi()
                    ->findAll(true)
            )
        );
    }

    public function testLeftJsonFlow(): void
    {
        $condition = false;
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test`.*,`t`.`name` AS `nikename`,`t`.`value` AS `tt` FROM `test` LEFT JOIN `hello` `t` ON `t`.`name` = '仔'",
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
                    ->table('test')
                    ->if($condition)
                    ->leftJoin(['t' => 'hello'], ['name as nikename', 'tt' => 'value'], 'name', '=', '小牛')
                    ->else()
                    ->leftJoin(['t' => 'hello'], ['name as nikename', 'tt' => 'value'], 'name', '=', '仔')
                    ->fi()
                    ->findAll(true)
            )
        );
    }

    public function testLeftJsonFlow2(): void
    {
        $condition = true;
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test`.*,`t`.`name` AS `nikename`,`t`.`value` AS `tt` FROM `test` LEFT JOIN `hello` `t` ON `t`.`name` = '小牛'",
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
                    ->table('test')
                    ->if($condition)
                    ->leftJoin(['t' => 'hello'], ['name as nikename', 'tt' => 'value'], 'name', '=', '小牛')
                    ->else()
                    ->leftJoin(['t' => 'hello'], ['name as nikename', 'tt' => 'value'], 'name', '=', '仔')
                    ->fi()
                    ->findAll(true)
            )
        );
    }

    public function testRightJsonFlow(): void
    {
        $condition = false;
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test`.*,`t`.`name` AS `nikename`,`t`.`value` AS `tt` FROM `test` RIGHT JOIN `hello` `t` ON `t`.`name` = '仔'",
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
                    ->table('test')
                    ->if($condition)
                    ->rightJoin(['t' => 'hello'], ['name as nikename', 'tt' => 'value'], 'name', '=', '小牛')
                    ->else()
                    ->rightJoin(['t' => 'hello'], ['name as nikename', 'tt' => 'value'], 'name', '=', '仔')
                    ->fi()
                    ->findAll(true)
            )
        );
    }

    public function testRightJsonFlow2(): void
    {
        $condition = true;
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test`.*,`t`.`name` AS `nikename`,`t`.`value` AS `tt` FROM `test` RIGHT JOIN `hello` `t` ON `t`.`name` = '小牛'",
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
                    ->table('test')
                    ->if($condition)
                    ->rightJoin(['t' => 'hello'], ['name as nikename', 'tt' => 'value'], 'name', '=', '小牛')
                    ->else()
                    ->rightJoin(['t' => 'hello'], ['name as nikename', 'tt' => 'value'], 'name', '=', '仔')
                    ->fi()
                    ->findAll(true)
            )
        );
    }

    public function testFullJsonFlow(): void
    {
        $condition = false;
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test`.*,`t`.`name` AS `nikename`,`t`.`value` AS `tt` FROM `test` FULL JOIN `hello` `t` ON `t`.`name` = '仔'",
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
                    ->table('test')
                    ->if($condition)
                    ->fullJoin(['t' => 'hello'], ['name as nikename', 'tt' => 'value'], 'name', '=', '小牛')
                    ->else()
                    ->fullJoin(['t' => 'hello'], ['name as nikename', 'tt' => 'value'], 'name', '=', '仔')
                    ->fi()
                    ->findAll(true)
            )
        );
    }

    public function testFullJsonFlow2(): void
    {
        $condition = true;
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test`.*,`t`.`name` AS `nikename`,`t`.`value` AS `tt` FROM `test` FULL JOIN `hello` `t` ON `t`.`name` = '小牛'",
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
                    ->table('test')
                    ->if($condition)
                    ->fullJoin(['t' => 'hello'], ['name as nikename', 'tt' => 'value'], 'name', '=', '小牛')
                    ->else()
                    ->fullJoin(['t' => 'hello'], ['name as nikename', 'tt' => 'value'], 'name', '=', '仔')
                    ->fi()
                    ->findAll(true)
            )
        );
    }

    public function testCrossJsonFlow(): void
    {
        $condition = false;
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test`.*,`t`.`name` AS `nikename`,`t`.`value` AS `tt` FROM `test` CROSS JOIN `hello` `t` ON `t`.`name` = '仔'",
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
                    ->table('test')
                    ->if($condition)
                    ->crossJoin(['t' => 'hello'], ['name as nikename', 'tt' => 'value'], 'name', '=', '小牛')
                    ->else()
                    ->crossJoin(['t' => 'hello'], ['name as nikename', 'tt' => 'value'], 'name', '=', '仔')
                    ->fi()
                    ->findAll(true)
            )
        );
    }

    public function testCrossJsonFlow2(): void
    {
        $condition = true;
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test`.*,`t`.`name` AS `nikename`,`t`.`value` AS `tt` FROM `test` CROSS JOIN `hello` `t` ON `t`.`name` = '小牛'",
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
                    ->table('test')
                    ->if($condition)
                    ->crossJoin(['t' => 'hello'], ['name as nikename', 'tt' => 'value'], 'name', '=', '小牛')
                    ->else()
                    ->crossJoin(['t' => 'hello'], ['name as nikename', 'tt' => 'value'], 'name', '=', '仔')
                    ->fi()
                    ->findAll(true)
            )
        );
    }

    public function testNaturalJsonFlow(): void
    {
        $condition = false;
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test`.*,`t`.`name` AS `nikename`,`t`.`value` AS `tt` FROM `test` NATURAL JOIN `hello` `t` ON `t`.`name` = '仔'",
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
                    ->table('test')
                    ->if($condition)
                    ->naturalJoin(['t' => 'hello'], ['name as nikename', 'tt' => 'value'], 'name', '=', '小牛')
                    ->else()
                    ->naturalJoin(['t' => 'hello'], ['name as nikename', 'tt' => 'value'], 'name', '=', '仔')
                    ->fi()
                    ->findAll(true)
            )
        );
    }

    public function testNaturalJsonFlow2(): void
    {
        $condition = true;
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test`.*,`t`.`name` AS `nikename`,`t`.`value` AS `tt` FROM `test` NATURAL JOIN `hello` `t` ON `t`.`name` = '小牛'",
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
                    ->table('test')
                    ->if($condition)
                    ->naturalJoin(['t' => 'hello'], ['name as nikename', 'tt' => 'value'], 'name', '=', '小牛')
                    ->else()
                    ->naturalJoin(['t' => 'hello'], ['name as nikename', 'tt' => 'value'], 'name', '=', '仔')
                    ->fi()
                    ->findAll(true)
            )
        );
    }

    public function testInnerJsonAndUnionWillThrowException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
           'JOIN queries cannot be used while using UNION queries.'
        );

        $connect = $this->createDatabaseConnectMock();
        $union = 'SELECT id,value FROM test2';

        $connect
            ->table('test', 'tid as id,tname as value')
            ->union($union)
            ->innerJoin(['t' => 'hello'], ['name as nikename', 'tt' => 'value'], 'name', '=', '小牛')
            ->findAll(true);
    }

    public function testInnerJoinWithTableIsSelect(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test`.*,`b`.`name` AS `nikename`,`b`.`value` AS `tt` FROM `test` INNER JOIN (SELECT `b`.* FROM `foo` `b`) b ON `b`.`name` = '小牛'",
                [],
                false,
                null,
                null,
                []
            ]
            eot;

        $joinTable = $connect->table('foo as b');

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->table('test')
                    ->innerJoin($joinTable, ['name as nikename', 'tt' => 'value'], 'name', '=', '小牛')
                    ->findAll(true)
            )
        );
    }

    public function testInnerJoinWithTableIsCondition(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test`.*,`b`.`name` AS `nikename`,`b`.`value` AS `tt` FROM `test` INNER JOIN (SELECT `b`.* FROM `foo` `b`) b ON `b`.`name` = '小牛'",
                [],
                false,
                null,
                null,
                []
            ]
            eot;

        $joinTable = $connect
            ->table('foo as b')
            ->databaseCondition();

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->table('test')
                    ->innerJoin($joinTable, ['name as nikename', 'tt' => 'value'], 'name', '=', '小牛')
                    ->findAll(true)
            )
        );
    }

    public function testInnerJoinWithTableIsClosure(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test`.*,`b`.`name` AS `nikename`,`b`.`value` AS `tt` FROM `test` INNER JOIN (SELECT `b`.* FROM `foo` `b`) b ON `b`.`name` = '小牛'",
                [],
                false,
                null,
                null,
                []
            ]
            eot;

        $joinTable = $connect
            ->table('foo as b')
            ->databaseCondition();

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->table('test')
                    ->innerJoin(function ($select) {
                        $select->table('foo as b');
                    }, ['name as nikename', 'tt' => 'value'], 'name', '=', '小牛')
                    ->findAll(true)
            )
        );
    }

    public function testInnerJoinWithTableIsArrayCondition(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test`.*,`foo`.`name` AS `nikename`,`foo`.`value` AS `tt` FROM `test` INNER JOIN (SELECT `b`.* FROM `foo` `b`) foo ON `foo`.`name` = '小牛'",
                [],
                false,
                null,
                null,
                []
            ]
            eot;

        $joinTable = $connect
            ->table('foo as b')
            ->databaseCondition();

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->table('test')
                    ->innerJoin(['foo' => $joinTable], ['name as nikename', 'tt' => 'value'], 'name', '=', '小牛')
                    ->findAll(true)
            )
        );
    }

    public function testInnerJoinWithTableIsArrayAndTheAliasKeyMustBeString(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
           'Alias must be string,but integer given.'
        );

        $connect = $this->createDatabaseConnectMock();

        $joinTable = $connect
            ->table('foo as b')
            ->databaseCondition();

        $connect
            ->table('test')
            ->innerJoin([$joinTable], ['name as nikename', 'tt' => 'value'], 'name', '=', '小牛')
            ->findAll(true);
    }

    public function testInnerJsonWithTableNameIsExpression(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test`.*,`a`.`name` AS `nikename`,`a`.`value` AS `tt` FROM `test` INNER JOIN (SELECT * FROM foo) a ON `a`.`name` = `test`.`name`",
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
                    ->table('test')
                    ->innerJoin('(SELECT * FROM foo)', ['name as nikename', 'tt' => 'value'], 'name', '=', '{[test.name]}')
                    ->findAll(true)
            )
        );
    }

    public function testInnerJsonWithTableNameIsExpressionWithAsCustomAlias(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test`.*,`foo`.`name` AS `nikename`,`foo`.`value` AS `tt` FROM `test` INNER JOIN (SELECT * FROM foo) foo ON `foo`.`name` = `test`.`name`",
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
                    ->table('test')
                    ->innerJoin('(SELECT * FROM foo) as foo', ['name as nikename', 'tt' => 'value'], 'name', '=', '{[test.name]}')
                    ->findAll(true)
            )
        );
    }
}
