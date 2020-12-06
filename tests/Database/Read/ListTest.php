<?php

declare(strict_types=1);

namespace Tests\Database\Read;

use Tests\Database\DatabaseTestCase as TestCase;

/**
 * @api(
 *     zh-CN:title="查询一列数据.list",
 *     path="database/read/list",
 *     zh-CN:description="",
 * )
 */
class ListTest extends TestCase
{
    /**
     * @api(
     *     zh-CN:title="list 查询基础用法",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testBaseUse(): void
    {
        $connect = $this->createDatabaseConnectMock();
        $sql = <<<'eot'
            [
                "SELECT `test`.`name` FROM `test`",
                [],
                false
            ]
            eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->sql()
                    ->table('test')
                    ->list('name')
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="list 查询字段逗号分隔",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testStringByCommaSeparation(): void
    {
        $connect = $this->createDatabaseConnectMock();
        $sql = <<<'eot'
            [
                "SELECT `test`.`name`,`test`.`id` FROM `test`",
                [],
                false
            ]
            eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->sql()
                    ->table('test')
                    ->list('name,id'),
                1
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="list 查询字段多个字符串",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testMoreString(): void
    {
        $connect = $this->createDatabaseConnectMock();
        $sql = <<<'eot'
            [
                "SELECT `test`.`name`,`test`.`id` FROM `test`",
                [],
                false
            ]
            eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->sql()
                    ->table('test')
                    ->list('name', 'id'),
                2
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="list 查询字段数组",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testArray(): void
    {
        $connect = $this->createDatabaseConnectMock();
        $sql = <<<'eot'
            [
                "SELECT `test`.`name`,`test`.`id` FROM `test`",
                [],
                false
            ]
            eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->sql()
                    ->table('test')
                    ->list(['name', 'id']),
                3
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="list 查询字段数组和字符串混合",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testArrayAndString(): void
    {
        $connect = $this->createDatabaseConnectMock();
        $sql = <<<'eot'
            [
                "SELECT `test`.`name`,`test`.`id` FROM `test`",
                [],
                false
            ]
            eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->sql()
                    ->table('test')
                    ->list(['name'], 'id'),
                4
            )
        );
    }
}
