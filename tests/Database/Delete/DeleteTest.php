<?php

declare(strict_types=1);

namespace Tests\Database\Delete;

use Leevel\Database\Condition;
use Tests\Database\DatabaseTestCase as TestCase;

/**
 * @api(
 *     zh-CN:title="删除数据.delete",
 *     path="database/delete/delete",
 *     zh-CN:description="",
 * )
 */
class DeleteTest extends TestCase
{
    /**
     * @api(
     *     zh-CN:title="delete 基本用法",
     *     zh-CN:description="删除成功后，返回影响行数。",
     *     zh-CN:note="",
     * )
     */
    public function testBaseUse(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "DELETE FROM `test_query` WHERE `test_query`.`id` = :test_query_id ORDER BY `test_query`.`id` DESC LIMIT 1",
                {
                    "test_query_id": [
                        1
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
                    ->where('id', 1)
                    ->limit(1)
                    ->orderBy('id desc')
                    ->delete()
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="delete 不带条件的删除",
     *     zh-CN:description="删除成功后，返回影响行数。",
     *     zh-CN:note="",
     * )
     */
    public function testWithoutCondition(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "DELETE FROM `test_query`",
                [],
                false
            ]
            eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->sql()
                    ->table('test_query')
                    ->delete()
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="delete.join 连表删除",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testJoin(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "DELETE t FROM `test_query` `t` INNER JOIN `test_query_subsql` `h` ON `h`.`name` = `t`.`name` WHERE `t`.`id` = :t_id",
                {
                    "t_id": [
                        1
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
                    ->innerJoin(['h' => 'test_query_subsql'], [], 'name', '=', Condition::raw('[t.name]'))
                    ->where('id', 1)
                    ->limit(1)
                    ->orderBy('id desc')
                    ->delete()
            )
        );
    }
}
