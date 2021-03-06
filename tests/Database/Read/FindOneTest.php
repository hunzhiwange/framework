<?php

declare(strict_types=1);

namespace Tests\Database\Read;

use Tests\Database\DatabaseTestCase as TestCase;

/**
 * @api(
 *     zh-CN:title="查询单条数据.findOne",
 *     path="database/read/findone",
 *     zh-CN:description="",
 * )
 */
class FindOneTest extends TestCase
{
    /**
     * @api(
     *     zh-CN:title="findOne 查询单条数据",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testBaseUse(): void
    {
        $connect = $this->createDatabaseConnectMock();
        $sql = <<<'eot'
            [
                "SELECT `test`.* FROM `test` LIMIT 1",
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
                    ->findOne()
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="one.find 查询单条数据",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testOneFind(): void
    {
        $connect = $this->createDatabaseConnectMock();
        $sql = <<<'eot'
            [
                "SELECT `test`.* FROM `test` LIMIT 1",
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
                    ->one()
                    ->find()
            )
        );
    }

    public function testOneFlow(): void
    {
        $condition = false;
        $connect = $this->createDatabaseConnectMock();
        $sql = <<<'eot'
            [
                "SELECT `test`.* FROM `test`",
                [],
                false
            ]
            eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->table('test')
                    ->sql()
                    ->if($condition)
                    ->one()
                    ->else()
                    ->all()
                    ->fi()
                    ->find()
            )
        );
    }

    public function testOneFlow2(): void
    {
        $condition = true;
        $connect = $this->createDatabaseConnectMock();
        $sql = <<<'eot'
            [
                "SELECT `test`.* FROM `test` LIMIT 1",
                [],
                false
            ]
            eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->table('test')
                    ->sql()
                    ->if($condition)
                    ->one()
                    ->else()
                    ->all()
                    ->fi()
                    ->find()
            )
        );
    }
}
