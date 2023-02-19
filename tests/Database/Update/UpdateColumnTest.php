<?php

declare(strict_types=1);

namespace Tests\Database\Update;

use Leevel\Database\Condition;
use Tests\Database\DatabaseTestCase as TestCase;

/**
 * @api(
 *     zh-CN:title="更新字段.updateColumn",
 *     path="database/update/updatecolumn",
 *     zh-CN:description="",
 * )
 */
final class UpdateColumnTest extends TestCase
{
    /**
     * @api(
     *     zh-CN:title="updateColumn 基本用法",
     *     zh-CN:description="更新成功后，返回影响行数，`updateColumn` 实际上调用的是 `update` 方法。",
     *     zh-CN:note="",
     * )
     */
    public function testBaseUse(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "UPDATE `test_query` SET `test_query`.`name` = :named_param_name WHERE `test_query`.`id` = :test_query_id",
                {
                    "named_param_name": [
                        "小小小鸟，怎么也飞不高。"
                    ],
                    "test_query_id": [
                        503
                    ]
                },
                false
            ]
            eot;

        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect->table('test_query')
                    ->where('id', 503)
                    ->updateColumn('name', '小小小鸟，怎么也飞不高。'),
                $connect
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="updateColumn 支持表达式",
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

        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect->table('test_query')
                    ->where('id', 503)
                    ->updateColumn('name', Condition::raw('concat([value],[name])')),
                $connect
            )
        );
    }
}
