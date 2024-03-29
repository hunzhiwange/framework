<?php

declare(strict_types=1);

namespace Tests\Database\Query;

use Leevel\Database\Condition;
use Leevel\Kernel\Utils\Api;
use Tests\Database\DatabaseTestCase as TestCase;

#[Api([
    'title' => 'Query lang.columns',
    'zh-CN:title' => '查询语言.columns',
    'path' => 'database/query/columns',
])]
final class ColumnsTest extends TestCase
{
    #[Api([
        'zh-CN:title' => 'Columns 添加字段',
        'zh-CN:description' => <<<'EOT'
字段条件用法和 table 中的字段用法一致，详情可以查看《查询语言.table》。
EOT,
    ])]
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

        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect
                    ->table('test_query')
                    ->columns('id')
                    ->columns('name,value')
                    ->findAll(),
                $connect
            )
        );
    }

    #[Api([
        'zh-CN:title' => 'SetColumns 设置字段',
        'zh-CN:description' => <<<'EOT'
清空原有字段，然后添加新的字段。
EOT,
    ])]
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

        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect
                    ->table('test_query')
                    ->columns('id')
                    ->columns('name,value')
                    ->setColumns('remark')
                    ->findAll(),
                $connect
            )
        );
    }

    #[Api([
        'zh-CN:title' => 'field 设置字段(SetColumns的别名)',
        'zh-CN:description' => <<<'EOT'
清空原有字段，然后添加新的字段。
EOT,
    ])]
    public function testField(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.`remark` FROM `test_query`",
                [],
                false
            ]
            eot;

        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect
                    ->table('test_query')
                    ->columns('id')
                    ->columns('name,value')
                    ->field('remark')
                    ->findAll(),
                $connect
            )
        );
    }

    #[Api([
        'zh-CN:title' => 'Columns 字段支持表达式',
    ])]
    public function testColumnsExpressionForSelectString(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT 'foo'",
                [],
                false
            ]
            eot;

        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect
                    ->columns(Condition::raw("'foo'"))
                    ->findAll(),
                $connect
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

        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect
                    ->table('test_query')
                    ->if($condition)
                    ->columns('id')
                    ->else()
                    ->columns('name,value')
                    ->fi()
                    ->findAll(),
                $connect
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

        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect
                    ->table('test_query')
                    ->if($condition)
                    ->columns('id')
                    ->else()
                    ->columns('name,value')
                    ->fi()
                    ->findAll(),
                $connect
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

        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect
                    ->table('test_query')
                    ->setColumns('foo')
                    ->if($condition)
                    ->setColumns('id')
                    ->else()
                    ->setColumns('name,value')
                    ->fi()
                    ->findAll(),
                $connect
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

        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect
                    ->table('test_query')
                    ->setColumns('foo')
                    ->if($condition)
                    ->setColumns('id')
                    ->else()
                    ->setColumns('name,value')
                    ->fi()
                    ->findAll(),
                $connect
            )
        );
    }

    #[Api([
        'zh-CN:title' => 'Columns 字段在连表中的查询',
    ])]
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

        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect
                    ->table('test_query')
                    ->setColumns('test_query.name,test_query.value')
                    ->join('test_query_subsql', 'name,value', 'name', '=', Condition::raw('[test_query.name]'))
                    ->findAll(),
                $connect
            )
        );
    }
}
