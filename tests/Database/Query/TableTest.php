<?php

declare(strict_types=1);

namespace Tests\Database\Query;

use Leevel\Database\Condition;
use Tests\Database\DatabaseTestCase as TestCase;

/**
 * @api(
 *     title="Query lang.table",
 *     zh-CN:title="查询语言.table",
 *     path="database/query/table",
 *     zh-CN:description="",
 * )
 *
 * @internal
 */
final class TableTest extends TestCase
{
    /**
     * @api(
     *     zh-CN:title="Table 查询数据库表",
     *     zh-CN:description="",
     *     zh-CN:note="",
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

        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect
                    ->table('test_query')
                    ->findAll(),
                $connect
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="Table 查询指定数据库的表",
     *     zh-CN:description="",
     *     zh-CN:note="",
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

        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect
                    ->table('test.test_query')
                    ->findAll(),
                $connect,
                1
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="Table 查询数据库表，表支持别名",
     *     zh-CN:description="",
     *     zh-CN:note="",
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

        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect
                    ->table(['p' => 'test.test_query'])
                    ->findAll(),
                $connect,
                2
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="Table 查询数据库表指定字段",
     *     zh-CN:description="",
     *     zh-CN:note="",
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

        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect
                    ->table('test_query', 'title,body')
                    ->findAll(),
                $connect
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="Table 查询数据库表指定字段，字段支持别名",
     *     zh-CN:description="",
     *     zh-CN:note="",
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

        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect
                    ->table('test.test_query', [
                        't' => 'title', 'name', 'remark,value',
                    ])
                    ->findAll(),
                $connect,
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

        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect
                    ->if($condition)
                    ->table('test_query')
                    ->else()
                    ->table('test_query_subsql')
                    ->fi()
                    ->findAll(),
                $connect
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

        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect
                    ->if($condition)
                    ->table('test_query')
                    ->else()
                    ->table('test_query_subsql')
                    ->fi()
                    ->findAll(),
                $connect
            )
        );
    }

    public function testTableIsInvalid(): void
    {
        $this->expectException(\TypeError::class);

        $connect = $this->createDatabaseConnectMock();

        $connect
            ->table(new \stdClass())
            ->findAll()
        ;
    }

    /**
     * @api(
     *     zh-CN:title="Table 查询数据库表支持子查询",
     *     zh-CN:description="",
     *     zh-CN:note="",
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

        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect
                    ->table($subSql.' as a')
                    ->findAll(),
                $connect
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="Table 查询数据库表支持子查询,子查询可以为数据库查询对象",
     *     zh-CN:description="",
     *     zh-CN:note="",
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

        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect
                    ->table(['bb' => $subSql])
                    ->findAll(),
                $connect
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="Table 查询数据库表支持子查询,子查询可以为数据库条件对象",
     *     zh-CN:description="",
     *     zh-CN:note="",
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

        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect
                    ->table(['bb' => $subSql])
                    ->findAll(),
                $connect
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="Table 查询数据库表支持子查询,子查询可以为闭包",
     *     zh-CN:description="",
     *     zh-CN:note="",
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

        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect
                    ->table(['b' => function ($select): void {
                        $select->table('test_query');
                    }])
                    ->findAll(),
                $connect
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="Table 查询数据库表支持子查询,子查询可以为闭包,未指定别名默认为自身",
     *     zh-CN:description="",
     *     zh-CN:note="",
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

        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect
                    ->table(function ($select): void {
                        $select->table('guest_book');
                    })
                    ->findAll(),
                $connect
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="Table 查询数据库表支持子查询,子查询可以为闭包,还可以进行 join 查询",
     *     zh-CN:description="",
     *     zh-CN:note="",
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

        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect
                    ->table(function ($select): void {
                        $select->table('test_query');
                    }, 'remark')
                    ->join('test_query_subsql', 'name,value', 'name', '=', Condition::raw('[test_query.name]'))
                    ->findAll(),
                $connect
            )
        );
    }
}
