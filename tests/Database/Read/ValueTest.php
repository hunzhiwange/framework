<?php

declare(strict_types=1);

namespace Tests\Database\Read;

use Tests\Database\DatabaseTestCase as TestCase;

/**
 * @api(
 *     zh-CN:title="查询一个字段的值.value",
 *     path="database/read/value",
 *     zh-CN:description="",
 * )
 */
class ValueTest extends TestCase
{
    /**
     * @api(
     *     zh-CN:title="value 查询基础用法",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testBaseUse(): void
    {
        $connect = $this->createDatabaseConnectMock();
        $sql = <<<'eot'
            [
                "SELECT `test`.`id` FROM `test` LIMIT 1",
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
                    ->value('id')
            )
        );
    }
}
