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

namespace Tests\Database\Update;

use Tests\Database\DatabaseTestCase as TestCase;

/**
 * update test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.06.24
 *
 * @version 1.0
 *
 * @api(
 *     zh-CN:title="更新数据.update",
 *     path="database/update/update",
 *     description="",
 * )
 */
class UpdateTest extends TestCase
{
    /**
     * @api(
     *     zh-CN:title="update 基本用法",
     *     zh-CN:description="更新成功后，返回影响行数。",
     *     note="",
     * )
     */
    public function testBaseUse(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "UPDATE `test_query` SET `test_query`.`name` = :name WHERE `test_query`.`id` = 503",
                {
                    "name": [
                        "小猪",
                        2
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
                    ->where('id', 503)
                    ->update(['name' => '小猪'])
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="update 更新指定条数",
     *     zh-CN:description="",
     *     note="",
     * )
     */
    public function testWithLimit(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "UPDATE `test_query` SET `test_query`.`name` = :name WHERE `test_query`.`id` = 503 LIMIT 5",
                {
                    "name": [
                        "小猪",
                        2
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
     *     note="",
     * )
     */
    public function testWithOrderBy(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "UPDATE `test_query` SET `test_query`.`name` = :name WHERE `test_query`.`id` = 503 ORDER BY `test_query`.`id` DESC",
                {
                    "name": [
                        "小猪",
                        2
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
     *     note="",
     * )
     */
    public function testWithOrderAndLimit(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "UPDATE `test_query` SET `test_query`.`name` = :name WHERE `test_query`.`id` = 503 ORDER BY `test_query`.`id` DESC LIMIT 2",
                {
                    "name": [
                        "小猪",
                        2
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
     *     note="",
     * )
     */
    public function testWithJoin(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "UPDATE `test_query` `t` INNER JOIN `test_query_subsql` `h` ON `t`.`id` = `h`.`value` SET `t`.`name` = :name WHERE `t`.`id` = 503",
                {
                    "name": [
                        "小猪",
                        2
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
                    ->join('test_query_subsql as h', '', 't.id', '=', '{[value]}')
                    ->where('id', 503)
                    ->update(['name' => '小猪'])
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="update 更新参数绑定",
     *     zh-CN:description="",
     *     note="",
     * )
     */
    public function testBind(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "UPDATE `test_query` SET `test_query`.`name` = :hello,`test_query`.`value` = :questionmark_0 WHERE `test_query`.`id` = 503",
                {
                    "questionmark_0": [
                        "小牛逼",
                        2
                    ],
                    "hello": "hello world!"
                }
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
                            'name'  => '[:hello]',
                            'value' => '[?]',
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
     *     note="",
     * )
     */
    public function testExpression(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "UPDATE `test_query` SET `test_query`.`name` = concat(`test_query`.`value`,`test_query`.`name`) WHERE `test_query`.`id` = 503",
                []
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
                        'name' => '{concat([value],[name])}',
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
