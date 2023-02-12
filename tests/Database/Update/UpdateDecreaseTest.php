<?php

declare(strict_types=1);

namespace Tests\Database\Update;

use Leevel\Database\Condition;
use Tests\Database\DatabaseTestCase as TestCase;

/**
 * @api(
 *     zh-CN:title="更新字段递减.updateDecrease",
 *     path="database/update/updatedecrease",
 *     zh-CN:description="",
 * )
 *
 * @internal
 *
 * @coversNothing
 */
final class UpdateDecreaseTest extends TestCase
{
    /**
     * @api(
     *     zh-CN:title="updateDecrease 基本用法",
     *     zh-CN:description="更新成功后，返回影响行数，`updateDecrease` 实际上调用的是 `updateColumn` 方法。",
     *     zh-CN:note="",
     * )
     */
    public function testBaseUse(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "UPDATE `test_query` SET `test_query`.`num` = `test_query`.`num`-3 WHERE `test_query`.`id` = :test_query_id",
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
                $connect
                    ->table('test_query')
                    ->where('id', 503)
                    ->updateDecrease('num', 3),
                $connect
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="updateDecrease 支持参数绑定",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testBind(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "UPDATE `test_query` SET `test_query`.`num` = `test_query`.`num`-3 WHERE `test_query`.`id` = ?",
                [
                    503
                ],
                false
            ]
            eot;

        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect
                    ->table('test_query')
                    ->where('id', Condition::raw('?'))
                    ->updateDecrease('num', 3, [503]),
                $connect
            )
        );
    }
}
