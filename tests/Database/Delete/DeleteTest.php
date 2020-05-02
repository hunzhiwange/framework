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

namespace Tests\Database\Delete;

use Tests\Database\DatabaseTestCase as TestCase;

/**
 * @api(
 *     zh-CN:title="删除数据.delete",
 *     path="database/delete/delete",
 *     description="",
 * )
 */
class DeleteTest extends TestCase
{
    /**
     * @api(
     *     zh-CN:title="delete 基本用法",
     *     zh-CN:description="删除成功后，返回影响行数。",
     *     note="",
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
                        1,
                        1
                    ]
                }
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
     *     note="",
     * )
     */
    public function testWithoutCondition(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "DELETE FROM `test_query`",
                []
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
     *     note="",
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
                        1,
                        1
                    ]
                }
            ]
            eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->sql()
                    ->table('test_query as t')
                    ->innerJoin(['h' => 'test_query_subsql'], [], 'name', '=', '{[t.name]}')
                    ->where('id', 1)
                    ->limit(1)
                    ->orderBy('id desc')
                    ->delete()
            )
        );
    }
}
