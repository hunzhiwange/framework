<?php

declare(strict_types=1);

namespace Tests\Database\Create;

use Leevel\Database\Condition;
use Tests\Database\DatabaseTestCase as TestCase;

/**
 * @api(
 *     zh-CN:title="插入单条数据.insert",
 *     path="database/create/insert",
 *     zh-CN:description="",
 * )
 */
class InsertTest extends TestCase
{
    /**
     * @api(
     *     zh-CN:title="insert 基本用法",
     *     zh-CN:description="写入成功后，返回 `lastInsertId`。",
     *     zh-CN:note="",
     * )
     */
    public function testBaseUse(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "INSERT INTO `test_query` (`test_query`.`name`,`test_query`.`value`) VALUES (:pdonamedparameter_name,:pdonamedparameter_value)",
                {
                    "pdonamedparameter_name": [
                        "小鸭子"
                    ],
                    "pdonamedparameter_value": [
                        "吃饭饭"
                    ]
                },
                false
            ]
            eot;

        $data = ['name' => '小鸭子', 'value' => '吃饭饭'];

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->sql()
                    ->table('test_query')
                    ->insert($data)
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="insert 绑定参数",
     *     zh-CN:description="",
     *     zh-CN:note="位置占位符会自动转为命名占位符，以增强灵活性。",
     * )
     */
    public function testBind(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "INSERT INTO `test_query` (`test_query`.`name`,`test_query`.`value`) VALUES (:pdonamedparameter_name,:pdopositional2namedparameter_0)",
                {
                    "pdonamedparameter_name": [
                        "小鸭子"
                    ],
                    "pdopositional2namedparameter_0": [
                        "吃肉"
                    ]
                },
                false
            ]
            eot;

        $data = ['name' => '小鸭子', 'value' => Condition::raw('?')];

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->sql()
                    ->table('test_query')
                    ->insert($data, ['吃肉'])
            )
        );

        $sql = <<<'eot'
            [
                "INSERT INTO `test_query` (`test_query`.`name`,`test_query`.`value`) VALUES (:pdonamedparameter_name,:value)",
                {
                    "pdonamedparameter_name": [
                        "小鸭子"
                    ],
                    "value": "呱呱呱"
                },
                false
            ]
            eot;

        $data = ['name' => '小鸭子', 'value' => Condition::raw(':value')];

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->sql()
                    ->table('test_query')
                    ->insert($data, ['value' => '呱呱呱']),
                1
            )
        );
    }

    public function testBindCheckBindParameterIsAlreadyExists(): void
    {
        $this->expectException(\PDOException::class);
        $this->expectExceptionMessage(
            'SQLSTATE[HY093]: Invalid parameter number'
        );

        $connect = $this->createDatabaseConnectMock();
        $data = ['value' => Condition::raw('?'), 'name' => Condition::raw(':pdopositional2namedparameter_0')];
        $this->runSql(
            $connect
                ->sql()
                ->table('test_query')
                ->insert($data, ['pdopositional2namedparameter_0' => '小鸭子', '吃肉'])
        );
    }

    public function testBindPdoPositionalParametersNotMatchWithBindData(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'PDO positional parameters not match with bind data.'
        );

        $connect = $this->createDatabaseConnectMock();
        $data = ['name' => Condition::raw('?'), 'value' => Condition::raw('?')];
        $connect
            ->sql()
            ->table('test_query')
            ->insert($data, ['吃肉']);
    }

    /**
     * @api(
     *     zh-CN:title="bind.insert 绑定参数写入数据",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testWithBindFunction(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "INSERT INTO `test_query` (`test_query`.`name`,`test_query`.`value`) VALUES (:pdonamedparameter_name,:pdopositional2namedparameter_0)",
                {
                    "pdonamedparameter_name": [
                        "小鸭子"
                    ],
                    "pdopositional2namedparameter_0": [
                        "吃鱼"
                    ]
                },
                false
            ]
            eot;

        $data = ['name' => '小鸭子', 'value' => Condition::raw('?')];

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->sql()
                    ->table('test_query')
                    ->bind(['吃鱼'])
                    ->insert($data)
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="insert 支持 replace 用法",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testReplace(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "REPLACE INTO `test_query` (`test_query`.`name`,`test_query`.`value`) VALUES (:pdonamedparameter_name,:value)",
                {
                    "pdonamedparameter_name": [
                        "小鸭子"
                    ],
                    "value": "呱呱呱"
                },
                false
            ]
            eot;

        $data = ['name' => '小鸭子', 'value' => Condition::raw(':value')];

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->sql()
                    ->table('test_query')
                    ->insert($data, ['value' => '呱呱呱'], true)
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="insert 支持字段指定表名",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testInsertSupportTable(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "REPLACE INTO `test_query` (`test_query`.`name`,`test_query`.`value`) VALUES (:pdonamedparameter_name,:value)",
                {
                    "pdonamedparameter_name": [
                        "小鸭子"
                    ],
                    "value": "呱呱呱"
                },
                false
            ]
            eot;

        $data = ['name' => '小鸭子', 'test_query.value' => Condition::raw(':value')];

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->sql()
                    ->table('test_query')
                    ->insert($data, ['value' => '呱呱呱'], true)
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="insert 空数据写入示例",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testInsertWithEmptyData(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "INSERT INTO `test_query` () VALUES ()",
                [],
                false
            ]
            eot;

        $data = [];

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->sql()
                    ->table('test_query')
                    ->insert($data)
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="insert.replace 空数据写入示例",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testReplaceWithEmptyData(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "REPLACE INTO `test_query` () VALUES ()",
                [],
                false
            ]
            eot;

        $data = [];

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->sql()
                    ->table('test_query')
                    ->insert($data, [], true)
            )
        );
    }

    protected function getDatabaseTable(): array
    {
        return ['test_query'];
    }
}
