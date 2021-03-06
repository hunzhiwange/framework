<?php

declare(strict_types=1);

namespace Tests\Database\Update;

use Leevel\Database\Condition;
use Tests\Database\DatabaseTestCase as TestCase;

/**
 * @api(
 *     zh-CN:title="更新数据.update",
 *     path="database/update/update",
 *     zh-CN:description="",
 * )
 */
class UpdateTest extends TestCase
{
    /**
     * @api(
     *     zh-CN:title="update 基本用法",
     *     zh-CN:description="更新成功后，返回影响行数。",
     *     zh-CN:note="",
     * )
     */
    public function testBaseUse(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "UPDATE `test_query` SET `test_query`.`name` = :pdonamedparameter_name WHERE `test_query`.`id` = :test_query_id",
                {
                    "pdonamedparameter_name": [
                        "小猪"
                    ],
                    "test_query_id": [
                        503
                    ]
                },
                false
            ]
            eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->sql()
                    ->table('test_query')
                    ->where('id', 503)
                    ->update(['name' => '小猪'])
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="update 更新指定条数",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testWithLimit(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "UPDATE `test_query` SET `test_query`.`name` = :pdonamedparameter_name WHERE `test_query`.`id` = :test_query_id LIMIT 5",
                {
                    "pdonamedparameter_name": [
                        "小猪"
                    ],
                    "test_query_id": [
                        503
                    ]
                },
                false
            ]
            eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->sql()
                    ->table('test_query')
                    ->where('id', 503)
                    ->limit(5)
                    ->update(['name' => '小猪'])
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="update 更新排序",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testWithOrderBy(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "UPDATE `test_query` SET `test_query`.`name` = :pdonamedparameter_name WHERE `test_query`.`id` = :test_query_id ORDER BY `test_query`.`id` DESC",
                {
                    "pdonamedparameter_name": [
                        "小猪"
                    ],
                    "test_query_id": [
                        503
                    ]
                },
                false
            ]
            eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->sql()
                    ->table('test_query')
                    ->where('id', 503)
                    ->orderBy('id desc')
                    ->update(['name' => '小猪'])
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="update 更新排序和指定条数",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testWithOrderAndLimit(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "UPDATE `test_query` SET `test_query`.`name` = :pdonamedparameter_name WHERE `test_query`.`id` = :test_query_id ORDER BY `test_query`.`id` DESC LIMIT 2",
                {
                    "pdonamedparameter_name": [
                        "小猪"
                    ],
                    "test_query_id": [
                        503
                    ]
                },
                false
            ]
            eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->sql()
                    ->table('test_query')
                    ->where('id', 503)
                    ->orderBy('id desc')
                    ->limit(2)
                    ->update(['name' => '小猪'])
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="update 连表更新",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testWithJoin(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "UPDATE `test_query` `t` INNER JOIN `test_query_subsql` `h` ON `t`.`id` = `h`.`value` SET `t`.`name` = :pdonamedparameter_name WHERE `t`.`id` = :t_id",
                {
                    "pdonamedparameter_name": [
                        "小猪"
                    ],
                    "t_id": [
                        503
                    ]
                },
                false
            ]
            eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->sql()
                    ->table('test_query as t')
                    ->join('test_query_subsql as h', '', 't.id', '=', Condition::raw('[value]'))
                    ->where('id', 503)
                    ->update(['name' => '小猪'])
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="update 更新参数绑定",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testBind(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "UPDATE `test_query` SET `test_query`.`name` = :hello,`test_query`.`value` = :pdopositional2namedparameter_0 WHERE `test_query`.`id` = :test_query_id",
                {
                    "pdopositional2namedparameter_0": [
                        "小牛逼"
                    ],
                    "test_query_id": [
                        503
                    ],
                    "hello": "hello world!"
                },
                false
            ]
            eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->sql()
                    ->table('test_query')
                    ->where('id', 503)
                    ->bind(['小牛逼'])
                    ->update(
                        [
                            'name'  => Condition::raw(':hello'),
                            'value' => Condition::raw('?'),
                        ],
                        [
                            'hello' => 'hello world!',
                        ]
                    )
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="update 更新支持表达式",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testExpression(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "UPDATE `test_query` SET `test_query`.`name` = concat(`test_query`.`value`,`test_query`.`name`) WHERE `test_query`.`id` = :test_query_id",
                {
                    "test_query_id": [
                        503
                    ]
                },
                false
            ]
            eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->sql()
                    ->table('test_query')
                    ->where('id', 503)
                    ->update([
                        'name' => Condition::raw('concat([value],[name])'),
                    ])
            )
        );
    }

    public function testUpdateWithEmptyDataException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Data for update can not be empty.'
        );

        $connect = $this->createDatabaseConnectMock();

        $connect
            ->sql()
            ->table('test_query')
            ->where('id', 503)
            ->update([]);
    }
}
